<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { useRoleAccess } from '@/composables/useRoleAccess';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, exportMethod, index, importMethod, manifest, restore } from '@/routes/employees';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Badge, Briefcase, Download, IdCardIcon, Upload } from 'lucide-vue-next';
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

const { HR_FINANCE_ROLES, ALL_BUSINESS_ROLES, canAccessRoles } = useRoleAccess();
const { hasPermission } = useAuth();

const canCreateEmployee = computed(() => hasPermission('can-create-employee'));
const canEditEmployee = computed(() => canAccessRoles(HR_FINANCE_ROLES));
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
        showRoute: canEditEmployee.value ? (row) => edit({ employee: (row as EmployeeIndexResource).id }).url : undefined,
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

// Import/Export
const showImportDialog = ref(false);
const importFile = ref<File | null>(null);
const importing = ref(false);
const importResult = computed(() => (page.props as any).importResult ?? null);

function onImportFileChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    importFile.value = target.files?.[0] ?? null;
}

async function submitImport(): Promise<void> {
    if (!importFile.value) return;

    importing.value = true;
    try {
        router.post(
            importMethod().url,
            { file: importFile.value },
            {
                preserveState: true,
                onSuccess: () => {
                    showImportDialog.value = false;
                    importFile.value = null;
                },
                onError: () => {
                    // FormRequest validation errors handled by Inertia
                },
                onFinish: () => {
                    importing.value = false;
                },
            },
        );
    } catch {
        importing.value = false;
    }
}

const canImport = computed(() => hasPermission('can-import-employees'));
const canExport = computed(() => hasPermission('can-export-data'));
const validationErrors = computed(() => (page.props as any).errors ?? {});
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
                        <Button v-if="canImport" variant="outline" @click="showImportDialog = true">
                            <Upload class="mr-2 h-4 w-4" />
                            Import
                        </Button>
                        <Button v-if="canExport" variant="outline" as-child>
                            <a :href="exportMethod().url" download>
                                <Download class="mr-2 h-4 w-4" />
                                Export
                            </a>
                        </Button>
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

            <!-- Import Dialog -->
            <Dialog :open="showImportDialog" @update:open="showImportDialog = $event">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Import Employees</DialogTitle>
                        <DialogDescription>
                            Upload a .csv or .xlsx file. Download the template first to see the required columns. Rows are matched by email — existing records are updated, new ones are created.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div v-if="importResult" class="rounded-lg border p-4" :class="importResult.errors?.length ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'">
                            <p class="text-sm font-medium" :class="importResult.errors?.length ? 'text-red-800' : 'text-green-800'">
                                Imported {{ importResult.success }} record(s).
                                <span v-if="importResult.errors?.length"> {{ Object.keys(importResult.errors).length }} error(s).</span>
                            </p>
                            <div v-if="importResult.errors?.length" class="mt-2 max-h-32 overflow-y-auto space-y-1">
                                <p v-for="(msg, row) in importResult.errors" class="text-xs text-red-700">
                                    Row {{ row }}: {{ msg }}
                                </p>
                            </div>
                        </div>
                        <div v-if="Object.keys(validationErrors).length" class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-medium text-red-800">Validation errors:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                <li v-for="(msgs, field) in validationErrors" class="text-xs text-red-700">
                                    {{ Array.isArray(msgs) ? msgs.join(', ') : msgs }}
                                </li>
                            </ul>
                        </div>
                        <div class="rounded-lg border border-dashed p-6 text-center">
                            <Upload class="mx-auto mb-2 h-8 w-8 text-muted-foreground" />
                            <p class="mb-1 text-sm font-medium">Choose a CSV or Excel file</p>
                            <p class="mb-3 text-xs text-muted-foreground">.csv or .xlsx, max 5MB</p>
                            <input
                                type="file"
                                accept=".csv,.xlsx"
                                class="block w-full text-sm file:mr-4 file:rounded file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:text-primary-foreground hover:file:bg-primary/90"
                                @change="onImportFileChange"
                            />
                        </div>

                        <div class="text-center">
                            <a
                                :href="manifest().url"
                                class="text-sm text-primary underline-offset-4 hover:underline"
                                download
                            >
                                Download template (.xlsx)
                            </a>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="showImportDialog = false">Cancel</Button>
                        <Button :disabled="!importFile || importing" @click="submitImport">
                            {{ importing ? 'Importing...' : 'Import' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
