<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import ApplicationIndexResource from '@/Http/Resources/Admin/Application/ApplicationIndexResource';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, restore, show } from '@/routes/applications';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge, Filter } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { createDateRangeHandler } from 'piacore/helpers/date-range';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Applications',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<ApplicationIndexResource>;
    positions?: Option[];
    statuses?: Option[];
}>();

const columns: DataTableColumn[] = [
    {
        key: 'full_name',
        label: 'Name',
        headerClass: 'min-w-70',
        cell: ({ row }) => {
            const application = row as ApplicationIndexResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium capitalize' }, application.full_name),
                h('span', { class: 'text-muted-foreground text-sm' }, application.email),
            ]);
        },
    },
    {
        key: 'position',
        label: 'Position',
        headerClass: 'min-w-60',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as ApplicationIndexResource).position,
    },
    {
        key: 'mobile_number',
        label: 'Mobile',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as ApplicationIndexResource).mobile_number,
    },
    {
        key: 'status',
        label: 'Status',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as ApplicationIndexResource).status
        }),
    },
    {
        key: 'created_at',
        label: 'Applied Date',
        cellClass: 'text-muted-foreground',
        sortable: false,
        cell: ({ row }) => {
            const date = new Date((row as ApplicationIndexResource).created_at);
            return date.toLocaleDateString();
        },
    },
    {
        key: 'actions',
        headerClass: 'text-right',
        cellClass: 'text-muted-foreground',
    },
];

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const search = ref<string>('');
const searchPlaceholder = ref('Search applications...');

const tabs = computed(() => [
    {
        key: 'default',
        label: 'All',
        count: props.data?.count?.defaultCount ?? 0,
    },
    {
        key: 'archived',
        label: 'Archived',
        count: props.data?.count?.archivedCount ?? 0,
    },
]);

const filters = computed(() => [
    {
        key: 'position',
        label: 'Position',
        icon: Badge,
        placeholder: 'Filter by position',
        widthClass: 'w-84',
        options: props.positions ?? [],
    },
    {
        key: 'status',
        label: 'Status',
        icon: Filter,
        placeholder: 'Filter by status',
        options: props.statuses ?? [],
    },
]);

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Applications',
        widthClass: 'w-56',
        options: [
            { value: 'asc', key: 'created', label: 'Created Date (Oldest)' },
            { value: 'desc', key: 'created', label: 'Created Date (Newest)' },
        ],
    },
];

const handleDateRangeChange = createDateRangeHandler({
    rangeKey: 'created',
});

const tableActions = computed<DataTableActionsConfig>(() => {
    const isArchived = activeTab.value === 'archived';

    return {
        variant: 'inline',
        showRoute: (row) => show((row as ApplicationIndexResource).id).url,
        deleteRoute: !isArchived ? (row) => destroy({ application: (row as ApplicationIndexResource).id }).url : undefined,
        restoreRoute: isArchived ? (row) => restore({ application: (row as ApplicationIndexResource).id }).url : undefined,
        destructiveAction: isArchived ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this application?',
        restoreConfirmMessage: 'Are you sure you want to restore this application?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Applications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Applications"
                    description="Manage job applications within the organization."
                />

                <CardContent class="space-y-4 -mt-3">
                    <DataTableControls
                        :tabs="tabs"
                        :active-tab="activeTab"
                        :activity-log="true"
                        :search-query="search"
                        :filters="filters"
                        :sorts="sorts"
                        :show-date-range="true"
                        :date-range="handleDateRangeChange"
                        :search-placeholder="searchPlaceholder"
                    />

                    <DataTable
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        :active-tab="activeTab"
                        row-key="id"
                        empty-message="No applications matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
