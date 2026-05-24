<?php
declare(strict_types=1);
namespace App\Http\Requests\Admin\Position;
use Illuminate\Foundation\Http\FormRequest;

class PositionImportRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['file' => ['required', 'file', 'mimes:csv,xlsx', 'max:5120']]; }
    public function rowRules(): array { return [
        'name' => ['required', 'string', 'max:255'],
        'level' => ['nullable', 'string', 'max:255'],
        'salary' => ['nullable', 'numeric'],
        'allowance' => ['nullable', 'numeric'],
        'department_id' => ['nullable', 'exists:departments,id'],
    ]; }
    public function messages(): array { return ['file.required' => 'Please select a file.', 'file.mimes' => 'File must be CSV or XLSX.']; }
    public function rowMessages(): array { return ['name.required' => 'Name is required.']; }
}
