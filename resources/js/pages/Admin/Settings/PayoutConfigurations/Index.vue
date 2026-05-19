<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { Separator } from '@/components/ui/separator';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs';
import SettingsCard from '@/components/settings/SettingsCard.vue';
import SettingsCardItem from '@/components/settings/SettingsCardItem.vue';
import AdminSettingsLayout from '@/layouts/admin/settings/Layout.vue';
import payoutConfigurations from '@/routes/settings/payout-configurations/index';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Calendar, RotateCcw, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import { computed, ref } from 'vue';
import type { PayoutConfiguration } from '@/types/settings';

type ActivityLogRow = Record<string, unknown>;

const props = defineProps<{
    configs: PayoutConfiguration[];
    activity_logs?: { data?: ActivityLogRow[] };
}>();

const breadcrumbs = computed(() => [
    { title: 'Settings', href: '/admin/settings' },
    { title: 'Payout Configurations', href: payoutConfigurations.index().url },
]);

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'information';
});

const resetForm = useForm({});

function resetDefaults(): void {
    if (window.confirm('Reset both payout configurations to their default values? This cannot be undone.')) {
        resetForm.post(payoutConfigurations.reset().url);
    }
}

const editingConfig = ref<PayoutConfiguration | null>(null);
const editDialogOpen = ref(false);

const editForm = useForm({
    name: '',
    period_start_day: '',
    period_end_day: '',
    period_end_is_last_day: false,
    cutoff_generation_day: '',
    cutoff_disburse_day: '',
    disburse_is_last_day: false,
    assumed_from_day: '',
    is_active: false,
});

function openEditDialog(config: PayoutConfiguration): void {
    editingConfig.value = config;
    editForm.name = config.name;
    editForm.period_start_day = String(config.period_start_day);
    editForm.period_end_day = String(config.period_end_day ?? '');
    editForm.period_end_is_last_day = config.period_end_is_last_day;
    editForm.cutoff_generation_day = String(config.cutoff_generation_day);
    editForm.cutoff_disburse_day = String(config.cutoff_disburse_day ?? '');
    editForm.disburse_is_last_day = config.disburse_is_last_day;
    editForm.assumed_from_day = String(config.assumed_from_day);
    editForm.is_active = config.is_active;
    editForm.clearErrors();
    editDialogOpen.value = true;
}

function closeEditDialog(): void {
    editDialogOpen.value = false;
    editingConfig.value = null;
}

function saveEdit(): void {
    if (!editingConfig.value) {
        return;
    }

    editForm.patch(
        payoutConfigurations.update({ payoutConfiguration: editingConfig.value.id }).url,
        {
            onSuccess: () => {
                closeEditDialog();
            },
        },
    );
}

function formatPeriodDescription(config: PayoutConfiguration): string {
    const start = config.period_start_day;
    const end = config.period_end_is_last_day
        ? 'last day'
        : config.period_end_day;
    const cutoff = config.cutoff_generation_day;
    const disburse = config.disburse_is_last_day
        ? 'last day'
        : config.cutoff_disburse_day;

    return `Period: ${start}${getDaySuffix(start)} – ${end}. Cutoff on ${cutoff}${getDaySuffix(cutoff)}, disburse on ${disburse}${typeof disburse === 'number' ? getDaySuffix(disburse) : ''}.`;
}

function getDaySuffix(day: number | string | null | undefined): string {
    if (day === null || day === undefined || day === '') {
        return '';
    }

    const d = Number(day);
    if (Number.isNaN(d)) {
        return '';
    }

    if (d >= 11 && d <= 13) {
        return 'th';
    }

    switch (d % 10) {
        case 1: return 'st';
        case 2: return 'nd';
        case 3: return 'rd';
        default: return 'th';
    }
}

function isFormDirty(): boolean {
    if (!editingConfig.value) {
        return false;
    }

    const original = editingConfig.value;

    return (
        editForm.name !== original.name ||
        Number(editForm.period_start_day) !== original.period_start_day ||
        String(editForm.period_end_day) !== String(original.period_end_day ?? '') ||
        Boolean(editForm.period_end_is_last_day) !== original.period_end_is_last_day ||
        Number(editForm.cutoff_generation_day) !== original.cutoff_generation_day ||
        String(editForm.cutoff_disburse_day) !== String(original.cutoff_disburse_day ?? '') ||
        Boolean(editForm.disburse_is_last_day) !== original.disburse_is_last_day ||
        Number(editForm.assumed_from_day) !== original.assumed_from_day ||
        Boolean(editForm.is_active) !== original.is_active
    );
}
</script>

<template>
    <Head title="Payout Configurations" />

    <AdminSettingsLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight">Payout Configurations</h2>
                    <p class="text-muted-foreground text-sm">
                        Configure payroll cutoff and disbursement schedules.
                    </p>
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    :disabled="resetForm.processing"
                    @click="resetDefaults"
                >
                    <RotateCcw class="mr-1 h-4 w-4" />
                    {{ resetForm.processing ? 'Resetting...' : 'Reset Defaults' }}
                </Button>
            </div>

            <!-- Tabs -->
            <Tabs :default-value="activeTab" class="w-full">
                <TabsList>
                    <TabsTrigger value="information" as-child>
                        <a :href="`${payoutConfigurations.index().url}?tab=information`">Information</a>
                    </TabsTrigger>
                    <TabsTrigger value="activity_logs" as-child>
                        <a :href="`${payoutConfigurations.index().url}?tab=activity_logs`">Activity Logs</a>
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="information" class="space-y-6 pt-4">
                    <SettingsCard
                        title="Payout Schedules"
                        description="Configure payroll period ranges, cutoff dates, and disbursement schedules."
                    >
                        <SettingsCardItem
                            v-for="config in props.configs"
                            :key="config.id"
                            :icon="Calendar"
                            :label="config.name"
                            :description="formatPeriodDescription(config)"
                            action-label="Edit"
                            @action="openEditDialog(config)"
                        />
                    </SettingsCard>
                </TabsContent>

                <TabsContent value="activity_logs" class="pt-4">
                    <ActivityLogTable
                        :rows="activity_logs?.data ?? []"
                        empty-message="No activity logs found for payout configurations."
                    />
                </TabsContent>
            </Tabs>
        </div>
    </AdminSettingsLayout>

    <!-- Edit Dialog -->
    <Dialog :open="editDialogOpen" @update:open="(v: boolean) => !v && closeEditDialog()">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Edit {{ editingConfig?.name }}</DialogTitle>
                <DialogDescription>
                    Update the payout schedule configuration.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6 py-4">
                <!-- Name -->
                <div class="space-y-2">
                    <Label for="edit-name">Name</Label>
                    <Input
                        id="edit-name"
                        v-model="editForm.name"
                        :disabled="editForm.processing"
                    />
                    <p v-if="editForm.errors.name" class="text-destructive text-xs font-medium">
                        {{ editForm.errors.name }}
                    </p>
                </div>

                <Separator />

                <!-- Period Start / End -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-period-start">Period Start</Label>
                        <Input
                            id="edit-period-start"
                            v-model="editForm.period_start_day"
                            type="number"
                            min="1"
                            max="31"
                            :disabled="editForm.processing"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-period-end">Period End</Label>
                        <Input
                            id="edit-period-end"
                            v-model="editForm.period_end_day"
                            type="number"
                            min="1"
                            max="31"
                            :disabled="editForm.processing || editForm.period_end_is_last_day"
                        />
                    </div>

                    <div class="flex items-end pb-2">
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="edit-period-end-last"
                                :checked="editForm.period_end_is_last_day"
                                :disabled="editForm.processing"
                                @update:checked="editForm.period_end_is_last_day = !!$event"
                            />
                            <Label for="edit-period-end-last">Last day</Label>
                        </div>
                    </div>
                </div>

                <!-- Cutoff / Disbursement -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-cutoff-gen">Cutoff Generation</Label>
                        <Input
                            id="edit-cutoff-gen"
                            v-model="editForm.cutoff_generation_day"
                            type="number"
                            min="1"
                            max="31"
                            :disabled="editForm.processing"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-cutoff-disburse">Disbursement</Label>
                        <Input
                            id="edit-cutoff-disburse"
                            v-model="editForm.cutoff_disburse_day"
                            type="number"
                            min="1"
                            max="31"
                            :disabled="editForm.processing || editForm.disburse_is_last_day"
                        />
                    </div>

                    <div class="flex items-end pb-2">
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="edit-disburse-last"
                                :checked="editForm.disburse_is_last_day"
                                :disabled="editForm.processing"
                                @update:checked="editForm.disburse_is_last_day = !!$event"
                            />
                            <Label for="edit-disburse-last">Last day</Label>
                        </div>
                    </div>
                </div>

                <!-- Assumed From / Active -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-assumed-from">Assume Attendance From Day</Label>
                        <Input
                            id="edit-assumed-from"
                            v-model="editForm.assumed_from_day"
                            type="number"
                            min="1"
                            max="31"
                            :disabled="editForm.processing"
                        />
                    </div>

                    <div class="flex items-end pb-2">
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="edit-active"
                                :checked="editForm.is_active"
                                :disabled="editForm.processing"
                                @update:checked="editForm.is_active = !!$event"
                            />
                            <Label for="edit-active">Active</Label>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button
                    variant="outline"
                    :disabled="editForm.processing"
                    @click="closeEditDialog"
                >
                    Cancel
                </Button>
                <Button
                    :disabled="editForm.processing || !isFormDirty()"
                    @click="saveEdit"
                >
                    <Save class="mr-1 h-4 w-4" />
                    {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
