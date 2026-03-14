export interface HolidayIndexResource {
    id: number;
    name: string;
    date: string;
    date_raw: string;
    type: string;
    type_value: number;
    type_badge: {
        label: string;
        variant: string;
    };
    description: string | null;
    created_at: string;
}

export interface HolidayEditResource {
    data: {
        id: number;
        name: string;
        date: string;
        date_formatted: string;
        type_value: number;
        type: string;
        type_badge: {
            label: string;
            variant: string;
        };
        description: string | null;
        created_at: string;
    };
}
