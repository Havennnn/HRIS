import type { Badge } from "piacore/Interface/Badge";

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
    created_at: string;
}

export interface ApplicationEditResource {
    id: number;
    job_id: number;
    job_name: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    full_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status_value: number;
    status: Badge;
    created_at: string;
}

export interface Form {
    job_id: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status: string;
}

export interface ApplicationData {
    id: number;
    full_name: string;
    status: Badge;
}
