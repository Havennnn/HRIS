export interface PositionResource {
    id: number;
    department_id: number;
    name: string;
    salary: number | null;
    allowance: number | null;
    level: string | null;
    created_at: string;
}

export interface Form {
    department_id: string;
    name: string;
    salary: string;
    allowance: string;
    level: string;
}
