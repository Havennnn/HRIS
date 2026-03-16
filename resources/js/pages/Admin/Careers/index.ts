
export interface CareerIndexResource {
    id: number;
    position: {
        id: number;
        name: string;
    };
    description: string;
    is_active: boolean;
    created_at: string;
}

export interface CareerEditResource {
    data: {
        id: number;
        position_id: number;
        position: string;
        description: string;
        is_active: boolean;
        created_at: string;
    };
}

export interface Form {
    position_id: string;
    description: string;
    is_active: boolean;
}
