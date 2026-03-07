<?php

namespace App\Http\Requests\Admin;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user('admin') ?? $this->user();

        if (! $user) {
            return false;
        }

        if ($this->isMethod('post')) {
            return method_exists($user, 'hasPermission')
                ? (bool) $user->hasPermission('can-create-department')
                : true;
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return method_exists($user, 'hasPermission')
                ? (bool) $user->hasPermission('can-update-department')
                : true;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Department|null $department */
        $department = $this->route('department');
        $id = $department?->getKey();

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Department::class)->ignore($id)],
        ];
    }
}
