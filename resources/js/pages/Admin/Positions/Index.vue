<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useRoleAccess } from '@/composables/useRoleAccess';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, restore, store as storeRoute, update as updateRoute } from '@/routes/positions';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Briefcase, Pencil, Plus } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import ImportExportDialog from 'piacore/components/ImportExportDialog.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
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
const { HR_FINANCE_ROLES, canAccessRoles } = useRoleAccess();
const isHR_FINANCE = computed(() => canAccessRoles(HR_FINANCE_ROLES));

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
    ...(isHR_FINANCE.value
        ? [
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
        ]
    : []),
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

// ─── Modal State ───────────────────────────────────────
const showModal = ref(false);
const editingPosition = ref<PositionResource | null>(null);
const isEditing = computed(() => editingPosition.value !== null);

const form = useForm({
    department_id: '',
    name: '',
    salary: '',
    allowance: '',
    level: '',
});

function openCreateModal(): void {
    editingPosition.value = null;
    form.department_id = '';
    form.name = '';
    form.salary = '';
    form.allowance = '';
    form.level = '';
    form.clearErrors();
    showModal.value = true;
}

function openEditModal(position: PositionResource): void {
    editingPosition.value = position;
    form.department_id = String(position.department_id ?? '');
    form.name = position.name;
    form.salary = position.salary?.toString() ?? '';
    form.allowance = position.allowance?.toString() ?? '';
    form.level = position.level ?? '';
    form.clearErrors();
    showModal.value = true;
}

function closeModal(): void {
    showModal.value = false;
    editingPosition.value = null;
    form.reset();
    form.clearErrors();
}

function submitForm(): void {
    if (isEditing.value) {
        form.patch(updateRoute({ position: editingPosition.value!.id }).url, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(storeRoute().url, {
            onSuccess: () => closeModal(),
        });
    }
}

// ─── Table Actions ─────────────────────────────────────
const tableActions = computed<DataTableActionsConfig | undefined>(() => {
    const archivedTab = activeTab.value === 'archived';

    if (!archivedTab && !canUpdatePosition.value && !canArchivePosition.value) {
        return undefined;
    }

    if (archivedTab && !canRestorePosition.value) {
        return undefined;
    }

    return {
        deleteRoute: !archivedTab && canArchivePosition.value ? (row) => destroy({ position: (row as PositionResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestorePosition.value ? (row) => restore({ position: (row as PositionResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this position?',
        restoreConfirmMessage: 'Are you sure you want to restore this position?',
        customActions: canUpdatePosition.value && !archivedTab
            ? [
                {
                    key: 'edit',
                    label: '',
                    icon: Pencil,
                    onClick: (row) => openEditModal(row as PositionResource),
                },
            ]
            : undefined,
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
                        <ImportExportDialog
                            :can-import="hasPermission('can-import-positions')"
                            :can-export="hasPermission('can-export-data')"
                        />
                        <Button v-if="canCreatePosition" @click="openCreateModal">
                            <Plus class="mr-1 h-4 w-4" />
                            Add Position
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

        <!-- Create/Edit Modal -->
        <Dialog :open="showModal" @update:open="closeModal">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Edit Position' : 'Add Position' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Update the position details below.' : 'Enter the details for the new position.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm">
                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <DataSelector
                                id="position-department"
                                :label="'Department'"
                                v-model="form.department_id"
                                :options="departments ?? []"
                                required
                                placeholder="-- Select a Department --"
                                :error="form.errors.department_id"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="position-name">
                                Position Name
                                <span class="text-destructive ml-0.5">*</span>
                            </Label>
                            <Input
                                id="position-name"
                                v-model="form.name"
                                placeholder="Enter position name"
                                required
                                :disabled="form.processing"
                                :aria-invalid="form.errors.name ? 'true' : undefined"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-destructive text-xs font-medium"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="position-level">Level</Label>
                                <Input
                                    id="position-level"
                                    v-model="form.level"
                                    placeholder="e.g., Junior"
                                    :disabled="form.processing"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="position-salary">Salary</Label>
                                <Input
                                    id="position-salary"
                                    v-model="form.salary"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    :disabled="form.processing"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="position-allowance">Allowance</Label>
                                <Input
                                    id="position-allowance"
                                    v-model="form.allowance"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    :disabled="form.processing"
                                />
                            </div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeModal" :disabled="form.processing">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : (isEditing ? 'Update Position' : 'Create Position') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
