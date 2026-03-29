import type { Badge } from "piacore/Interface/Badge";

export interface EmployeeIndexResource {
    id: number;
    position: string;
    department: string;
    full_name: string;
    birthdate: string;
    mobile_number: string;
    email: string;
    status: Badge;
    type: string;
    created_at: string;
}

export interface EmployeeEditResource {
    id: number;
    position_id: number;
    position: string;
    position_level: string;
    department_id: number;
    department: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    full_name: string;
    birthdate: string;
    birthdate_formatted: string;
    hired_date: string | null;
    mobile_number: string;
    email: string;
    status_value: number;
    status: Badge;
    type_value: number;
    type: string;
    type_label: string;
    created_at: string;
    device: {
        desktop: boolean | null;
        laptop: boolean | null;
    } | null;
}

export interface Form {
    position_id: string;
    first_name: string;
    last_name: string;
    middle_name: string;
    birthdate: string;
    hired_date: string;
    mobile_number: string;
    email: string;
    status: string;
    type: string;
}

export interface EmployeeData {
    id: number;
    full_name: string;
    status: Badge;
}
