<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/performance-reviews';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import ImportExportDialog from 'piacore/components/ImportExportDialog.vue';
import { useAuth } from 'piacore/composables/useAuth';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { computed, h, ref } from 'vue';
import type { PerformanceReviewIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Performance Reviews',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<PerformanceReviewIndexResource>;
    statuses?: { value: number; label: string }[];
}>();

const { hasPermission } = useAuth();

const canCreate = computed(() => hasPermission('can-create-performance-review'));
const canUpdate = computed(() => hasPermission('can-update-performance-review'));
const canArchive = computed(() => hasPermission('can-archive-performance-review'));
const canRestore = computed(() => hasPermission('can-restore-performance-review'));

const columns: DataTableColumn[] = [
    {
        key: 'employee',
        label: 'Employee',
        headerClass: 'min-w-[200px]',
        cell: ({ row }) => {
            const review = row as PerformanceReviewIndexResource;
            return h('span', { class: 'font-medium' }, review.employee?.name ?? '-');
        },
    },
    {
        key: 'reviewer',
        label: 'Reviewer',
        headerClass: 'min-w-[200px]',
        cell: ({ row }) => (row as PerformanceReviewIndexResource).reviewer?.name ?? '-',
    },
    {
        key: 'review_date',
        label: 'Review Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PerformanceReviewIndexResource).review_date,
    },
    {
        key: 'overall_score',
        label: 'Score',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PerformanceReviewIndexResource).overall_score ?? '-',
    },
    {
        key: 'status',
        label: 'Status',
        cell: ({ row }) => {
            const badge = (row as PerformanceReviewIndexResource).status;
            return badge ? h(DataBadge, { badge }) : '-';
        },
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PerformanceReviewIndexResource).created_at,
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
const searchPlaceholder = ref('Search performance reviews...');

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
        key: 'status',
        label: 'Status',
        options: (props.statuses ?? []).map(s => ({ value: s.value, label: s.label })),
    },
];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Reviews',
        widthClass: 'w-72',
        options: [
            { value: 'asc', key: 'review_date', label: 'Review Date (Oldest)' },
            { value: 'desc', key: 'review_date', label: 'Review Date (Newest)' },
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
        editRoute: canUpdate.value ? (row) => edit({ performance_review: (row as PerformanceReviewIndexResource).id }).url : undefined,
        deleteRoute: !archivedTab && canArchive.value ? (row) => destroy({ performance_review: (row as PerformanceReviewIndexResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestore.value ? (row) => restore({ performance_review: (row as PerformanceReviewIndexResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this performance review?',
        restoreConfirmMessage: 'Are you sure you want to restore this performance review?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Performance Reviews" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Performance Reviews"
                    description="Manage employee performance reviews with KPI scores and feedback."
                >
                    <template #actions>
                        <ImportExportDialog />
                        <Button v-if="canCreate" as-child>
                            <Link :href="create().url">Add Review</Link>
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
                        empty-message="No performance reviews matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
