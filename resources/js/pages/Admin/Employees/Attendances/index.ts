import type { Badge } from "piacore/Interface/Badge";

export interface AttendanceIndexResource {
    id: number;
    date: string;
    time_in: string | null;
    time_out: string | null;
    late_minutes: number;
    overtime_minutes: number;
    status: Badge;
    status_value: number;
    created_at: string;
}