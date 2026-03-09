import type { Badge } from "piacore/Interface/Badge";

export interface CareerIndexResource {
    id: number;
    position: string;
    position_id: number;
    description: string;
    is_active: boolean;
    status: Badge;
    created_at: string;
}

export interface CareerEditResource {
    id: number;
    position_id: number;
    position_name: string;
    description: string;
    is_active: boolean;
    status_value: number;
    status: Badge;
    created_at: string;
}

export interface Form {
    position_id: string;
    description: string;
    is_active: boolean;
}

export interface CareerData {
    id: number;
    position: string;
    status: Badge;
}
