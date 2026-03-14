<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/payrolls';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft, Calculator, Wallet } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import { computed } from 'vue';
import type { PayrollShowResource } from './index';

const props = defineProps<{
    data: PayrollShowResource | { data: PayrollShowResource };
}>();

const payrollData = computed<PayrollShowResource>(() => {
    if ('data' in props.data) {
        return props.data.data;
    }

    return props.data;
});

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(Number(amount ?? 0));
}

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Payrolls',
        href: index().url,
    },
    {
        title: `Payroll #${payrollData.value?.id ?? ''}`,
        href: payrollData.value?.id ? show({ payroll: payrollData.value.id }).url : '#',
    },
]);

const headerActions = computed(() => [
    {
        label: 'Back',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    },
]);

const grossPay = computed(() => Number(payrollData.value.gross_pay ?? 0));
const customBonus = computed(() => {
    return (payrollData.value.adjustments ?? [])
        .filter((item) => item.type?.toLowerCase() === 'bonus')
        .reduce((sum, item) => sum + Number(item.amount ?? 0), 0);
});
const customDeduction = computed(() => {
    return (payrollData.value.adjustments ?? [])
        .filter((item) => item.type?.toLowerCase() === 'deduction')
        .reduce((sum, item) => sum + Number(item.amount ?? 0), 0);
});

const lateDeduction = computed(() => Number(payrollData.value.payment_breakdown?.late_deduction ?? 0));
const totalDeductions = computed(() => {
    return Number(payrollData.value.tax ?? 0)
        + Number(payrollData.value.sss ?? 0)
        + Number(payrollData.value.pagibig ?? 0)
        + Number(payrollData.value.philhealth ?? 0)
        + lateDeduction.value
        + customDeduction.value;
});
</script>

<template>
    <Head :title="`Payroll #${payrollData.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="profile"
                title="Payroll Details"
                :subtitle="`Payroll #${payrollData.id}`"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="payrollData.status" />
                </template>
            </DataHeader>

            <div class="mx-auto w-full max-w-5xl space-y-6">
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Wallet class="h-5 w-5" />
                            Employee & Period
                        </CardTitle>
                        <CardDescription>
                            Payroll identity and covered pay period.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Employee</Label>
                                <p class="font-medium">{{ payrollData.employee?.full_name }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Email</Label>
                                <p class="font-medium">{{ payrollData.employee?.email }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Position</Label>
                                <p class="font-medium">{{ payrollData.employee?.position }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Department</Label>
                                <p class="font-medium">{{ payrollData.employee?.department }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Pay Period Start</Label>
                                <p class="font-medium">{{ payrollData.pay_period_start }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-muted-foreground">Pay Period End</Label>
                                <p class="font-medium">{{ payrollData.pay_period_end }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Calculator class="h-5 w-5" />
                            Compensation Breakdown
                        </CardTitle>
                        <CardDescription>
                            Detailed earnings, deductions, and reconciliation for audit review.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-3 rounded-md border p-3">
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <span class="text-sm font-semibold">Payment Section</span>
                                        <span class="text-xs text-muted-foreground">Earnings</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Monthly Basic Salary</span>
                                        <span class="font-medium">{{ formatCurrency(payrollData.basic_salary) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Allowance</span>
                                        <span class="font-medium">{{ formatCurrency(payrollData.allowance) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">
                                            Overtime Pay ({{ payrollData.payment_breakdown?.overtime_minutes ?? 0 }} mins)
                                        </span>
                                        <span class="font-medium text-green-700">
                                            + {{ formatCurrency(payrollData.payment_breakdown?.overtime_pay ?? 0) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Bonus</span>
                                        <span class="font-medium text-green-700">+ {{ formatCurrency(customBonus) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t pt-2 text-sm font-semibold">
                                        <span>Gross Pay</span>
                                        <span>{{ formatCurrency(grossPay) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-3 rounded-md border p-3">
                                    <div class="flex items-center justify-between border-b pb-2">
                                        <span class="text-sm font-semibold">Deduction Section</span>
                                        <span class="text-xs text-muted-foreground">Government & attendance</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">
                                            Late Deduction ({{ payrollData.payment_breakdown?.late_minutes ?? 0 }} mins)
                                        </span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(lateDeduction) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Deductions</span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(customDeduction) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Tax</span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(payrollData.tax) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">SSS</span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(payrollData.sss) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Pag-IBIG</span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(payrollData.pagibig) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">PhilHealth</span>
                                        <span class="font-medium text-red-600">- {{ formatCurrency(payrollData.philhealth) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t pt-2 text-sm font-semibold text-red-700">
                                        <span>Total Deductions</span>
                                        <span>- {{ formatCurrency(totalDeductions) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 rounded-md border border-dashed p-3">
                                <p class="text-sm font-semibold">Calculation Section</p>
                                <p class="text-xs text-muted-foreground">
                                    Gross Pay - Total Deduction = Net Pay
                                </p>
                                <div class="flex items-center justify-between border-t pt-2 text-sm font-semibold">
                                    <span>Net Pay</span>
                                    <span>{{ formatCurrency(payrollData.net_pay) }}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="text-lg">Adjustments</CardTitle>
                        <CardDescription>
                            Itemized payroll adjustments linked to this record.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!payrollData.adjustments?.length" class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground">
                            No adjustments found for this payroll.
                        </div>

                        <div v-else class="overflow-x-auto rounded-md border">
                            <table class="w-full text-sm">
                                <thead class="bg-muted/50">
                                    <tr>
                                        <th class="p-3 text-left font-medium">Type</th>
                                        <th class="p-3 text-left font-medium">Reason</th>
                                        <th class="p-3 text-right font-medium">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="adjustment in payrollData.adjustments" :key="adjustment.id" class="border-t">
                                        <td class="p-3">{{ adjustment.type }}</td>
                                        <td class="p-3 text-muted-foreground">{{ adjustment.reason || '-' }}</td>
                                        <td class="p-3 text-right font-medium">{{ formatCurrency(adjustment.amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="text-lg">Attendance Details</CardTitle>
                        <CardDescription>
                            Attendance audit within payroll period ({{ payrollData.attendance_summary?.ratio ?? '0/0' }}).
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-3 md:grid-cols-3">
                            <div class="rounded-md border p-3">
                                <p class="text-xs text-muted-foreground">Recorded Days</p>
                                <p class="text-lg font-semibold">{{ payrollData.attendance_summary?.recorded_days ?? 0 }}</p>
                            </div>
                            <div class="rounded-md border p-3">
                                <p class="text-xs text-muted-foreground">Expected Days</p>
                                <p class="text-lg font-semibold">{{ payrollData.attendance_summary?.expected_days ?? 0 }}</p>
                            </div>
                            <div class="rounded-md border p-3">
                                <p class="text-xs text-muted-foreground">Attendance Ratio</p>
                                <p class="text-lg font-semibold">{{ payrollData.attendance_summary?.ratio ?? '0/0' }}</p>
                            </div>
                        </div>

                        <div v-if="!payrollData.attendance_logs?.length" class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground">
                            No attendance logs found for this payroll period.
                        </div>

                        <div v-else class="overflow-x-auto rounded-md border">
                            <table class="w-full text-sm">
                                <thead class="bg-muted/50">
                                    <tr>
                                        <th class="p-3 text-left font-medium">Date</th>
                                        <th class="p-3 text-left font-medium">Time In</th>
                                        <th class="p-3 text-left font-medium">Time Out</th>
                                        <th class="p-3 text-right font-medium">Late (mins)</th>
                                        <th class="p-3 text-right font-medium">OT (mins)</th>
                                        <th class="p-3 text-center font-medium">OT Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="attendance in payrollData.attendance_logs" :key="attendance.id" class="border-t">
                                        <td class="p-3">{{ attendance.date }}</td>
                                        <td class="p-3">{{ attendance.time_in || '-' }}</td>
                                        <td class="p-3">{{ attendance.time_out || '-' }}</td>
                                        <td class="p-3 text-right">{{ attendance.late_minutes }}</td>
                                        <td class="p-3 text-right">{{ attendance.overtime_minutes }}</td>
                                        <td class="p-3 text-center font-medium">{{ attendance.ot_approved_label }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
