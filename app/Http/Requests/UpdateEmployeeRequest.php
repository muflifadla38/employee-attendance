<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        $userStatuses = array_column(UserStatus::cases(), 'value');

        return [
            'name' => 'sometimes|string|max:255',
            'bod' => 'sometimes|date_format:Y-m-d',
            'city' => 'sometimes|string|max:255',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:1024',
            'email' => 'sometimes|string|unique:users,email',
            'password' => 'sometimes|required|string|min:8',
            'status' => 'sometimes|in:'.implode(',', $userStatuses),
        ];
    }
}
