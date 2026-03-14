import type { Badge } from "piacore/Interface/Badge";

export const RequestStatus = {
    PENDING: 1,
    REVIEWING: 2,
    APPROVED: 3,
    REJECTED: 4,
    CANCELLED: 5,
    RESCHEDULE: 6,
    COMPLETED: 7,
} as const;

export interface RequestIndexResource {
    id: number;
    full_name: string;
    position: string;
    department: string;
    type: string;
    status_value: number;
    status: Badge;
    message: string;
    requested_date: string;
    end_date: string | null;
    days: number | null;
    overtime_hours: number | null;
    created_at: string;
}

export interface RequestShowResource {
    id: number;
    full_name: string;
    position: string;
    position_level: string;
    department: string;
    type: string;
    type_value: number;
    status_value: number;
    status: Badge;
    message: string;
    requested_date: string;
    end_date: string | null;
    days: number | null;
    overtime_hours: number | null;
}

export interface Form {
    employee_id: string;
    type: string;
    message: string;
    requested_date: string;
    end_date: string;
    days: string;
    overtime_hours: string;
}
