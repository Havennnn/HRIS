<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index } from '@/routes/employees';
import * as attendanceRoutes from '@/routes/employees/attendance';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Calendar } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { EmployeeData } from '../index';
import type { AttendanceIndexResource } from './index';


const props = defineProps<{
    data: PaginatedData<AttendanceIndexResource>;
    employee: { data: EmployeeData };
    status?: Option[];
}>();

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const search = ref<string>('');
const searchPlaceholder = ref('Search attendances...');

const employeeData = computed(() => props.employee?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Employees',
        href: index().url
    },
    {
        title: 'Edit',
        href: edit({ employee: employeeData.value.id }).url,
    },
    {
        title: 'Attendance',
        href: '#',
    },
]);

const columns: DataTableColumn[] = [
    {
        key: 'date',
        label: 'Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).date,
    },
    {
        key: 'time_in',
        label: 'Time In',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).time_in ?? '-',
    },
    {
        key: 'time_out',
        label: 'Time Out',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).time_out ?? '-',
    },
    {
        key: 'late_minutes',
        label: 'Late (min)',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).late_minutes ?? 0,
    },
    {
        key: 'overtime_minutes',
        label: 'Overtime (min)',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).overtime_minutes ?? 0,
    },
    {
        key: 'status',
        label: 'Status',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as AttendanceIndexResource).status
        }),
    },
    {
        key: 'created_at',
        label: 'Created',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceIndexResource).created_at,
    },
];

const tabs = computed(() => [
    {
        key: 'information',
        label: 'Information',
        route: edit({ employee: employeeData.value?.id as number }).url,
    },
    {
        key: 'default',
        label: 'Attendance',
        route: attendanceRoutes.index({ employee: employeeData.value?.id as number }).url,
    },
    {
        key: 'activity_logs',
        label: 'Activity Log',
        route: edit({ employee: employeeData.value?.id as number }).url + '?tab=activity_logs',
    },
]);

const filters = [
    {
        key: 'status',
        label: 'Status',
        icon: Calendar,
        options: props.status,
    }
];

const sorts = [
    {
        key: 'date',
        label: 'Sort',
        icon: Calendar,
        menuLabel: 'Sort Attendances',
        widthClass: 'w-44',
        options: [
            { value: 'desc', label: 'Date (Latest)' },
            { value: 'asc', label: 'Date (Oldest)' },
        ],
    },
];

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Attendances" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <DataHeader
                variant="form"
                :title="employeeData?.full_name"
                subtitle="Employee Profile"
                :use-avatar="false"
                :actions="[{ label: 'Back', href: index().url, icon: ArrowLeft, variant: 'outline' as const, size: 'sm' as const }]"
            >
                <template #badge>
                    <DataBadge :badge="employeeData?.status" />
                </template>
            </DataHeader>

            <!-- Controls -->
            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                :activity-log="true"
                :filters="filters"
                :sorts="sorts"
                :show-search="false"
                :show-date-range="true"
            />

            <!-- Data Table -->
            <DataTable
                :columns="columns"
                :paginated="data"
                :active-tab="activeTab"
                row-key="id"
                empty-message="No employees matched your filters."
                @page-change="handlePageChange"
            />
        </div>
    </AppLayout>
</template>
