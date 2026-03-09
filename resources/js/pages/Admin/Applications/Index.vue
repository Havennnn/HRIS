<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { applications, getPositionOptions, getStatusOptions } from '@/dummy/DummyData';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, restore, show } from '@/routes/applications';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { ApplicationIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Applications',
        href: index().url,
    },
];

// Transform dummy application data to match ApplicationIndexResource format
const transformApplicationToResource = (app: typeof applications[0]): ApplicationIndexResource => ({
    id: app.id,
    job_id: app.id,
    position: app.career,
    full_name: app.full_name,
    first_name: app.full_name.split(' ')[0],
    last_name: app.full_name.split(' ').slice(1).join(' ') || '',
    middle_name: '',
    birthdate: app.birthdate,
    mobile_number: app.mobile_number,
    email: app.email,
    status: app.status[0] || { label: 'Pending', variant: 'badge-pending' },
    created_at: new Date().toISOString().split('T')[0],
});

// Transform applications to ApplicationIndexResource format
const applicationsData = computed<ApplicationIndexResource[]>(() =>
    applications.map(transformApplicationToResource)
);

// Create paginated data from dummy data
const dummyPaginatedData = computed<PaginatedData<ApplicationIndexResource>>(() => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: applicationsData.value.length,
    data: applicationsData.value,
    count: {
        defaultCount: applicationsData.value.length,
        archivedCount: 0,
    },
}));

// Use dummy data (for development)
const tableData = computed<PaginatedData<ApplicationIndexResource>>(() => {
    return dummyPaginatedData.value;
});

// Job/Position options for filter
const jobs = computed<Option[]>(() => getPositionOptions());

// Status options for filter
const statuses = computed<Option[]>(() => getStatusOptions());

const props = defineProps<{
    data?: PaginatedData<ApplicationIndexResource>;
    jobs?: Option[];
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
                h('span', { class: 'text-muted-foreground' }, application.email),
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
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as ApplicationIndexResource).created_at,
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
        count: tableData.value.count?.defaultCount ?? 0,
    },
    {
        key: 'archived',
        label: 'Archived',
        count: tableData.value.count?.archivedCount ?? 0,
    },
    {
        key: 'activity_logs',
        label: 'Activity Log',
    },
]);

const filters = [
    {
        key: 'position',
        label: 'Position',
        icon: Badge,
        options: jobs.value,
    },
    {
        key: 'status',
        label: 'Status',
        icon: Badge,
        options: statuses.value,
    },
];

const sorts = [
    {
        key: 'name',
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Applications',
        widthClass: 'w-44',
        options: [
            { value: 'asc', label: 'Name (A-Z)' },
            { value: 'desc', label: 'Name (Z-A)' },
        ],
    },
];

const tableActions = computed<DataTableActionsConfig>(() => ({
    editRoute: (row) => show({ application: row.id as number }).url,
    deleteRoute: (row) => destroy({ application: row.id as number }).url,
    restoreRoute: (row) => restore({ application: row.id as number }).url,
    destructiveAction: activeTab.value === 'archived' ? 'restore' : 'delete',
    deleteConfirmMessage: 'Are you sure you want to delete this application?',
    restoreConfirmMessage: 'Are you sure you want to restore this application?',
}));

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
                        :search-placeholder="searchPlaceholder"
                    />

                    <DataTable
                        :columns="columns"
                        :paginated="tableData"
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
