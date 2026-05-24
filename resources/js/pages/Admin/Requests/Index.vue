<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { approve, cancel, destroy, index, reject, restore, show } from '@/routes/requests';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge, Ban, Check, Filter, X } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import ImportExportDialog from 'piacore/components/ImportExportDialog.vue';
import { createDateRangeHandler } from 'piacore/helpers/date-range';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { computed, h, ref } from 'vue';
import type { RequestIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Leave Requests',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<RequestIndexResource>;
    types: Array<{ value: string; label: string }>;
    statuses: Array<{ value: string; label: string }>;
}>();

const columns: DataTableColumn[] = [
    {
        key: 'employee',
        label: 'Employee',
        headerClass: 'min-w-[200px]',
        cell: ({ row }) => {
            const request = row as RequestIndexResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium' }, request.full_name),
                h('span', { class: 'text-xs text-muted-foreground' }, `${request.position} - ${request.department}`),
            ]);
        },
    },
    {
        key: 'type',
        label: 'Type',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as RequestIndexResource).type,
    },
    {
        key: 'message',
        label: 'Message',
        cellClass: 'max-w-[200px] truncate text-muted-foreground',
        cell: ({ row }) => (row as RequestIndexResource).message,
    },
    {
        key: 'requested_date',
        label: 'Requested Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as RequestIndexResource).requested_date,
    },
    {
        key: 'days',
        label: 'Days/Hours',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => {
            const request = row as RequestIndexResource;
            return request.days ? `${request.days} days` : (request.overtime_hours ? `${request.overtime_hours} hours` : '-');
        },
    },
    {
        key: 'status',
        label: 'Status',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as RequestIndexResource).status
        }),
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as RequestIndexResource).created_at,
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
const searchPlaceholder = ref('Search requests...');

const tabs = computed(() => [
    {
        key: 'default',
        label: 'All',
        count: props.data.count?.defaultCount ?? 0,
    },
    {
        key: 'leaves',
        label: 'Leaves',
        count: props.data.count?.leavesCount ?? 0,
    },
    {
        key: 'overtimes',
        label: 'Overtimes',
        count: props.data.count?.overtimesCount ?? 0,
    },
    {
        key: 'archived',
        label: 'Archived',
        count: props.data.count?.archivedCount ?? 0,
    },
]);

const filters = computed(() => [
    {
        key: 'type',
        label: 'Type',
        icon: Filter,
        placeholder: 'Filter by type',
        options: props.types,
    },
    {
        key: 'status',
        label: 'Status',
        icon: Badge,
        placeholder: 'Filter by status',
        options: props.statuses,
    },
]);

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Requests',
        widthClass: 'w-64',
        options: [
            { value: 'asc', key:'created', label: 'Created Date (Oldest)' },
            { value: 'desc', key:'created', label: 'Created Date (Newest)' },
            { value: 'asc', key:'requested_date', label: 'Requested Date (Earliest)' },
            { value: 'desc', key:'requested_date', label: 'Requested Date (Latest)' },
        ],
    },
];

const handleDateRangeChange = createDateRangeHandler({
    rangeKey: 'created',
});

const tableActions = computed<DataTableActionsConfig>(() => {
    const isArchived = activeTab.value === 'archived';

    // Determine which custom actions to show based on status
    const getCustomActions = () => {
        if (isArchived) return [];

        return [
            {
                key: 'approve',
                label: 'Approve',
                icon: Check,
                route: (row) => approve({ request: (row as RequestIndexResource).id }).url,
                method: 'post',
                confirmMessage: 'Are you sure you want to approve this request?',
                showWhen: (row: Record<string, unknown>) => {
                    const request = row as RequestIndexResource;
                    return request.status_value === 2; // REVIEWING
                },
            },
            {
                key: 'reject',
                label: 'Reject',
                icon: X,
                route: (row) => reject({ request: (row as RequestIndexResource).id }).url,
                method: 'post',
                destructive: true,
                confirmMessage: 'Are you sure you want to reject this request?',
                showWhen: (row: Record<string, unknown>) => {
                    const request = row as RequestIndexResource;
                    return request.status_value === 2; // REVIEWING
                },
            },
            {
                key: 'cancel',
                label: 'Cancel',
                icon: Ban,
                route: (row) => cancel({ request: (row as RequestIndexResource).id }).url,
                method: 'post',
                confirmMessage: 'Are you sure you want to cancel this request?',
                showWhen: (row: Record<string, unknown>) => {
                    const request = row as RequestIndexResource;
                    return request.status_value === 3; // APPROVED
                },
            },
            {
                key: 'complete',
                label: 'Mark Complete',
                icon: Check,
                route: (row) => complete({ request: (row as RequestIndexResource).id }).url,
                method: 'post',
                confirmMessage: 'Are you sure you want to mark this request as completed?',
                showWhen: (row: Record<string, unknown>) => {
                    const request = row as RequestIndexResource;
                    return request.status_value === 3; // APPROVED
                },
            },
        ];
    };

    return {
        variant: 'menu',
        customActions: getCustomActions(),
        showRoute: (row) => show({ request: (row as RequestIndexResource).id }).url,
        deleteRoute: (row) => destroy({ request: (row as RequestIndexResource).id }).url,
        restoreRoute: (row) => restore({ request: (row as RequestIndexResource).id }).url,
        destructiveAction: isArchived ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to archive this request?',
        restoreConfirmMessage: 'Are you sure you want to restore this request?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}

</script>

<template>
    <Head title="Leave Requests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Leave Requests"
                    description="Manage employee leave and overtime requests."
                >
                    <template #actions>
                        <ImportExportDialog />
                    </template>
                />

                <CardContent class="space-y-4 -mt-3">
                    <DataTablePanel
                        :tabs="tabs"
                        :active-tab="activeTab"
                        :activity-log="true"
                        :search-query="search"
                        :filters="filters"
                        :sorts="sorts"
                        :show-date-range="true"
                        :search-placeholder="searchPlaceholder"
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        row-key="id"
                        empty-message="No requests matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>







