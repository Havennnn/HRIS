<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AdminSettingsLayout from '@/layouts/admin/settings/Layout.vue';
import { create, destroy, edit, index, restore } from '@/routes/settings/kpis';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import { useAuth } from 'piacore/composables/useAuth';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { computed, h, ref } from 'vue';
import type { KpiIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'KPIs',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<KpiIndexResource>;
}>();

const { hasPermission } = useAuth();

const canCreate = computed(() => hasPermission('can-create-kpi'));
const canUpdate = computed(() => hasPermission('can-update-kpi'));
const canArchive = computed(() => hasPermission('can-archive-kpi'));
const canRestore = computed(() => hasPermission('can-restore-kpi'));

const columns: DataTableColumn[] = [
    {
        key: 'name',
        label: 'KPI Name',
        headerClass: 'min-w-[240px]',
        cell: ({ row }) => {
            const kpi = row as KpiIndexResource;
            return h('div', { class: 'font-medium' }, kpi.name);
        },
    },
    {
        key: 'description',
        label: 'Description',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as KpiIndexResource).description ?? '-',
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as KpiIndexResource).created_at,
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
const searchPlaceholder = ref('Search KPIs...');

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
        menuLabel: 'Sort KPIs',
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

    if (!archivedTab && !canUpdate.value && !canArchive.value) {
        return undefined;
    }

    if (archivedTab && !canRestore.value) {
        return undefined;
    }

    return {
        editRoute: canUpdate.value ? (row) => edit({ kpi: (row as KpiIndexResource).id }).url : undefined,
        deleteRoute: !archivedTab && canArchive.value ? (row) => destroy({ kpi: (row as KpiIndexResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestore.value ? (row) => restore({ kpi: (row as KpiIndexResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this KPI?',
        restoreConfirmMessage: 'Are you sure you want to restore this KPI?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="KPIs" />
    <AdminSettingsLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="KPIs"
                    description="Manage Key Performance Indicators used for performance reviews."
                >
                    <template #actions>
                        <Button v-if="canCreate" as-child>
                            <Link :href="create().url">Add KPI</Link>
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
                        empty-message="No KPIs matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AdminSettingsLayout>
</template>
