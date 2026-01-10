<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required' => 'Please enter your name.',
            'guest_phone.required' => 'Please enter your phone number.',
            'room_type_id.required' => 'Please select a room type.',
            'room_type_id.exists' => 'Invalid room type selected.',
            'check_in.required' => 'Please select a check-in date.',
            'check_in.after_or_equal' => 'Check-in date cannot be in the past.',
            'check_out.required' => 'Please select a check-out date.',
            'check_out.after' => 'Check-out date must be after check-in date.',
            'number_of_rooms.required' => 'Please specify the number of rooms.',
            'number_of_rooms.min' => 'You must book at least 1 room.',
        ];
    }
}
