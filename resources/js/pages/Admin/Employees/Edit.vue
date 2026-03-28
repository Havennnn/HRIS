<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { edit, index, update } from '@/routes/employees';
import * as attendanceRoutes from '@/routes/employees/attendance';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, LaptopMinimalCheck, Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import EmployeeEditResource from './index';

const props = defineProps<{
    data: EmployeeEditResource;
    positions?: Option[];
    types?: Option[];
}>();

const isEditing = ref(false);
const isUpdatingDevice = ref(false);

const { HR_ROLES, canAccessRoles } = useRoleAccess();
const canEditEmployee = computed(() => canAccessRoles(HR_ROLES));

const page = usePage();
const pageTitle = 'Employee';

const employeeData = computed(() => props.data?.data);
const deviceData = computed(() => employeeData.value?.device ?? null);
const hasDeviceInfo = computed(() => {
    const device = deviceData.value;

    if (!device) {
        return false;
    }

    return [device.desktop, device.laptop].some((value) => Boolean(value?.trim()));
});

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

const deviceForm = useForm({
    desktop: deviceData.value?.desktop ?? '',
    laptop: deviceData.value?.laptop ?? '',
});

const headerActions = computed(() => [
    {
        label: 'Back',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    },
    ...(!isEditing.value && canEditEmployee.value
        ? [
            {
                label: 'Edit',
                icon: Pencil,
                variant: 'default' as const,
                size: 'sm' as const,
                onClick: () => (isEditing.value = true),
            },
            {
                label: 'Device',
                icon: LaptopMinimalCheck,
                variant: 'default' as const,
                size: 'sm' as const,
                onClick: () => {
                    deviceForm.desktop = deviceData.value?.desktop ?? '';
                    deviceForm.laptop = deviceData.value?.laptop ?? '';
                    deviceForm.clearErrors();
                    isUpdatingDevice.value = true;
                },
            },
        ]
        : []),
]);

const tabs = computed(() => [
    {
        key: 'default',
        label: 'Information',
    },
    {
        key: 'attendance',
        label: 'Attendance',
        route: attendanceRoutes.index({ employee: employeeData.value?.id as number }).url,
    },
    {
        key: 'activity_logs',
        label: 'Activity Log',
    },
]);

const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

function submit(): void {
    if (!canEditEmployee.value) {
        return;
    }

    form.patch(update({ employee: employeeData.value?.id as number }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}

function updateDevice(): void {
    if (!employeeData.value?.id) {
        return;
    }

    deviceForm.patch(`/employees/${employeeData.value.id}/device`, {
        preserveScroll: true,
        onSuccess: () => {
            isUpdatingDevice.value = false;
        },
    });
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="form"
                :title="employeeData?.full_name"
                subtitle="Employee Profile"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="employeeData?.status" />
                </template>
            </DataHeader>

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

            <div v-else class="mx-auto w-full max-w-4xl space-y-6">
                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Employee Information</CardTitle>
                        <CardDescription>Basic employee details.</CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Position</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.position }} - {{ employeeData?.position_level }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Department</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.department }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>First Name</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.first_name }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Middle Name</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.middle_name || '-' }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Last Name</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.last_name }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Birthdate</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.birthdate_formatted ?? employeeData?.birthdate ?? '-' }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Employee Type</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.type }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Mobile Number</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.mobile_number }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Email</Label>
                                <div class="py-1 text-sm">
                                    {{ employeeData?.email }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Device Information</CardTitle>
                        <CardDescription>Assigned employee devices.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <div v-if="hasDeviceInfo" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Desktop</Label>
                                <div class="py-1 text-sm">
                                    {{ deviceData?.desktop || '-' }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Laptop</Label>
                                <div class="py-1 text-sm">
                                    {{ deviceData?.laptop || '-' }}
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-sm text-muted-foreground">
                            No device provided.
                        </div>
                    </CardContent>
                </Card>

                <Card v-else-if="canEditEmployee" class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit Employee
                        </CardTitle>
                        <CardDescription>Update employee information.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" @click="isEditing = false">
                                    Cancel
                                </Button>

                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Update' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Dialog v-model:open="isUpdatingDevice">
                    <DialogContent class="sm:max-w-[400px]">
                        <DialogHeader>
                            <DialogTitle>Update Device Information</DialogTitle>
                            <DialogDescription>
                                Update assigned devices for this employee.
                            </DialogDescription>
                        </DialogHeader>

                        <form
                            id="update-device-form"
                            class="grid gap-6"
                            @submit.prevent="updateDevice"
                        >
                            <div class="space-y-2">
                                <Label for="desktop">Desktop</Label>
                                <Input id="desktop" v-model="deviceForm.desktop" placeholder="Desktop asset or name" />
                                <p v-if="deviceForm.errors.desktop" class="text-sm text-destructive">
                                    {{ deviceForm.errors.desktop }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="laptop">Laptop</Label>
                                <Input id="laptop" v-model="deviceForm.laptop" placeholder="Laptop asset or name" />
                                <p v-if="deviceForm.errors.laptop" class="text-sm text-destructive">
                                    {{ deviceForm.errors.laptop }}
                                </p>
                            </div>
                        </form>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isUpdatingDevice = false"
                            >
                                Cancel
                            </Button>

                            <Button
                                type="submit"
                                form="update-device-form"
                                :disabled="deviceForm.processing"
                            >
                                {{ deviceForm.processing ? 'Saving...' : 'Update Device' }}
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
