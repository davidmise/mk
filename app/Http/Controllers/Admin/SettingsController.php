<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\HeroSlide;
use App\Models\NavigationItem;
use App\Models\SocialLink;
use App\Models\SystemPreference;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Show general settings.
     */
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:50',
            'site_phone_secondary' => 'nullable|string|max:50',
            'site_address' => 'nullable|string|max:500',
            'site_city' => 'nullable|string|max:100',
            'site_country' => 'nullable|string|max:100',
            'google_maps_embed' => 'nullable|string',
            'footer_text' => 'nullable|string|max:500',
            'copyright_text' => 'nullable|string|max:255',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        Cache::forget('site_settings');
        ActivityLog::log('updated', 'Updated general settings');

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Show branding settings.
     */
    public function branding()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.branding', compact('settings'));
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|image|max:2048',
            'admin_logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|mimes:ico,png|max:512',
            'footer_logo' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
        ]);

        $imageFields = ['site_logo', 'admin_logo', 'favicon', 'footer_logo', 'og_image'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file
                $old = SiteSetting::where('key', $field)->first();
                if ($old && $old->value) {
                    Storage::disk('public')->delete($old->value);
                }

                $path = $request->file($field)->store('branding', 'public');

                SiteSetting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $path, 'type' => 'image']
                );
            }
        }

        Cache::forget('site_settings');
        ActivityLog::log('updated', 'Updated branding settings');

        return back()->with('success', 'Branding updated successfully.');
    }

    /**
     * Show SEO settings.
     */
    public function seo()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.seo', compact('settings'));
    }

    /**
     * Update SEO settings.
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'google_analytics' => 'nullable|string|max:50',
            'facebook_pixel' => 'nullable|string|max:50',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        Cache::forget('site_settings');
        ActivityLog::log('updated', 'Updated SEO settings');

        return back()->with('success', 'SEO settings updated successfully.');
    }

    /**
     * Show system preferences.
     */
    public function preferences()
    {
        $preferences = SystemPreference::all()->groupBy('group');
        return view('admin.settings.preferences', compact('preferences'));
    }

    /**
     * Update system preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:10',
            'min_stay_nights' => 'required|integer|min:1',
            'max_stay_nights' => 'required|integer|min:1',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'cancellation_window_hours' => 'required|integer|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
        ]);

        $groups = [
            'general' => ['currency', 'currency_symbol', 'timezone', 'date_format', 'time_format', 'language'],
            'booking' => ['min_stay_nights', 'max_stay_nights', 'check_in_time', 'check_out_time', 'cancellation_window_hours', 'allow_same_day_booking', 'allow_overbooking'],
            'payment' => ['tax_rate', 'service_charge_rate', 'deposit_percentage', 'require_deposit'],
        ];

        foreach ($request->except('_token') as $key => $value) {
            $group = 'general';
            foreach ($groups as $grp => $keys) {
                if (in_array($key, $keys)) {
                    $group = $grp;
                    break;
                }
            }

            $type = is_numeric($value) ? (strpos($value, '.') !== false ? 'decimal' : 'integer') : 'string';
            if (in_array($value, ['true', 'false', '1', '0'])) {
                $type = 'boolean';
            }

            SystemPreference::set($key, $value, $type, $group);
        }

        ActivityLog::log('updated', 'Updated system preferences');

        return back()->with('success', 'Preferences updated successfully.');
    }

    /**
     * Manage hero slides.
     */
    public function heroSlides()
    {
        $slides = HeroSlide::ordered()->get();
        return view('admin.settings.hero-slides', compact('slides'));
    }

    /**
     * Store a hero slide.
     */
    public function storeHeroSlide(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'subtext' => 'nullable|string|max:500',
            'image' => 'required|image|max:5120',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['image'] = $request->file('image')->store('hero-slides', 'public');
        $data['order'] = HeroSlide::max('order') + 1;
        $data['is_active'] = $request->boolean('is_active', true);

        $slide = HeroSlide::create($data);

        ActivityLog::log('created', "Created hero slide: {$slide->heading}", $slide);

        return back()->with('success', 'Hero slide added successfully.');
    }

    /**
     * Update a hero slide.
     */
    public function updateHeroSlide(Request $request, HeroSlide $slide)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'subtext' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slide->image);
            $data['image'] = $request->file('image')->store('hero-slides', 'public');
        }

        $slide->update($data);

        ActivityLog::log('updated', "Updated hero slide: {$slide->heading}", $slide);

        return back()->with('success', 'Hero slide updated successfully.');
    }

    /**
     * Delete a hero slide.
     */
    public function deleteHeroSlide(HeroSlide $slide)
    {
        Storage::disk('public')->delete($slide->image);

        ActivityLog::log('deleted', "Deleted hero slide: {$slide->heading}", $slide);

        $slide->delete();

        return back()->with('success', 'Hero slide deleted successfully.');
    }

    /**
     * Manage social links.
     */
    public function socialLinks()
    {
        $links = SocialLink::ordered()->get();
        return view('admin.settings.social-links', compact('links'));
    }

    /**
     * Store a social link.
     */
    public function storeSocialLink(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['order'] = SocialLink::max('order') + 1;
        $data['is_active'] = $request->boolean('is_active', true);

        SocialLink::create($data);

        return back()->with('success', 'Social link added successfully.');
    }

    /**
     * Update a social link.
     */
    public function updateSocialLink(Request $request, SocialLink $link)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'icon' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $link->update([
            'platform' => $request->platform,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Social link updated successfully.');
    }

    /**
     * Delete a social link.
     */
    public function deleteSocialLink(SocialLink $link)
    {
        $link->delete();
        return back()->with('success', 'Social link deleted successfully.');
    }

    /**
     * System maintenance.
     */
    public function maintenance()
    {
        $cacheSize = $this->getDirectorySize(storage_path('framework/cache'));
        $logSize = $this->getDirectorySize(storage_path('logs'));

        return view('admin.settings.maintenance', compact('cacheSize', 'logSize'));
    }

    /**
     * Update hero slider settings.
     */
    public function updateHeroSlidesSettings(Request $request)
    {
        $request->validate([
            'slider_interval' => 'nullable|integer|min:1|max:30',
            'slider_effect' => 'nullable|string|max:50',
            'slider_autoplay' => 'nullable|boolean',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        Cache::forget('site_settings');
        ActivityLog::log('updated', 'Updated hero slider settings');

        return back()->with('success', 'Slider settings updated successfully.');
    }

    /**
     * Update all social links at once.
     */
    public function updateAllSocialLinks(Request $request)
    {
        $request->validate([
            'links' => 'nullable|array',
            'links.*.platform' => 'required|string|max:50',
            'links.*.url' => 'required|url|max:255',
            'links.*.is_active' => 'boolean',
        ]);

        // Update existing links
        if ($request->has('links')) {
            foreach ($request->links as $id => $linkData) {
                $link = SocialLink::find($id);
                if ($link) {
                    $link->update([
                        'platform' => $linkData['platform'],
                        'url' => $linkData['url'],
                        'is_active' => isset($linkData['is_active']) && $linkData['is_active'],
                    ]);
                }
            }
        }

        ActivityLog::log('updated', 'Updated social links');

        return back()->with('success', 'Social links updated successfully.');
    }

    /**
     * Clear cache.
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'application':
                Artisan::call('cache:clear');
                break;
            case 'views':
                Artisan::call('view:clear');
                break;
            case 'routes':
                Artisan::call('route:clear');
                break;
            case 'config':
                Artisan::call('config:clear');
                break;
            default:
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                Artisan::call('config:clear');
        }

        ActivityLog::log('maintenance', "Cleared {$type} cache");

        return back()->with('success', ucfirst($type) . ' cache cleared successfully.');
    }

    /**
     * Clear logs.
     */
    public function clearLogs()
    {
        $files = glob(storage_path('logs/*.log'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        ActivityLog::log('maintenance', 'Cleared application logs');

        return back()->with('success', 'Logs cleared successfully.');
    }

    /**
     * Get directory size.
     */
    protected function getDirectorySize(string $path): string
    {
        $size = 0;
        foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $file) {
            $size += is_file($file) ? filesize($file) : $this->getDirectorySizeRecursive($file);
        }

        return $this->formatBytes($size);
    }

    /**
     * Get directory size recursively.
     */
    protected function getDirectorySizeRecursive(string $path): int
    {
        $size = 0;
        foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $file) {
            $size += is_file($file) ? filesize($file) : $this->getDirectorySizeRecursive($file);
        }
        return $size;
    }

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
