<script setup lang="ts">
import { Button } from '@/components/ui/button';
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
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs';
import SettingsCard from 'piacore/components/SettingsCard.vue';
import SettingsCardItem from 'piacore/components/SettingsCardItem.vue';
import AdminSettingsLayout from '@/layouts/admin/settings/Layout.vue';
import { create, destroy, index, restore, store, update } from '@/routes/settings/kpis';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ClipboardList, Plus, Save, Trash2 } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import { computed, ref } from 'vue';
import type { KpiIndexResource } from './index';

type ActivityLogRow = Record<string, unknown>;

const props = defineProps<{
    data: { data: KpiIndexResource[]; total?: number };
    activity_logs?: { data?: ActivityLogRow[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'KPIs', href: index().url },
];

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'information';
});

// ─── Create Dialog ─────────────────────────────────
const createDialogOpen = ref(false);
const createForm = useForm({
    name: '',
    description: '',
});

function openCreateDialog(): void {
    createForm.reset();
    createForm.clearErrors();
    createDialogOpen.value = true;
}

function submitCreate(): void {
    createForm.post(store().url, {
        onSuccess: () => {
            createDialogOpen.value = false;
            createForm.reset();
        },
    });
}

// ─── Edit Dialog ───────────────────────────────────
const editingKpi = ref<KpiIndexResource | null>(null);
const editDialogOpen = ref(false);
const editForm = useForm({
    name: '',
    description: '',
});

function openEditDialog(kpi: KpiIndexResource): void {
    editingKpi.value = kpi;
    editForm.name = kpi.name;
    editForm.description = kpi.description ?? '';
    editForm.clearErrors();
    editDialogOpen.value = true;
}

function submitEdit(): void {
    if (!editingKpi.value) return;
    editForm.patch(update({ kpi: editingKpi.value.id }).url, {
        onSuccess: () => {
            editDialogOpen.value = false;
            editingKpi.value = null;
        },
    });
}

function closeEditDialog(): void {
    editDialogOpen.value = false;
    editingKpi.value = null;
}

// ─── Delete ────────────────────────────────────────
function confirmDelete(kpi: KpiIndexResource): void {
    if (window.confirm(`Are you sure you want to delete "${kpi.name}"?`)) {
        const form = useForm({});
        form.delete(destroy({ kpi: kpi.id }).url);
    }
}
</script>

<template>
    <Head title="KPIs" />

    <AdminSettingsLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight">KPIs</h2>
                    <p class="text-muted-foreground text-sm">
                        Define Key Performance Indicators used in performance reviews.
                    </p>
                </div>

                <Button size="sm" @click="openCreateDialog">
                    <Plus class="mr-1 h-4 w-4" />
                    Add KPI
                </Button>
            </div>

            <!-- Tabs -->
            <Tabs :default-value="activeTab" class="w-full">
                <TabsList>
                    <TabsTrigger value="information" as-child>
                        <a :href="`${index().url}?tab=information`">Information</a>
                    </TabsTrigger>
                    <TabsTrigger value="activity_logs" as-child>
                        <a :href="`${index().url}?tab=activity_logs`">Activity Logs</a>
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="information" class="space-y-6 pt-4">
                    <SettingsCard
                        title="Key Performance Indicators"
                        description="Manage the KPIs used to evaluate employee performance."
                    >
                        <SettingsCardItem
                            v-for="kpi in props.data?.data ?? []"
                            :key="kpi.id"
                            :icon="ClipboardList"
                            :label="kpi.name"
                            :description="kpi.description ?? undefined"
                            action-label="Edit"
                            @action="openEditDialog(kpi)"
                        />
                        <div
                            v-if="!props.data?.data?.length"
                            class="flex flex-col items-center justify-center py-8 text-center"
                        >
                            <ClipboardList class="mb-2 h-8 w-8 text-muted-foreground/50" />
                            <p class="text-sm text-muted-foreground">
                                No KPIs defined yet. Click "Add KPI" to create your first one.
                            </p>
                        </div>
                    </SettingsCard>
                </TabsContent>

                <TabsContent value="activity_logs" class="pt-4">
                    <ActivityLogTable
                        :rows="activity_logs?.data ?? []"
                        empty-message="No activity logs found for KPIs."
                    />
                </TabsContent>
            </Tabs>
        </div>
    </AdminSettingsLayout>

    <!-- Create Dialog -->
    <Dialog :open="createDialogOpen" @update:open="createDialogOpen = $event">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Add KPI</DialogTitle>
                <DialogDescription>
                    Create a new Key Performance Indicator for performance reviews.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4 py-2" @submit.prevent="submitCreate">
                <div class="space-y-2">
                    <Label for="kpi-name">
                        KPI Name
                        <span class="text-destructive ml-0.5">*</span>
                    </Label>
                    <Input
                        id="kpi-name"
                        v-model="createForm.name"
                        placeholder="e.g. Sales Target Achievement"
                        required
                        :disabled="createForm.processing"
                    />
                    <p v-if="createForm.errors.name" class="text-destructive text-xs font-medium">
                        {{ createForm.errors.name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="kpi-description">Description</Label>
                    <textarea
                        id="kpi-description"
                        v-model="createForm.description"
                        placeholder="Optional description of this KPI"
                        :disabled="createForm.processing"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    ></textarea>
                </div>

                <DialogFooter class="pt-2">
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="createForm.processing"
                        @click="createDialogOpen = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :disabled="createForm.processing || !createForm.name"
                    >
                        <Save class="mr-1 h-4 w-4" />
                        {{ createForm.processing ? 'Creating...' : 'Create KPI' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Edit Dialog -->
    <Dialog :open="editDialogOpen" @update:open="(v: boolean) => !v && closeEditDialog()">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Edit {{ editingKpi?.name }}</DialogTitle>
                <DialogDescription>
                    Update the KPI details.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4 py-2" @submit.prevent="submitEdit">
                <div class="space-y-2">
                    <Label for="edit-kpi-name">
                        KPI Name
                        <span class="text-destructive ml-0.5">*</span>
                    </Label>
                    <Input
                        id="edit-kpi-name"
                        v-model="editForm.name"
                        required
                        :disabled="editForm.processing"
                    />
                    <p v-if="editForm.errors.name" class="text-destructive text-xs font-medium">
                        {{ editForm.errors.name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="edit-kpi-description">Description</Label>
                    <textarea
                        id="edit-kpi-description"
                        v-model="editForm.description"
                        :disabled="editForm.processing"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    ></textarea>
                </div>

                <DialogFooter class="flex items-center justify-between pt-2">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        class="text-destructive"
                        :disabled="editForm.processing"
                        @click="editingKpi && confirmDelete(editingKpi)"
                    >
                        <Trash2 class="mr-1 h-4 w-4" />
                        Delete
                    </Button>
                    <div class="flex gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="editForm.processing"
                            @click="closeEditDialog"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="editForm.processing"
                        >
                            <Save class="mr-1 h-4 w-4" />
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
