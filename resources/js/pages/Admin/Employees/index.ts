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

export interface UploadedFilePreview {
    name?: string;
    url?: string;
    size?: number | string;
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
    contact_person: {
        name: string;
        type_value: number;
        type: string;
        mobile_number: string;
    } | null;
    documents: {
        sss: UploadedFilePreview | null;
        philhealth: UploadedFilePreview | null;
        bir: UploadedFilePreview | null;
        medical: UploadedFilePreview | null;
    };
    device: {
        desktop: string;
        laptop: string;
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
