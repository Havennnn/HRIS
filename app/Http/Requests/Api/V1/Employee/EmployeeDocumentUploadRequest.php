<?php

namespace App\Http\Requests\Api\V1\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class EmployeeDocumentUploadRequest extends FormRequest
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
            'sss_file' => ['nullable', File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(10240)],
            'philhealth_file' => ['nullable', File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(10240)],
            'bir_file' => ['nullable', File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(10240)],
            'medical_file' => ['nullable', File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(10240)],
        ];
    }

    /**
     * Ensure at least one onboarding document is provided.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (
                ! $this->hasFile('sss_file')
                && ! $this->hasFile('philhealth_file')
                && ! $this->hasFile('bir_file')
                && ! $this->hasFile('medical_file')
            ) {
                $validator->errors()->add('documents', 'At least one onboarding document is required.');
            }
        });
    }
}
