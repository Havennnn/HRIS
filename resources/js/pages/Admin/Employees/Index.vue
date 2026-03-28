<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useRoleAccess } from '@/composables/useRoleAccess';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/employees';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge, Briefcase, IdCardIcon } from 'lucide-vue-next';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import { useAuth } from 'piacore/composables/useAuth';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import type { Option } from 'piacore/Interface/Selector';
import { computed, h, ref } from 'vue';
import type { EmployeeIndexResource } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: index().url,
    },
];

const props = defineProps<{
    data: PaginatedData<EmployeeIndexResource>;
    positions?: Option[];
    statuses?: Option[];
    types?: Option[];
}>();

const { ALL_BUSINESS_ROLES, canAccessRoles } = useRoleAccess();
const { hasPermission } = useAuth();

const canCreateEmployee = computed(() => hasPermission('can-create-employee'));
const canEditEmployee = computed(() => canAccessRoles(ALL_BUSINESS_ROLES));
const canArchiveEmployee = computed(() => hasPermission('can-archive-employee'));
const canRestoreEmployee = computed(() => hasPermission('can-restore-employee'));

const columns: DataTableColumn[] = [
    {
        key: 'full_name',
        label: 'Name',
        headerClass: 'min-w-70',
        cell: ({ row }) => {
            const employee = row as EmployeeIndexResource;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-medium capitalize' }, employee.full_name),
                h('span', { class: 'text-muted-foreground' }, employee.email),
            ]);
        },
    },
    {
        key: 'position',
        label: 'Position',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as EmployeeIndexResource).position,
    },
    {
        key: 'department',
        label: 'Department',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as EmployeeIndexResource).department,
    },
    {
        key: 'mobile_number',
        label: 'Mobile',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as EmployeeIndexResource).mobile_number,
    },
    {
        key: 'type',
        label: 'Type',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as EmployeeIndexResource).type,
    },
    {
        key: 'status',
        label: 'Status',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => h(DataBadge, {
            badge: (row as EmployeeIndexResource).status,
        }),
    },
    {
        key: 'created_at',
        label: 'Created Date',
        cellClass: 'text-muted-foreground',
        cell: ({ row }) => (row as EmployeeIndexResource).created_at,
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
const searchPlaceholder = ref('Search employees...');

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
        icon: Briefcase,
        options: props.positions ?? [],
    },
    {
        key: 'status',
        label: 'Status',
        icon: Badge,
        options: props.statuses ?? [],
    },
    {
        key: 'type',
        label: 'Type',
        icon: IdCardIcon,
        options: props.types ?? [],
    },
];

const sorts = [
    {
        label: 'Sort',
        icon: ArrowUpDown,
        menuLabel: 'Sort Employees',
        widthClass: 'w-44',
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

    if (!archivedTab && !canEditEmployee.value && !canArchiveEmployee.value) {
        return undefined;
    }

    if (archivedTab && !canRestoreEmployee.value) {
        return undefined;
    }

    return {
        editRoute: canEditEmployee.value ? (row) => edit({ employee: (row as EmployeeIndexResource).id }).url : undefined,
        deleteRoute: !archivedTab && canArchiveEmployee.value ? (row) => destroy({ employee: (row as EmployeeIndexResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestoreEmployee.value ? (row) => restore({ employee: (row as EmployeeIndexResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this employee?',
        restoreConfirmMessage: 'Are you sure you want to restore this employee?',
    };
});

function handlePageChange(url: string | null): void {
    if (url) {
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Employees" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Employees"
                    description="Manage employees within the organization."
                >
                    <template #actions>
                        <Button v-if="canCreateEmployee" as-child>
                            <Link :href="create().url">Add Employee</Link>
                        </Button>
                    </template>
                </DataHeader>

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
                        empty-message="No employees matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
