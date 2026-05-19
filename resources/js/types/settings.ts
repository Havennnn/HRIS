export type PayoutConfiguration = {
    id: number;
    name: string;
    slug: string;
    period_start_day: number;
    period_end_day: number | null;
    period_end_is_last_day: boolean;
    cutoff_generation_day: number;
    cutoff_disburse_day: number | null;
    disburse_is_last_day: boolean;
    assumed_from_day: number;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
};
