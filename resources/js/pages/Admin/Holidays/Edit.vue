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
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/holidays';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Save } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import HolidayEditResource from './index';

const props = defineProps<{
    data: HolidayEditResource;
    types?: Option[];
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Holiday';

const holidayData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Holidays', href: index().url },
    {
        title: 'Edit',
        href: edit({ holiday: holidayData.value?.id as number }).url,
    },
]);

const form = useForm({
    name: holidayData.value?.name ?? '',
    date: holidayData.value?.date ?? '',
    type: holidayData.value?.type_value ?? '',
    is_paid: holidayData.value?.is_paid ?? true,
    description: holidayData.value?.description ?? '',
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
    form.patch(update({ holiday: holidayData.value?.id as number }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <DataHeader
                variant="form"
                :title="holidayData?.name"
                subtitle="Holiday Details"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="holidayData?.type_badge" />
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
                        <CardTitle>Holiday Information</CardTitle>
                        <CardDescription>
                            Basic holiday details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Holiday Name</p>
                                <p class="text-sm">{{ holidayData?.name }}</p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Date</p>
                                <p class="text-sm">{{ holidayData?.date }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Type</p>
                                <p class="text-sm">{{ holidayData?.type }}</p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Paid Holiday</p>
                                <p class="text-sm">{{ holidayData?.is_paid ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-medium text-muted-foreground mb-1">Description</p>
                            <p class="text-sm">{{ holidayData?.description || '-' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- EDIT MODE -->
                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit Holiday
                        </CardTitle>

                        <CardDescription>
                            Update holiday information.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <Label for="name">
                                    Holiday Name
                                    <span class="text-destructive ml-0.5">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    required
                                    :disabled="form.processing"
                                />

                                <p
                                    v-if="form.errors.name"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="date">
                                        Date
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>
                                    <Input
                                        id="date"
                                        v-model="form.date"
                                        type="date"
                                        required
                                        :disabled="form.processing"
                                    />

                                    <p
                                        v-if="form.errors.date"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.date }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <DataSelector
                                        id="type"
                                        v-model="form.type"
                                        :options="types ?? []"
                                        label="Holiday Type"
                                        required
                                        placeholder="-- Select Type --"
                                        :error="form.errors.type"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="is_paid"
                                    :checked="form.is_paid"
                                    :disabled="form.processing"
                                    @update:checked="form.is_paid = !!$event"
                                />
                                <Label for="is_paid" class="cursor-pointer text-sm font-normal">
                                    Paid holiday (employees get paid for this day off)
                                </Label>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">
                                    Description
                                </Label>
                                <Input
                                    id="description"
                                    v-model="form.description"
                                    :disabled="form.processing"
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
