<?php

namespace App\Http\Requests\Admin\Employee;

use App\Enums\Type\EmployeeContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'required_with:type,mobile_number'],
            'type' => ['required', 'integer', Rule::enum(EmployeeContactType::class), 'required_with:name,mobile_number'],
            'mobile_number' => ['required', 'string', 'max:20', 'required_with:name,type'],
        ];
    }
}
