<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/careers';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { CareerIndexResource } from './index';

const props = defineProps<{
    data: PaginatedData<CareerIndexResource>;
    positions?: Option[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Careers',
        href: index().url,
    },
];

// Position options for filter
const positions = computed<Option[]>(() => props.positions ?? []);

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const search = ref<string>('');
const searchPlaceholder = ref('Search careers...');

const tabs = computed(() => [
    {
        key: 'default',
        label: 'All',
        count: props.data.count?.defaultCount ?? 0,
    },
    {
        key: 'archived',
        label: 'Archived',
        count: props.data.count?.archivedCount ?? 0,
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
        options: positions.value,
    },
];

const sorts = [
    {
        key: 'position',
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Careers',
        widthClass: 'w-44',
        options: [
            { value: 'asc', label: 'Position (A-Z)' },
            { value: 'desc', label: 'Position (Z-A)' },
        ],
    },
];

const tableActions = computed<DataTableActionsConfig>(() => ({
    editRoute: (row) => edit({ career: (row as CareerIndexResource).id }).url,
    deleteRoute: (row) => destroy({ career: (row as CareerIndexResource).id }).url,
    restoreRoute: (row) => restore({ career: (row as CareerIndexResource).id }).url,
    destructiveAction: activeTab.value === 'archived' ? 'restore' : 'delete',
    deleteConfirmMessage: 'Are you sure you want to delete this career?',
    restoreConfirmMessage: 'Are you sure you want to restore this career?',
}));

const columns: DataTableColumn[] = [
    {
        key: 'position',
        label: 'Position',
        headerClass: 'min-w-60',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as CareerIndexResource).position?.name ?? '-',
    },
    {
        key: 'description',
        label: 'Description',
        headerClass: 'min-w-80',
        cell: ({ row }) => {
            const career = row as CareerIndexResource;
            return h('div', { class: 'truncate max-w-xs' }, career.description || '-');
        },
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as CareerIndexResource).created_at,
    },
    {
        key: 'actions',
        headerClass: 'text-right',
        cellClass: 'text-muted-foreground',
    },
];

function handlePageChange(url: string | null): void {
    if (url) {
        window.location.href = url;
    }
}
</script>

<template>
    <Head title="Careers" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Careers"
                    description="Manage career positions within the organization."
                >
                    <template #actions>
                        <Button as-child>
                            <Link :href="create().url">Add Career</Link>
                        </Button>
                    </template>
                </DataHeader>

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
                        :paginated="data"
                        :actions="tableActions"
                        :active-tab="activeTab"
                        row-key="id"
                        empty-message="No careers matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
