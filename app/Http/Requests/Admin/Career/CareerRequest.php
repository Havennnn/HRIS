<?php

namespace App\Http\Requests\Admin\Career;

use App\Enums\Status\CareerStatus;
use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerRequest extends FormRequest
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
            'position_id' => ['required', Rule::exists(Position::class, 'id')],
            'description' => ['required', 'string'],
            'salary' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'integer', Rule::enum(CareerStatus::class)],
        ];
    }
}
