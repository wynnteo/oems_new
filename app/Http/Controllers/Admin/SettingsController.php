<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Display settings grouped by category.
     */
    public function index()
    {
        // Check if settings exist, if not create default ones
        $this->ensureDefaultSettings();
        
        $settingsGroups = [
            'general' => Setting::where('group', 'general')->orderBy('sort_order')->get(),
            'email' => Setting::where('group', 'email')->orderBy('sort_order')->get(),
            'certificate' => Setting::where('group', 'certificate')->orderBy('sort_order')->get(),
            'notification' => Setting::where('group', 'notification')->orderBy('sort_order')->get(),
            'system' => Setting::where('group', 'system')->orderBy('sort_order')->get(),
        ];

        return view('admin.settings.index', compact('settingsGroups'));
    }

    /**
     * Ensure default settings exist.
     */
    private function ensureDefaultSettings()
    {
        $settingsCount = Setting::count();
        
        if ($settingsCount === 0) {
            $this->seedAllDefaultSettings();
        }
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $settings = Setting::all();
        $errors = [];

        foreach ($settings as $setting) {
            $value = $request->input($setting->key);
            
            // Skip if no value provided and not required
            if (is_null($value) && !$setting->is_required) {
                continue;
            }

            // Validate setting value
            if ($setting->validation_rules) {
                $validator = Validator::make(
                    [$setting->key => $value],
                    [$setting->key => $setting->validation_rules]
                );

                if ($validator->fails()) {
                    $errors[$setting->key] = $validator->errors()->first($setting->key);
                    continue;
                }
            }

            // Handle file uploads
            if ($setting->type === 'file' && $request->hasFile($setting->key)) {
                $file = $request->file($setting->key);
                
                // Delete old file if exists
                if ($setting->value && Storage::exists('public/' . $setting->value)) {
                    Storage::delete('public/' . $setting->value);
                }

                // Store new file
                $path = $file->store('settings', 'public');
                $value = $path;
            }

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = $request->has($setting->key) ? '1' : '0';
            }

            // Handle JSON values
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            }

            // Update setting
            $setting->update(['value' => $value]);
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Clear cache
        Cache::forget('app_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Reset settings to default.
     */
    public function reset(Request $request)
    {
        $group = $request->input('group');
        
        if ($group) {
            $this->seedDefaultSettings($group);
            $message = ucfirst($group) . ' settings reset to default values.';
        } else {
            $this->seedAllDefaultSettings();
            $message = 'All settings reset to default values.';
        }

        Cache::forget('app_settings');

        return redirect()->route('settings.index')
            ->with('success', $message);
    }

    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Send test email
            \Mail::raw('This is a test email from your Online Exam Management System.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - Online Exam Management System');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Application cache cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export settings as JSON.
     */
    public function export()
    {
        $settings = Setting::all()->toArray();
        $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON.
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->getRealPath());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['settings_file' => 'Invalid JSON file.']);
            }

            foreach ($settings as $settingData) {
                Setting::updateOrCreate(
                    ['key' => $settingData['key']],
                    $settingData
                );
            }

            Cache::forget('app_settings');

            return redirect()->route('settings.index')
                ->with('success', 'Settings imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['settings_file' => 'Error importing settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Seed default settings for a specific group.
     */
    private function seedDefaultSettings(string $group)
    {
        $defaults = $this->getDefaultSettings();
        
        if (isset($defaults[$group])) {
            foreach ($defaults[$group] as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        }
    }

    /**
     * Seed all default settings.
     */
    private function seedAllDefaultSettings()
    {
        $defaults = $this->getDefaultSettings();
        
        foreach ($defaults as $group => $settings) {
            foreach ($settings as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    $setting
                );
            }
        }
    }

    /**
     * Get default settings configuration.
     */
    private function getDefaultSettings(): array
    {
        return [
            'general' => [
                [
                    'key' => 'app_name',
                    'value' => 'Online Exam Management System',
                    'type' => 'text',
                    'group' => 'general',
                    'label' => 'Application Name',
                    'description' => 'The name of your application',
                    'is_required' => true,
                    'validation_rules' => 'required|string|max:255',
                    'options' => null,
                    'sort_order' => 1
                ],
                [
                    'key' => 'app_logo',
                    'value' => null,
                    'type' => 'file',
                    'group' => 'general',
                    'label' => 'Application Logo',
                    'description' => 'Upload your application logo',
                    'is_required' => false,
                    'validation_rules' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'options' => null,
                    'sort_order' => 2
                ],
                [
                    'key' => 'app_description',
                    'value' => 'A comprehensive online examination management system',
                    'type' => 'textarea',
                    'group' => 'general',
                    'label' => 'Application Description',
                    'description' => 'Brief description of your application',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 3
                ],
                [
                    'key' => 'timezone',
                    'value' => 'UTC',
                    'type' => 'select',
                    'group' => 'general',
                    'label' => 'Default Timezone',
                    'description' => 'Default timezone for the application',
                    'is_required' => true,
                    'validation_rules' => null,
                    'options' => [
                        'UTC' => 'UTC',
                        'America/New_York' => 'Eastern Time',
                        'America/Chicago' => 'Central Time',
                        'America/Denver' => 'Mountain Time',
                        'America/Los_Angeles' => 'Pacific Time',
                        'Europe/London' => 'London',
                        'Europe/Paris' => 'Paris',
                        'Asia/Tokyo' => 'Tokyo',
                        'Asia/Singapore' => 'Singapore'
                    ],
                    'sort_order' => 4
                ]
            ],
            'email' => [
                [
                    'key' => 'mail_from_address',
                    'value' => 'noreply@example.com',
                    'type' => 'email',
                    'group' => 'email',
                    'label' => 'From Email Address',
                    'description' => 'Default email address for outgoing emails',
                    'is_required' => true,
                    'validation_rules' => 'required|email',
                    'options' => null,
                    'sort_order' => 1
                ],
                [
                    'key' => 'mail_from_name',
                    'value' => 'Online Exam System',
                    'type' => 'text',
                    'group' => 'email',
                    'label' => 'From Name',
                    'description' => 'Default name for outgoing emails',
                    'is_required' => true,
                    'validation_rules' => 'required|string|max:255',
                    'options' => null,
                    'sort_order' => 2
                ],
                [
                    'key' => 'enable_email_notifications',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'email',
                    'label' => 'Enable Email Notifications',
                    'description' => 'Enable or disable email notifications',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 3
                ]
            ],
            'certificate' => [
                [
                    'key' => 'certificate_template',
                    'value' => 'default',
                    'type' => 'select',
                    'group' => 'certificate',
                    'label' => 'Certificate Template',
                    'description' => 'Default certificate template',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => [
                        'default' => 'Default Template',
                        'modern' => 'Modern Template',
                        'classic' => 'Classic Template'
                    ],
                    'sort_order' => 1
                ],
                [
                    'key' => 'certificate_signature',
                    'value' => null,
                    'type' => 'file',
                    'group' => 'certificate',
                    'label' => 'Certificate Signature',
                    'description' => 'Upload signature image for certificates',
                    'is_required' => false,
                    'validation_rules' => 'image|mimes:jpeg,png,jpg,gif|max:1024',
                    'options' => null,
                    'sort_order' => 2
                ],
                [
                    'key' => 'certificate_authority_name',
                    'value' => 'Administrator',
                    'type' => 'text',
                    'group' => 'certificate',
                    'label' => 'Authority Name',
                    'description' => 'Name of the certifying authority',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 3
                ],
                [
                    'key' => 'certificate_authority_title',
                    'value' => 'Director',
                    'type' => 'text',
                    'group' => 'certificate',
                    'label' => 'Authority Title',
                    'description' => 'Title of the certifying authority',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 4
                ]
            ],
            'notification' => [
                [
                    'key' => 'email_notifications_enabled',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'notification',
                    'label' => 'Email Notifications',
                    'description' => 'Enable email notifications',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 1
                ],
                [
                    'key' => 'sms_notifications_enabled',
                    'value' => '0',
                    'type' => 'boolean',
                    'group' => 'notification',
                    'label' => 'SMS Notifications',
                    'description' => 'Enable SMS notifications',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 2
                ]
            ],
            'system' => [
                [
                    'key' => 'maintenance_mode',
                    'value' => '0',
                    'type' => 'boolean',
                    'group' => 'system',
                    'label' => 'Maintenance Mode',
                    'description' => 'Enable maintenance mode',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 1
                ],
                [
                    'key' => 'debug_mode',
                    'value' => '0',
                    'type' => 'boolean',
                    'group' => 'system',
                    'label' => 'Debug Mode',
                    'description' => 'Enable debug mode (not recommended for production)',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 2
                ],
                [
                    'key' => 'auto_backup',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'system',
                    'label' => 'Auto Backup',
                    'description' => 'Enable automatic database backups',
                    'is_required' => false,
                    'validation_rules' => null,
                    'options' => null,
                    'sort_order' => 3
                ]
            ]
        ];
    }
}