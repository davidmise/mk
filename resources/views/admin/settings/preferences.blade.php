@extends('admin.layouts.app')

@section('title', 'System Preferences')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Preferences</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">System Preferences</h1>
        <p class="page-subtitle">Configure booking rules, notifications, and system behavior</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <form action="{{ route('admin.settings.preferences.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Booking Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Booking Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Default Check-in Time</label>
                                <input type="time" name="check_in_time" class="form-control"
                                       value="{{ old('check_in_time', $settings['check_in_time'] ?? '14:00') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Default Check-out Time</label>
                                <input type="time" name="check_out_time" class="form-control"
                                       value="{{ old('check_out_time', $settings['check_out_time'] ?? '11:00') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Minimum Stay (nights)</label>
                                <input type="number" name="min_stay" class="form-control" min="1" max="30"
                                       value="{{ old('min_stay', $settings['min_stay'] ?? '1') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Maximum Stay (nights)</label>
                                <input type="number" name="max_stay" class="form-control" min="1" max="365"
                                       value="{{ old('max_stay', $settings['max_stay'] ?? '30') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Advance Booking (days)</label>
                                <input type="number" name="advance_booking_days" class="form-control" min="0" max="365"
                                       value="{{ old('advance_booking_days', $settings['advance_booking_days'] ?? '365') }}">
                                <small class="text-muted">How far in advance guests can book</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Last Minute Booking (hours)</label>
                                <input type="number" name="last_minute_hours" class="form-control" min="0" max="48"
                                       value="{{ old('last_minute_hours', $settings['last_minute_hours'] ?? '24') }}">
                                <small class="text-muted">Minimum hours before check-in</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-check">
                                    <input type="checkbox" name="require_deposit" value="1" class="form-check-input"
                                           {{ old('require_deposit', $settings['require_deposit'] ?? false) ? 'checked' : '' }}>
                                    <span class="form-check-label">Require deposit for bookings</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group" id="depositAmountGroup">
                                <label class="form-label">Deposit Amount (%)</label>
                                <input type="number" name="deposit_percentage" class="form-control" min="0" max="100"
                                       value="{{ old('deposit_percentage', $settings['deposit_percentage'] ?? '30') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Policy -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Cancellation Policy</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Free Cancellation Window (hours)</label>
                                <input type="number" name="free_cancellation_hours" class="form-control" min="0"
                                       value="{{ old('free_cancellation_hours', $settings['free_cancellation_hours'] ?? '48') }}">
                                <small class="text-muted">Hours before check-in for free cancellation</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Late Cancellation Fee (%)</label>
                                <input type="number" name="late_cancellation_fee" class="form-control" min="0" max="100"
                                       value="{{ old('late_cancellation_fee', $settings['late_cancellation_fee'] ?? '50') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cancellation Policy Text</label>
                        <textarea name="cancellation_policy" class="form-control" rows="3"
                                  placeholder="Free cancellation up to 48 hours before check-in. After that, 50% of the total booking amount will be charged.">{{ old('cancellation_policy', $settings['cancellation_policy'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Email Notifications</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Notification Recipients</label>
                        <input type="text" name="notification_emails" class="form-control"
                               value="{{ old('notification_emails', $settings['notification_emails'] ?? '') }}"
                               placeholder="admin@hotel.com, manager@hotel.com">
                        <small class="text-muted">Comma-separated list of emails for system notifications</small>
                    </div>

                    <div class="notification-toggles">
                        <h4 style="font-size: 0.875rem; margin-bottom: 1rem; color: var(--gray-600);">Send notifications for:</h4>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_new_booking" value="1" class="form-check-input"
                                               {{ old('notify_new_booking', $settings['notify_new_booking'] ?? true) ? 'checked' : '' }}>
                                        <span class="form-check-label">New Bookings</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_booking_cancelled" value="1" class="form-check-input"
                                               {{ old('notify_booking_cancelled', $settings['notify_booking_cancelled'] ?? true) ? 'checked' : '' }}>
                                        <span class="form-check-label">Booking Cancellations</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_check_in" value="1" class="form-check-input"
                                               {{ old('notify_check_in', $settings['notify_check_in'] ?? false) ? 'checked' : '' }}>
                                        <span class="form-check-label">Guest Check-ins</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_check_out" value="1" class="form-check-input"
                                               {{ old('notify_check_out', $settings['notify_check_out'] ?? false) ? 'checked' : '' }}>
                                        <span class="form-check-label">Guest Check-outs</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_contact_message" value="1" class="form-check-input"
                                               {{ old('notify_contact_message', $settings['notify_contact_message'] ?? true) ? 'checked' : '' }}>
                                        <span class="form-check-label">Contact Form Messages</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="notify_low_availability" value="1" class="form-check-input"
                                               {{ old('notify_low_availability', $settings['notify_low_availability'] ?? true) ? 'checked' : '' }}>
                                        <span class="form-check-label">Low Availability Alerts</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest Confirmation Emails -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Guest Email Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="send_booking_confirmation" value="1" class="form-check-input"
                                   {{ old('send_booking_confirmation', $settings['send_booking_confirmation'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Send booking confirmation email to guests</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="send_reminder_email" value="1" class="form-check-input"
                                   {{ old('send_reminder_email', $settings['send_reminder_email'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Send reminder email before check-in</span>
                        </label>
                    </div>

                    <div class="form-group" id="reminderDaysGroup">
                        <label class="form-label">Send reminder (days before)</label>
                        <input type="number" name="reminder_days_before" class="form-control" min="1" max="14" style="width: 150px;"
                               value="{{ old('reminder_days_before', $settings['reminder_days_before'] ?? '2') }}">
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="send_review_request" value="1" class="form-check-input"
                                   {{ old('send_review_request', $settings['send_review_request'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Send review request email after check-out</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Display Preferences -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Display Preferences</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Items per page (tables)</label>
                                <select name="items_per_page" class="form-control">
                                    <option value="10" {{ ($settings['items_per_page'] ?? 20) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($settings['items_per_page'] ?? 20) == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($settings['items_per_page'] ?? '') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($settings['items_per_page'] ?? '') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Date Format</label>
                                <select name="date_format" class="form-control">
                                    <option value="M d, Y" {{ ($settings['date_format'] ?? 'M d, Y') == 'M d, Y' ? 'selected' : '' }}>Jan 15, 2025</option>
                                    <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>15/01/2025</option>
                                    <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>01/15/2025</option>
                                    <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>2025-01-15</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Time Format</label>
                                <select name="time_format" class="form-control">
                                    <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                                    <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12-hour (2:30 PM)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="show_prices_with_tax" value="1" class="form-check-input"
                                   {{ old('show_prices_with_tax', $settings['show_prices_with_tax'] ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Show prices including tax on frontend</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle deposit amount field
    const depositCheckbox = document.querySelector('input[name="require_deposit"]');
    const depositGroup = document.getElementById('depositAmountGroup');

    function toggleDeposit() {
        depositGroup.style.opacity = depositCheckbox.checked ? '1' : '0.5';
        depositGroup.querySelector('input').disabled = !depositCheckbox.checked;
    }

    depositCheckbox.addEventListener('change', toggleDeposit);
    toggleDeposit();

    // Toggle reminder days field
    const reminderCheckbox = document.querySelector('input[name="send_reminder_email"]');
    const reminderGroup = document.getElementById('reminderDaysGroup');

    function toggleReminder() {
        reminderGroup.style.display = reminderCheckbox.checked ? 'block' : 'none';
    }

    reminderCheckbox.addEventListener('change', toggleReminder);
    toggleReminder();
});
</script>
@endpush
