<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/careers';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import type { CareerEditResource } from './index';

const props = defineProps<{
    data: CareerEditResource;
    positions?: Option[];
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Career';

const careerData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Careers', href: index().url },
    {
        title: 'Edit',
        href: careerData.value?.id ? edit({ career: careerData.value.id }).url : '#',
    },
]);

const form = useForm({
    position_id: careerData.value?.position_id?.toString() ?? '',
    description: careerData.value?.description ?? '',
    is_active: careerData.value?.is_active ? 'true' : 'false',
});

// Status options for is_active field
const statusOptions = computed<Option[]>(() => [
    { value: 'true', label: 'Active' },
    { value: 'false', label: 'Inactive' },
]);

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
                :title="careerData?.position"
                subtitle="Career Position"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <span
                        :class="[
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
                            careerData?.is_active
                                ? 'bg-green-100 text-green-800'
                                : 'bg-gray-100 text-gray-800'
                        ]"
                    >
                        {{ careerData?.is_active ? 'Active' : 'Inactive' }}
                    </span>
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
                            <div class="py-1 text-sm">
                                {{ careerData?.position ?? '-' }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Description</Label>
                            <div class="py-1 text-sm whitespace-pre-wrap">
                                {{ careerData?.description || '-' }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Created Date</Label>
                            <div class="py-1 text-sm">
                                {{ careerData?.created_at ?? '-' }}
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
                                    :options="positions ?? []"
                                    placeholder="-- Select Position --"
                                    :error="form.errors.position_id"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Description</Label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
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
