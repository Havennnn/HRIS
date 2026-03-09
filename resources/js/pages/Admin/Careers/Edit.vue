<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getCareerById, getPositionOptions } from '@/dummy/DummyData';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/careers';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, DraftingCompass, Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import type { CareerEditResource } from './index';

const props = defineProps<{
    data?: CareerEditResource;
    positions?: Option[];
}>();

// Position options from dummy data
const positions = computed<Option[]>(() => getPositionOptions());

// Status options for is_active field
const statusOptions = computed<Option[]>(() => [
    { value: 'true', label: 'Active' },
    { value: 'false', label: 'Inactive' },
]);

// Get career data from dummy if no server data
const dummyCareerData = computed(() => {
    // If we have server data, use it
    if (props.data?.data) {
        return props.data.data;
    }
    // Otherwise, get from dummy data (using first dummy career as default)
    const dummyCareers = getPositionOptions();
    if (dummyCareers.length > 0) {
        const career = getCareerById(parseInt(dummyCareers[0].value));
        if (career) {
            return {
                id: career.id,
                position_id: career.id,
                position_name: career.position,
                description: career.description,
                is_active: career.is_active,
                status_value: career.is_active ? 1 : 0,
                status: {
                    label: career.is_active ? 'Active' : 'Inactive',
                    variant: career.is_active ? 'badge-success' : 'badge-secondary',
                },
                created_at: new Date().toISOString().split('T')[0],
            };
        }
    }
    return null;
});

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Career';

const careerData = computed(() => props.data?.data ?? dummyCareerData.value);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Careers', href: index().url },
    {
        title: 'Edit',
        href: careerData.value?.id ? edit({ career: careerData.value.id }).url : '#',
    },
]);

const form = useForm({
    position_id: careerData.value?.position_id ?? '',
    description: careerData.value?.description ?? '',
    is_active: careerData.value?.is_active ? 'true' : 'false',
});

// Computed to convert is_active string to boolean for display
const isActiveBoolean = computed(() => form.is_active === 'true');

// Default status for DataBadge
const defaultStatus = { label: 'Inactive', variant: 'badge-secondary' };
const careerStatus = computed(() => careerData.value?.status ?? defaultStatus);

const headerActions = computed(() => [
    {
        label: 'Back',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    },
    ...(!isEditing.value
        ? [{
            label: 'Set to Inactive',
            icon: DraftingCompass,
            variant: 'default',
            size: 'sm',
            onClick: () => (isEditing.value = true)
        }]
    : []),
    ...(!isEditing.value
        ? [{
            label: 'Edit',
            icon: Pencil,
            variant: 'default',
            size: 'sm',
            onClick: () => (isEditing.value = true)
        }]
    : []),
]);

const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const tabs = computed(() => [
    {
        key: 'default',
        label: 'Information'
    },
    {
        key: 'activity_logs',
        label: 'Activity Log'
    },
]);

function submit(): void {
    if (!careerData.value?.id) return;
    form.patch(update({ career: careerData.value.id }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="careerData" class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <DataHeader
                variant="form"
                :title="careerData?.position_name"
                subtitle="Career Position"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="careerStatus" />
                </template>
            </DataHeader>

            <!-- Tabs -->
            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                show-tabs
                :show-date-range="false"
                :show-search="false"
            />

            <!-- Activity Logs -->
            <div v-if="activeTab === 'activity_logs'" class="mx-auto w-full">
                <ActivityLogTable />
            </div>

            <!-- Information -->
            <div v-else class="mx-auto w-full max-w-4xl">
                <!-- VIEW MODE -->
                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Career Information</CardTitle>
                        <CardDescription>
                            Basic career position details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="space-y-2">
                            <Label>Position</Label>
                            <div
                                class="py-1 text-sm"
                            >
                                {{ careerData?.position_name }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Description</Label>
                            <div
                                class="py-1 text-sm whitespace-pre-wrap"
                            >
                                {{ careerData?.description || '-' }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Status</Label>
                            <div
                                class="py-1 text-sm"
                            >
                                {{ isActiveBoolean ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- EDIT MODE -->
                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit Career
                        </CardTitle>

                        <CardDescription>
                            Update career information.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <DataSelector
                                    id="position_id"
                                    v-model="form.position_id"
                                    :label="'Position'"
                                    :options="positions"
                                    placeholder="-- Select Position --"
                                    :error="form.errors.position_id"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Description</Label>
                                <Input
                                    v-model="form.description"
                                    type="textarea"
                                    class="min-h-[120px] resize-y"
                                />

                                <p
                                    v-if="form.errors.description"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <DataSelector
                                    id="is_active"
                                    v-model="form.is_active"
                                    :options="statusOptions"
                                    label="Status"
                                    placeholder="-- Select Status --"
                                    :error="form.errors.is_active"
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="isEditing = false"
                                >
                                    Cancel
                                </Button>

                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing ? 'Saving...' : 'Update'
                                    }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
