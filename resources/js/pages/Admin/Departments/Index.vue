<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/departments';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTable from 'piacore/components/DataTable.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { computed, h, ref } from 'vue';
import type { DepartmentResource } from './Departments';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<DepartmentResource>;
}>();

const columns: DataTableColumn[] = [
    {
        key: 'name',
        label: 'Department',
        headerClass: 'min-w-[240px]',
        cell: ({ row }) => {
            const department = row as DepartmentResource;
            return h('div', { class: 'flex items-center gap-3' }, [
                h('div', { class: 'flex flex-col' }, [
                    h('span', { class: 'font-medium' }, department.name),
                ]),
            ]);
        },
    },
    {
        key: 'positions_count',
        label: 'Positions',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as DepartmentResource).positions_count,
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as DepartmentResource).created_at,
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
const searchPlaceholder = ref('Search departments...');

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

const filters = [];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Departments',
        widthClass: 'w-64',
        options: [
            { value: 'asc', key:'name', label: 'Name (A-Z)' },
            { value: 'desc', key:'name', label: 'Name (Z-A)' },
            { value: 'asc', key:'created', label: 'Created Date (Oldest)' },
            { value: 'desc', key:'created', label: 'Created Date (Newest)' },
        ],
    },
];

const tableActions = computed<DataTableActionsConfig>(() => ({
    editRoute: (row) => edit({ department: (row as DepartmentResource).id }).url,
    deleteRoute: (row) => destroy({ department: (row as DepartmentResource).id }).url,
    restoreRoute: (row) => restore({ department: (row as DepartmentResource).id }).url,
    destructiveAction: activeTab.value === 'archived' ? 'restore' : 'delete',
    deleteConfirmMessage: 'Are you sure you want to delete this department?',
    restoreConfirmMessage: 'Are you sure you want to restore this department?',
}));

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}

</script>

<template>
    <Head title="Departments" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Departments"
                    description="Manage departments within the organization."
                >
                    <template #actions>
                        <Button as-child>
                            <Link :href="create().url">Add Department</Link>
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
                        :show-date-range="false"
                        :search-placeholder="searchPlaceholder"
                    />

                    <DataTable
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        :active-tab="activeTab"
                        row-key="id"
                        empty-message="No departments matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
