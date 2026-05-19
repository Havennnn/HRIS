export interface PerformanceReviewIndexResource {
    id: number;
    employee: { id: number; name: string } | null;
    reviewer: { id: number; name: string } | null;
    review_date: string;
    overall_score: string | null;
    status: { label: string; variant: string } | null;
    created_at: string;
}

export interface PerformanceReviewEditResource {
    data: {
        id: number;
        employee_id: number | null;
        reviewer_id: number | null;
        review_date: string;
        overall_score: string | null;
        status: number | null;
        scores: Array<{ id: number | null; kpi_id: number; score: number }>;
        feedback: Array<{ id: number | null; type: number; feedback: string }>;
    };
}

export interface EmployeeOption {
    id: number;
    first_name: string;
    last_name: string;
}

export interface KpiOption {
    id: number;
    name: string;
}

export interface StatusOption {
    value: number;
    label: string;
}
