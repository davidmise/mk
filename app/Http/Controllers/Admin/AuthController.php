<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (auth()->check() && auth()->user()->roles()->exists()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting
        $throttleKey = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            RateLimiter::hit($throttleKey, 300);
            ActivityLog::logFailedLogin($request->email);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $minutes = $user->locked_until->diffInMinutes(now());
            throw ValidationException::withMessages([
                'email' => "Your account is locked. Please try again in {$minutes} minutes.",
            ]);
        }

        // Check if account is suspended
        if ($user->isSuspended()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ]);
        }

        // Check if user has admin role
        if (!$user->roles()->exists()) {
            throw ValidationException::withMessages([
                'email' => 'You do not have permission to access the admin area.',
            ]);
        }

        // Attempt login
        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);
            $user->recordFailedLogin();
            ActivityLog::logFailedLogin($request->email);

            // Check if account should be locked
            if ($user->failed_login_attempts >= User::MAX_LOGIN_ATTEMPTS) {
                throw ValidationException::withMessages([
                    'email' => 'Your account has been locked due to too many failed login attempts.',
                ]);
            }

            $attemptsLeft = User::MAX_LOGIN_ATTEMPTS - $user->failed_login_attempts;
            throw ValidationException::withMessages([
                'email' => "Invalid credentials. {$attemptsLeft} attempts remaining.",
            ]);
        }

        // Login successful
        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $user->recordSuccessfulLogin();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        if (auth()->check()) {
            ActivityLog::logLogout(auth()->user());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->roles()->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email is not associated with an admin account.',
            ]);
        }

        // Generate password reset token and send email
        // In a real application, you would use Laravel's built-in password reset
        $token = \Str::random(64);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // TODO: Send email with reset link
        // Mail::to($user)->send(new ResetPasswordMail($token));

        return back()->with('success', 'If an account exists with this email, you will receive a password reset link.');
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('admin.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid password reset token.',
            ]);
        }

        // Check token expiry (1 hour)
        if (now()->diffInHours($resetRecord->created_at) > 1) {
            throw ValidationException::withMessages([
                'email' => 'Password reset token has expired.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        if ($user->status === 'locked') {
            $user->activate();
        }

        // Delete the reset token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        ActivityLog::log('auth', 'password_reset', 'Password was reset', $user);

        return redirect()->route('admin.login')->with('success', 'Your password has been reset. Please login with your new password.');
    }

    /**
     * Show profile page.
     */
    public function showProfile()
    {
        return view('admin.auth.profile', ['user' => auth()->user()]);
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        ActivityLog::log('profile', 'updated', 'Profile updated', $user);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('auth', 'password_changed', 'Password was changed', $user);

        return back()->with('success', 'Password updated successfully.');
    }
}
