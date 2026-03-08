<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/positions';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed } from 'vue';
import type { PositionResource } from './Positions';

const props = defineProps<{
    data: PositionResource;
    departments?: Option[];
}>();

const page = usePage();
const pageTitle = 'Edit Position';

const positionData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Positions', href: index().url },
    { title: 'Edit', href: edit({ position: positionData.value?.id as number }).url },
]);

const form = useForm({
    department_id: positionData.value?.department_id ?? '',
    name: positionData.value?.name ?? '',
});

const headerActions = computed(() => [
    {
        label: 'Back to Positions',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    }
]);

const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const tabs = computed(() => [
    {
        key: 'default',
        label: 'Information',
    },
    {
        key: 'activity_logs',
        label: 'Activity Log'
    },
]);

function submit(): void {
    form.patch(update({ position: positionData.value?.id as number }).url);
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Data Header -->
            <DataHeader
                variant="profile"
                :title="pageTitle"
                :subtitle="positionData?.name"
                :use-avatar="false"
                :actions="headerActions"
            />

            <!-- Data Controller -->
            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                show-tabs
                :show-date-range="false"
                :show-search="false"
            />

            <!-- Activity Logs Tab -->
            <div v-if="activeTab === 'activity_logs'" class="mx-auto w-full">
                <ActivityLogTable />
            </div>

            <!-- Information/Edit Tab -->
            <div v-else class="mx-auto w-full max-w-4xl space-y-6">
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Position Details
                        </CardTitle>
                        <CardDescription>
                            Update the position information. All fields marked with <span class="text-destructive">*</span> are required.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <Label for="department_id">
                                    Department
                                    <span class="text-destructive ml-0.5">*</span>
                                </Label>

                                <DataSelector
                                    id="department_id"
                                    v-model="form.department_id"
                                    :options="departments ?? []"
                                    required
                                    placeholder="-- Select a Department --"
                                    :error="form.errors.department_id"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="name">
                                    Position Name
                                    <span class="text-destructive ml-0.5">*</span>
                                </Label>

                                <Input
                                    id="name"
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

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
