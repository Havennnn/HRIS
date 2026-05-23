export interface KpiIndexResource {
    id: number;
    name: string;
    description: string | null;
    created_at: string;
}

export interface KpiEditResource {
    data: {
        id: number;
        name: string;
        description: string | null;
    };
}
