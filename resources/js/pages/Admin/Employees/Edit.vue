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
import { edit, index, update } from '@/routes/employees';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import type { EmployeeResource } from './index';

const props = defineProps<{
    data: EmployeeResource;
    positions?: Option[];
    types?: Option[];
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Employee';

const employeeData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Employees', href: index().url },
    {
        title: 'Edit',
        href: edit({ employee: employeeData.value?.id as number }).url,
    },
]);

const form = useForm({
    position_id: employeeData.value?.position_id ?? '',
    first_name: employeeData.value?.first_name ?? '',
    last_name: employeeData.value?.last_name ?? '',
    middle_name: employeeData.value?.middle_name ?? '',
    birthdate: employeeData.value?.birthdate ?? '',
    mobile_number: employeeData.value?.mobile_number ?? '',
    email: employeeData.value?.email ?? '',
    type: employeeData.value?.type_value ?? '',
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
        key: 'attendance', 
        label: 'Attendance',
        route: '#',
    },
    { 
        key: 'activity_logs', 
        label: 'Activity Log' 
    },
]);

function submit(): void {
    form.patch(update({ employee: employeeData.value?.id as number }).url, {
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
                variant="profile"
                :title="employeeData?.full_name"
                subtitle="Employee Profile"
                :use-avatar="false"
                :actions="headerActions"
            />

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
                        <CardTitle>Employee Information</CardTitle>
                        <CardDescription>
                            Basic employee details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="space-y-2">
                            <Label>Position</Label>
                            <div
                                class="py-1 text-sm"
                            >
                                {{ employeeData?.position }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>First Name</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.first_name }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Middle Name</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.middle_name || '-' }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Last Name</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.last_name }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Birthdate</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.birthdate }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Employee Type</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.type }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Mobile Number</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.mobile_number }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Email</Label>
                                <div
                                    class="py-1 text-sm"
                                >
                                    {{ employeeData?.email }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- EDIT MODE -->
                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit Employee
                        </CardTitle>

                        <CardDescription>
                            Update employee information.
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

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="space-y-2">
                                    <Label>First Name</Label>
                                    <Input v-model="form.first_name" required />

                                    <p
                                        v-if="form.errors.first_name"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label>Middle Name</Label>
                                    <Input v-model="form.middle_name" />
                                </div>

                                <div class="space-y-2">
                                    <Label>Last Name</Label>
                                    <Input v-model="form.last_name" required />

                                    <p
                                        v-if="form.errors.last_name"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.last_name }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label>Birthdate</Label>
                                    <Input
                                        v-model="form.birthdate"
                                        type="date"
                                        required
                                    />

                                    <p
                                        v-if="form.errors.birthdate"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.birthdate }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <DataSelector
                                        id="type"
                                        v-model="form.type"
                                        :options="types ?? []"
                                        label="Employee Type"
                                        required
                                        placeholder="-- Select Type --"
                                        :error="form.errors.type"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label>Mobile Number</Label>
                                    <Input
                                        v-model="form.mobile_number"
                                        required
                                    />

                                    <p
                                        v-if="form.errors.mobile_number"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.mobile_number }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label>Email</Label>
                                    <Input
                                        v-model="form.email"
                                        type="email"
                                        required
                                    />

                                    <p
                                        v-if="form.errors.email"
                                        class="text-xs text-destructive"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>
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
