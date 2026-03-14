<?php

namespace App\Http\Requests\Admin\Department;

use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PositionRequest extends FormRequest
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
                ? (bool) $user->hasPermission('can-create-position')
                : true;
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return method_exists($user, 'hasPermission')
                ? (bool) $user->hasPermission('can-update-position')
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
        /** @var Position|null $position */
        $position = $this->route('position');
        $id = $position?->getKey();

        return [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique(Position::class)->ignore($id)],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'allowance' => ['nullable', 'numeric', 'min:0'],
            'level' => ['nullable', 'string', 'max:100'],
        ];
    }
}
