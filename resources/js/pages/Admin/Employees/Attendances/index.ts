import type { Badge } from "piacore/Interface/Badge";

export interface AttendanceTagResource {
    id: number;
    name: string;
    value: number | null;
    badge: Badge | null;
}

export interface AttendanceIndexResource {
    id: number;
    date: string;
    time_in: string | null;
    time_out: string | null;
    late_minutes: number;
    overtime_minutes: number;
    status: Badge | null;
    status_value: number | null;
    tags: AttendanceTagResource[];
    created_at: string;
}
