import type { Badge } from 'piacore/Interface/Badge';

export const ApplicationStatus = {
    PENDING: 1,
    REVIEWING: 2,
    INTERVIEW: 3,
    REJECTED: 4,
    HIRED: 5,
} as const;

export interface ApplicationIndexResource {
    id: number;
    job_id: number;
    position: string;
    full_name: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status: Badge;
    status_value?: number;
    created_at: string;
}

export interface ApplicationShowResource {
    id: number;
    position: string;
    full_name: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status: Badge;
    status_value: number;
    details: {
        id: number;
    } | null;
    interview: {
        id: number;
    } | null;
    created_at: string;
    updated_at: string;
}
