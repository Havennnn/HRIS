<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { draft, index, publish } from '@/routes/pages';
import type { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { PageIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pages',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<PageIndexResource>;
    statuses?: Option[];
}>();

function togglePageStatus(row: PageIndexResource): void {
    if (row.status_value === 1) {
        router.post(draft({ page: row.id }).url, {}, { preserveScroll: true });
    } else {
        router.post(publish({ page: row.id }).url, {}, { preserveScroll: true });
    }
}

const columns: DataTableColumn[] = [
    {
        key: 'title',
        label: 'Title',
        headerClass: 'min-w-60',
        cell: ({ row }) => {
            const page = row as PageIndexResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium capitalize' }, page.title),
                h('span', { class: 'text-muted-foreground text-xs' }, `/${page.slug}`),
            ]);
        },
    },
    {
        key: 'template',
        label: 'Template',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PageIndexResource).template,
    },
    {
        key: 'status',
        label: 'Status',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as PageIndexResource).status,
        }),
    },
    {
        key: 'created_at',
        label: 'Created',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as PageIndexResource).created_at,
    },
    {
        key: 'published',
        label: 'Toggle',
        headerClass: 'text-center',
        cellClass: 'text-center',
        cell: ({ row }) => {
            const page = row as PageIndexResource;
            const isPublished = page.status_value === 1;
            const isArchived = activeTab.value === 'archived';

            return h('div', { class: 'flex justify-center' }, [
                h('button', {
                    type: 'button',
                    role: 'switch',
                    disabled: isArchived,
                    'aria-checked': isPublished,
                    onClick: () => togglePageStatus(page),
                    class: isArchived
                        ? 'inline-flex h-5 w-9 items-center rounded-full border-2 border-transparent opacity-50 cursor-not-allowed bg-input'
                        : `inline-flex h-5 w-9 items-center rounded-full border-2 border-transparent transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background ${
                            isPublished
                                ? 'bg-primary'
                                : 'bg-input'
                        }`,
                }, [
                    h('span', {
                        'aria-hidden': 'true',
                        class: `pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform ${
                            isPublished ? 'translate-x-4' : 'translate-x-0'
                        }`,
                    }),
                ]),
            ]);
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
const searchPlaceholder = ref('Search pages...');

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
        key: 'status',
        label: 'Status',
        icon: Badge,
        options: props.statuses ?? [],
    },
];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Pages',
        widthClass: 'w-44',
        options: [
            { value: 'asc', key: 'title', label: 'Title (A-Z)' },
            { value: 'desc', key: 'title', label: 'Title (Z-A)' },
            { value: 'asc', key: 'created', label: 'Created (Oldest)' },
            { value: 'desc', key: 'created', label: 'Created (Newest)' },
        ],
    },
];

function getRowId(row: Record<string, unknown>): number {
    return (row as PageIndexResource).id;
}

const tableActions = computed<DataTableActionsConfig | undefined>(() => ({
    showRoute: (row) => `/pages/${getRowId(row)}/edit`,
}));

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Pages" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Pages"
                    description="Manage CMS pages for your website."
                />

                <CardContent class="space-y-4 -mt-3">
                    <DataTablePanel
                        :tabs="tabs"
                        layout="inline"
                        :active-tab="activeTab"
                        :activity-log="true"
                        :search-placeholder="searchPlaceholder"
                        :search-query="search"
                        :filters="filters"
                        :sorts="sorts"
                        :show-date-range="false"
                        :columns="columns"
                        :paginated="data"
                        :actions="tableActions"
                        row-key="id"
                        empty-message="No pages found. Run php artisan cms:sync to seed pages from config."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
