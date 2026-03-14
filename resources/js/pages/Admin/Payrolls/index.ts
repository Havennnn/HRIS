import type { Badge } from 'piacore/Interface/Badge';

export const PayrollStatus = {
    PENDING: 1,
    APPROVED: 2,
    DISBURSED: 3,
    REJECTED: 4,
} as const;

export interface PayrollIndexResource {
    id: number;
    employee_id: number;
    full_name: string;
    position: string;
    department: string;
    status_value: number;
    status: Badge;
    basic_salary: number;
    allowance: number;
    tax: number;
    sss: number;
    pagibig: number;
    philhealth: number;
    gross_pay: number;
    net_pay: number;
    pay_period_start: string;
    pay_period_end: string;
    created_at: string;
}

export interface PayrollAdjustmentResource {
    id: number;
    type: string;
    type_value: number;
    reason: string;
    amount: number;
}

export interface PayrollAttendanceSummary {
    recorded_days: number;
    expected_days: number;
    ratio: string;
}

export interface PayrollAttendanceLog {
    id: number;
    date: string;
    time_in: string | null;
    time_out: string | null;
    late_minutes: number;
    overtime_minutes: number;
    ot_approved: boolean;
    ot_approved_label: 'Yes' | 'No';
}

export interface PayrollShowResource {
    id: number;
    employee: {
        id: number;
        full_name: string;
        email: string;
        position: string;
        position_level: string;
        department: string;
    };
    status_value: number;
    status: Badge;
    basic_salary: number;
    allowance: number;
    tax: number;
    sss: number;
    pagibig: number;
    philhealth: number;
    gross_pay: number;
    net_pay: number;
    payment_breakdown: {
        overtime_minutes: number;
        overtime_pay: number;
        late_minutes: number;
        late_deduction: number;
    };
    pay_period_start: string;
    pay_period_end: string;
    adjustments: PayrollAdjustmentResource[];
    attendance_summary: PayrollAttendanceSummary;
    attendance_logs: PayrollAttendanceLog[];
    created_at: string;
    updated_at: string;
}
