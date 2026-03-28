<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/attendance-logs';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowUpDown, Clock } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { h, ref } from 'vue';
import type { AttendanceLogIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Attendance Logs',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<AttendanceLogIndexResource>;
    type?: Option[];
}>();

const search = ref<string>('');
const searchPlaceholder = ref('Search attendance logs...');

const columns: DataTableColumn[] = [
    {
        key: 'employee',
        label: 'Employee',
        headerClass: 'min-w-[200px]',
        cell: ({ row }) => {
            const log = row as AttendanceLogIndexResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium capitalize' }, log.employee),
            ]);
        },
    },
    {
        key: 'type',
        label: 'Type',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceLogIndexResource).type,
    },
    {
        key: 'timestamp',
        label: 'Timestamp',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceLogIndexResource).timestamp,
    },
    {
        key: 'created_at',
        label: 'Created',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as AttendanceLogIndexResource).created_at,
    },
];

const filters = [
    {
        key: 'type',
        label: 'Type',
        icon: Clock,
        options: props.type ?? [],
    },
];

const sorts = [
    {
        key: 'timestamp',
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Attendance Logs',
        widthClass: 'w-44',
        options: [
            { value: 'asc', label: 'Timestamp (Oldest)' },
            { value: 'desc', label: 'Timestamp (Latest)' },
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
    <Head title="Attendance Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <!-- Header -->
                <DataHeader
                    variant="card"
                    title="Attendance Logs"
                    description="Manage employee attendance logs"
                />

                <CardContent class="space-y-4 -mt-3">
                    <DataTablePanel
                        :search-query="search"
                        :search-placeholder="searchPlaceholder"
                        :filters="filters"
                        :sorts="sorts"
                        :show-sorts
                        :show-date-range="false"
                        :show-tabs="false"
                        :columns="columns"
                        :paginated="data"
                        empty-message="No employees matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>







