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
import AdminSettingsLayout from '@/layouts/admin/settings/Layout.vue';
import { edit, index, update } from '@/routes/settings/kpis';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { computed, ref } from 'vue';
import type { KpiEditResource } from './index';

const props = defineProps<{
    data: KpiEditResource;
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'KPI';

const kpiData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'KPIs', href: index().url },
    {
        title: 'Edit',
        href: edit({ kpi: kpiData.value?.id as number }).url,
    },
]);

const form = useForm({
    name: kpiData.value?.name ?? '',
    description: kpiData.value?.description ?? '',
});

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
    { key: 'default', label: 'Information' },
    { key: 'activity_logs', label: 'Activity Log' },
]);

function submit(): void {
    form.patch(update({ kpi: kpiData.value?.id as number }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}
</script>

<template>
    <Head :title="pageTitle" />

    <AdminSettingsLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="form"
                :title="kpiData?.name"
                subtitle="KPI Details"
                :use-avatar="false"
                :actions="headerActions"
            />

            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                show-tabs
                :show-date-range="false"
                :show-search="false"
            />

            <div v-if="activeTab === 'activity_logs'" class="mx-auto w-full">
                <ActivityLogTable />
            </div>

            <div v-else class="mx-auto w-full max-w-4xl">
                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>KPI Information</CardTitle>
                        <CardDescription>
                            Key Performance Indicator details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground mb-1">KPI Name</p>
                            <p class="text-sm">{{ kpiData?.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground mb-1">Description</p>
                            <p class="text-sm">{{ kpiData?.description || '-' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit KPI
                        </CardTitle>

                        <CardDescription>
                            Update KPI information.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <Label for="name">
                                    KPI Name
                                    <span class="text-destructive ml-0.5">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    required
                                    :disabled="form.processing"
                                />

                                <p v-if="form.errors.name" class="text-xs text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    :disabled="form.processing"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                ></textarea>
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
                                    {{ form.processing ? 'Saving...' : 'Update' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminSettingsLayout>
</template>
