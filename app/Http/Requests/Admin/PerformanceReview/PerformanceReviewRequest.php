<?php

namespace App\Http\Requests\Admin\PerformanceReview;

use App\Enums\Type\ReviewFeedbackType;
use App\Models\Employee;
use App\Models\Kpi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerformanceReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', Rule::exists(Employee::class, 'id')],
            'reviewer_id' => ['required', Rule::exists(Employee::class, 'id')],
            'review_date' => ['required', 'date'],
            'status' => ['required', 'integer', Rule::enum(\App\Enums\Status\PerformanceReviewStatus::class)],
            'scores' => ['nullable', 'array'],
            'scores.*.id' => ['nullable', 'integer'],
            'scores.*.kpi_id' => ['required_with:scores', Rule::exists(Kpi::class, 'id')],
            'scores.*.score' => ['required_with:scores', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'array'],
            'feedback.*.id' => ['nullable', 'integer'],
            'feedback.*.type' => ['required_with:feedback', 'integer', Rule::enum(ReviewFeedbackType::class)],
            'feedback.*.feedback' => ['required_with:feedback', 'string', 'max:1000'],
        ];
    }
}
