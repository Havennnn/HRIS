
export interface CareerIndexResource {
    id: number;
    position: {
        id: number;
        name: string;
    };
    description: string;
    salary: string | null;
    status: {
        label: string;
        variant: string;
    };
    created_at: string;
}

export interface CareerEditResource {
    data: {
        id: number;
        position_id: number;
        position: string;
        description: string;
        salary: string | null;
        status_value: number;
        status: {
            label: string;
            variant: string;
        };
        meta_title: string | null;
        meta_description: string | null;
        og_title: string | null;
        og_description: string | null;
        og_image: string | null;
        created_at: string;
    };
}

export interface Form {
    position_id: string;
    description: string;
    salary: string;
    salary_type: 'fixed' | 'range';
    salary_min: string;
    salary_max: string;
    is_active: boolean;
}
