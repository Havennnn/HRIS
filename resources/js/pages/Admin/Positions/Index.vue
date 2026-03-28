<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/positions';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Briefcase } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import { useAuth } from 'piacore/composables/useAuth';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { PositionResource } from './Positions';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Positions',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<PositionResource>;
    departments?: Option[];
}>();

const { hasPermission } = useAuth();

const canCreatePosition = computed(() => hasPermission('can-create-position'));
const canUpdatePosition = computed(() => hasPermission('can-update-position'));
const canArchivePosition = computed(() => hasPermission('can-archive-position'));
const canRestorePosition = computed(() => hasPermission('can-restore-position'));

const columns: DataTableColumn[] = [
    {
        key: 'name',
        label: 'Position',
        headerClass: 'min-w-[240px]',
        cell: ({ row }) => {
            const position = row as PositionResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium' }, position.name),
            ]);
        },
    },
    {
        key: 'level',
        label: 'Level',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PositionResource).level ?? '-',
    },
    {
        key: 'salary',
        label: 'Salary',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => {
            const position = row as PositionResource;
            return position.salary ? `P${Number(position.salary).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '-';
        },
    },
    {
        key: 'allowance',
        label: 'Allowance',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => {
            const position = row as PositionResource;
            return position.allowance ? `P${Number(position.allowance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '-';
        },
    },
    {
        key: 'department',
        label: 'Department',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PositionResource).department,
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PositionResource).created_at,
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
const searchPlaceholder = ref('Search positions...');

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
]);

const filters = [
    {
        key: 'department',
        label: 'Department',
        icon: Briefcase,
        options: props.departments ?? [],
    },
];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Positions',
        widthClass: 'w-64',
        options: [
            { value: 'asc', key: 'name', label: 'Name (A-Z)' },
            { value: 'desc', key: 'name', label: 'Name (Z-A)' },
            { value: 'asc', key: 'created', label: 'Created Date (Oldest)' },
            { value: 'desc', key: 'created', label: 'Created Date (Newest)' },
        ],
    },
];

const tableActions = computed<DataTableActionsConfig | undefined>(() => {
    const archivedTab = activeTab.value === 'archived';

    if (!archivedTab && !canUpdatePosition.value && !canArchivePosition.value) {
        return undefined;
    }

    if (archivedTab && !canRestorePosition.value) {
        return undefined;
    }

    return {
        editRoute: canUpdatePosition.value ? (row) => edit({ position: (row as PositionResource).id }).url : undefined,
        deleteRoute: !archivedTab && canArchivePosition.value ? (row) => destroy({ position: (row as PositionResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestorePosition.value ? (row) => restore({ position: (row as PositionResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this position?',
        restoreConfirmMessage: 'Are you sure you want to restore this position?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Positions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Positions"
                    description="Manage positions within the organization."
                >
                    <template #actions>
                        <Button v-if="canCreatePosition" as-child>
                            <Link :href="create().url">Add Position</Link>
                        </Button>
                    </template>
                </DataHeader>

                <CardContent class="space-y-4 -mt-3">
                    <DataTablePanel
                        :tabs="tabs"
                        layout="inline"
                        :active-tab="activeTab"
                        :activity-log="true"
                        :search-query="search"
                        :filters="filters"
                        :sorts="sorts"
                        :show-date-range="false"
                        :search-placeholder="searchPlaceholder"
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        row-key="id"
                        empty-message="No positions matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
