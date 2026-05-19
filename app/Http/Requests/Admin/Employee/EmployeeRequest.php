<?php

namespace App\Http\Requests\Admin\Employee;

use App\Enums\Type\EmployeeType;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
        $employeeId = $this->route('employee');

        return [
            'position_id' => ['nullable', Rule::exists(Position::class, 'id')],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before:today'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique(Employee::class)->ignore($employeeId)],
            'type' => ['required', 'integer', Rule::enum(EmployeeType::class)],
        ];
    }
}
