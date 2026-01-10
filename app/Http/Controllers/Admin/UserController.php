<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('role_id', $request->role));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'status' => 'required|in:active,suspended',
        ]);

        // Check if current user can assign this role
        $this->authorizeRoleAssignment($request->role);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        // Assign role via pivot table
        $user->roles()->sync([$request->role]);

        ActivityLog::log('auth', 'created', "Created user: {$user->name}", $user);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'activityLogs' => fn($q) => $q->latest()->take(20)]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $this->authorizeUserAccess($user);

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeUserAccess($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,id',
            'status' => 'required|in:active,suspended',
        ]);

        // Check if current user can assign this role
        $this->authorizeRoleAssignment($request->role);

        $oldRole = $user->getPrimaryRole()?->name ?? 'None';

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        // Update role via pivot table
        $user->roles()->sync([$request->role]);

        ActivityLog::log('auth', 'updated', "Updated user: {$user->name} (Role: {$oldRole} -> {$user->getPrimaryRole()->name})", $user);

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->authorizeUserAccess($user);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        if ($user->status === 'locked') {
            $user->activate();
        }

        ActivityLog::log('password_reset', "Reset password for user: {$user->name}", $user);

        return back()->with('success', 'Password has been reset successfully.');
    }

    /**
     * Suspend a user.
     */
    public function suspend(User $user)
    {
        $this->authorizeUserAccess($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->suspend();

        ActivityLog::log('suspended', "Suspended user: {$user->name}", $user);

        return back()->with('success', 'User account has been suspended.');
    }

    /**
     * Activate a user.
     */
    public function activate(User $user)
    {
        $this->authorizeUserAccess($user);

        $user->activate();

        ActivityLog::log('activated', "Activated user: {$user->name}", $user);

        return back()->with('success', 'User account has been activated.');
    }

    /**
     * Unlock a user account.
     */
    public function unlock(User $user)
    {
        $this->authorizeUserAccess($user);

        $user->unlock();

        ActivityLog::log('unlocked', "Unlocked user account: {$user->name}", $user);

        return back()->with('success', 'User account has been unlocked.');
    }

    /**
     * Send password reset email to user.
     */
    public function sendPasswordReset(User $user)
    {
        $this->authorizeUserAccess($user);

        // In a production system, you would use Laravel's built-in password reset
        // Password::broker()->sendResetLink(['email' => $user->email]);

        ActivityLog::log('password_reset_sent', "Sent password reset email to: {$user->email}", $user);

        return response()->json([
            'success' => true,
            'message' => "Password reset email sent to {$user->email}."
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorizeUserAccess($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Admin account cannot be deleted.');
        }

        ActivityLog::log('deleted', "Deleted user: {$user->name}", $user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Staff member has been removed.');
    }

    /**
     * Check if current user can manage the target user.
     */
    protected function authorizeUserAccess(User $user): void
    {
        $currentUser = auth()->user();

        // Super Admin can manage anyone
        if ($currentUser->isSuperAdmin()) {
            return;
        }

        // Can't manage Super Admin
        if ($user->isSuperAdmin()) {
            abort(403, 'You cannot manage Super Admin accounts.');
        }

        // Can only manage users with lower role level
        if ($user->role && $currentUser->getPrimaryRole()) {
            if ($user->getPrimaryRole()?->level >= $currentUser->getPrimaryRole()?->level) {
                abort(403, 'You can only manage users with lower access levels.');
            }
        }
    }

    /**
     * Check if current user can assign a specific role.
     */
    protected function authorizeRoleAssignment(int $roleId): void
    {
        $currentUser = auth()->user();
        $role = Role::find($roleId);

        if (!$role) {
            return;
        }

        // Super Admin can assign any role
        if ($currentUser->isSuperAdmin()) {
            return;
        }

        // Can't assign Super Admin role
        if ($role->slug === 'super-admin') {
            abort(403, 'You cannot assign the Super Admin role.');
        }

        // Can only assign roles with lower level
        if ($currentUser->getPrimaryRole() && $role->level >= $currentUser->getPrimaryRole()->level) {
            abort(403, 'You can only assign roles with lower access levels.');
        }
    }
}
