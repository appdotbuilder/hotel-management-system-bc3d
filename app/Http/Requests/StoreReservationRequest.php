<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_id' => 'required|integer|exists:guests,id',
            'room_type_id' => 'required|integer|exists:room_types,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1|max:10',
            'children' => 'required|integer|min:0|max:10',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'guest_id.required' => 'Please select a guest.',
            'guest_id.exists' => 'The selected guest is invalid.',
            'room_type_id.required' => 'Please select a room type.',
            'room_type_id.exists' => 'The selected room type is invalid.',
            'check_in_date.required' => 'Check-in date is required.',
            'check_in_date.after_or_equal' => 'Check-in date must be today or later.',
            'check_out_date.required' => 'Check-out date is required.',
            'check_out_date.after' => 'Check-out date must be after check-in date.',
            'adults.required' => 'Number of adults is required.',
            'adults.min' => 'At least one adult is required.',
        ];
    }
}