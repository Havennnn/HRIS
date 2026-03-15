<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/payrolls';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, BadgeDollarSign } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { createDateRangeHandler } from 'piacore/helpers/date-range';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { PayrollIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payrolls',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<PayrollIndexResource>;
    statuses?: Option[];
}>();

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(Number(amount ?? 0));
}

const columns: DataTableColumn[] = [
    {
        key: 'employee',
        label: 'Employee',
        headerClass: 'min-w-[220px]',
        cell: ({ row }) => {
            const payroll = row as PayrollIndexResource;

            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium' }, payroll.full_name),
                h('span', { class: 'text-xs text-muted-foreground' }, `${payroll.position} - ${payroll.department}`),
            ]);
        },
    },
    {
        key: 'pay_period',
        label: 'Pay Period',
        cell: ({ row }) => {
            const payroll = row as PayrollIndexResource;
            return `${payroll.pay_period_start} - ${payroll.pay_period_end}`;
        },
    },
    {
        key: 'status',
        label: 'Status',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as PayrollIndexResource).status,
        }),
    },
    {
        key: 'basic_salary',
        label: 'Basic Salary',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => formatCurrency((row as PayrollIndexResource).basic_salary),
    },
    {
        key: 'net_pay',
        label: 'Net Pay',
        headerClass: 'min-w-[140px]',
        cellClass: 'font-medium',
        cell: ({ row }) => formatCurrency((row as PayrollIndexResource).net_pay),
    },
    {
        key: 'created_at',
        label: 'Created',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PayrollIndexResource).created_at,
    },
    {
        key: 'actions',
        headerClass: 'text-right',
        cellClass: 'text-right',
    },
];

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const search = ref<string>('');
const searchPlaceholder = ref('Search payrolls...');

const tabs = computed(() => [
    {
        key: 'default',
        label: 'All',
        count: props.data.count?.defaultCount ?? 0,
    },
    {
        key: 'pending',
        label: 'Pending',
        count: props.data.count?.pendingCount ?? 0,
    },
    {
        key: 'approved',
        label: 'Approved',
        count: props.data.count?.approvedCount ?? 0,
    },
    {
        key: 'disbursed',
        label: 'Disbursed',
        count: props.data.count?.disbursedCount ?? 0,
    },
    {
        key: 'rejected',
        label: 'Rejected',
        count: props.data.count?.rejectedCount ?? 0,
    },
]);

const filters = [
    {
        key: 'status',
        label: 'Status',
        icon: BadgeDollarSign,
        options: props.statuses ?? [],
    },
];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Payrolls',
        widthClass: 'w-64',
        options: [
            { value: 'asc', key: 'created', label: 'Created (Oldest)' },
            { value: 'desc', key: 'created', label: 'Created (Newest)' },
            { value: 'asc', key: 'period_start', label: 'Period Start (Earliest)' },
            { value: 'desc', key: 'period_start', label: 'Period Start (Latest)' },
            { value: 'asc', key: 'period_end', label: 'Period End (Earliest)' },
            { value: 'desc', key: 'period_end', label: 'Period End (Latest)' },
            { value: 'asc', key: 'net_pay', label: 'Net Pay (Lowest)' },
            { value: 'desc', key: 'net_pay', label: 'Net Pay (Highest)' },
        ],
    },
];

const tableActions = computed<DataTableActionsConfig>(() => ({
    showRoute: (row) => show({ payroll: (row as PayrollIndexResource).id }).url,
}));

const handleDateRangeChange = createDateRangeHandler({
    rangeKey: 'created',
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Payrolls" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Payrolls"
                    description="Review generated payrolls and payout statuses."
                />

                <CardContent class="space-y-4 -mt-3">
                    <DataTableControls
                        :tabs="tabs"
                        :active-tab="activeTab"
                        :search-query="search"
                        :search-placeholder="searchPlaceholder"
                        :filters="filters"
                        :sorts="sorts"
                        :date-range="handleDateRangeChange"
                        :show-date-range="true"
                    />

                    <DataTable
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        :active-tab="activeTab"
                        row-key="id"
                        empty-message="No payrolls matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
