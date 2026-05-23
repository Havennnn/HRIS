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
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, restore, store as storeRoute, update as updateRoute } from '@/routes/departments';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, Plus, Pencil } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import type { DataTableActionsConfig, DataTableColumn } from 'piacore/components/DataTable.vue';
import DataTablePanel from 'piacore/components/DataTablePanel.vue';
import { useAuth } from 'piacore/composables/useAuth';
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

const { hasPermission } = useAuth();

const canCreateDepartment = computed(() => hasPermission('can-create-department'));
const canUpdateDepartment = computed(() => hasPermission('can-update-department'));
const canArchiveDepartment = computed(() => hasPermission('can-archive-department'));
const canRestoreDepartment = computed(() => hasPermission('can-restore-department'));

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
            { value: 'asc', key: 'name', label: 'Name (A-Z)' },
            { value: 'desc', key: 'name', label: 'Name (Z-A)' },
            { value: 'asc', key: 'created', label: 'Created Date (Oldest)' },
            { value: 'desc', key: 'created', label: 'Created Date (Newest)' },
        ],
    },
];

// ─── Modal State ───────────────────────────────────────
const showModal = ref(false);
const editingDepartment = ref<DepartmentResource | null>(null);
const isEditing = computed(() => editingDepartment.value !== null);

const form = useForm({
    name: '',
});

function openCreateModal(): void {
    editingDepartment.value = null;
    form.name = '';
    form.clearErrors();
    showModal.value = true;
}

function openEditModal(department: DepartmentResource): void {
    editingDepartment.value = department;
    form.name = department.name;
    form.clearErrors();
    showModal.value = true;
}

function closeModal(): void {
    showModal.value = false;
    editingDepartment.value = null;
    form.reset();
    form.clearErrors();
}

function submitForm(): void {
    if (isEditing.value) {
        form.patch(updateRoute({ department: editingDepartment.value!.id }).url, {
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

    if (!archivedTab && !canUpdateDepartment.value && !canArchiveDepartment.value) {
        return undefined;
    }

    if (archivedTab && !canRestoreDepartment.value) {
        return undefined;
    }

    return {
        deleteRoute: !archivedTab && canArchiveDepartment.value ? (row) => destroy({ department: (row as DepartmentResource).id }).url : undefined,
        restoreRoute: archivedTab && canRestoreDepartment.value ? (row) => restore({ department: (row as DepartmentResource).id }).url : undefined,
        destructiveAction: archivedTab ? 'restore' : 'delete',
        deleteConfirmMessage: 'Are you sure you want to delete this department?',
        restoreConfirmMessage: 'Are you sure you want to restore this department?',
        customActions: canUpdateDepartment.value && !archivedTab
            ? [
                {
                    key: 'edit',
                    label: '',
                    icon: Pencil,
                    onClick: (row) => openEditModal(row as DepartmentResource),
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
                        <Button v-if="canCreateDepartment" @click="openCreateModal">
                            <Plus class="mr-1 h-4 w-4" />
                            Add Department
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
                        empty-message="No departments matched your filters."
                        @page-change="handlePageChange"
                    />
                </CardContent>
            </Card>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog :open="showModal" @update:open="closeModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Edit Department' : 'Add Department' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Update the department name below.' : 'Enter the name for the new department.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm">
                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <Label for="dept-name">
                                Department Name
                                <span class="text-destructive ml-0.5">*</span>
                            </Label>
                            <Input
                                id="dept-name"
                                v-model="form.name"
                                placeholder="Enter department name"
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
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeModal" :disabled="form.processing">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : (isEditing ? 'Update Department' : 'Create Department') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
