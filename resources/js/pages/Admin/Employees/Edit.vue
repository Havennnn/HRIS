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
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Eye, KeyRound, LaptopMinimalCheck, MoreHorizontal, Pencil, Phone, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import EmployeeEditResource from './index';

const props = defineProps<{
    data: EmployeeEditResource;
    positions?: Option[];
    types?: Option[];
    contactTypes?: Option[];
}>();

const isEditing = ref(false);
const isUpdatingDevice = ref(false);
const isUpdatingContactPerson = ref(false);
const isDocumentPreviewOpen = ref(false);
const selectedDocumentUrl = ref('');
const selectedDocumentName = ref('Document Preview');

const { HR_ROLES, canAccessRoles } = useRoleAccess();
const canEditEmployee = computed(() => canAccessRoles(HR_ROLES));

const page = usePage();
const pageTitle = 'Employee';

const employeeData = computed(() => props.data?.data);
const contactPerson = computed(() => employeeData.value?.contact_person ?? null);
const hasContactPerson = computed(() => {
    const contact = contactPerson.value;

    if (!contact) {
        return false;
    }

    return [contact.name, contact.type, contact.mobile_number].some((value) => Boolean(value?.trim()));
});

const deviceData = computed(() => employeeData.value?.device ?? null);
const hasDeviceInfo = computed(() => {
    const device = deviceData.value;

    if (!device) {
        return false;
    }

    return [device.desktop, device.laptop].some((value) => Boolean(value?.trim()));
});

const documents = computed(() => employeeData.value?.documents ?? null);
const documentItems = computed(() => [
    { key: 'sss', label: 'SSS', file: documents.value?.sss ?? null },
    { key: 'philhealth', label: 'PhilHealth', file: documents.value?.philhealth ?? null },
    { key: 'bir', label: 'BIR', file: documents.value?.bir ?? null },
    { key: 'medical', label: 'Medical', file: documents.value?.medical ?? null },
]);
const hasUploadedDocument = computed(() =>
    documentItems.value.some((item) => Boolean(item.file?.url)),
);

function isImageFile(file: { name?: string; url?: string } | null | undefined): boolean {
    if (!file?.url) {
        return false;
    }

    const source = (file.name || file.url).toLowerCase();
    return /\.(png|jpe?g|gif|webp|bmp|svg)(\?|$)/i.test(source);
}

function formatFileSize(size?: number | string): string {
    const bytes = Number(size ?? 0);

    if (!Number.isFinite(bytes) || bytes <= 0) {
        return '-';
    }

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function openDocumentPreview(url: string, name?: string): void {
    selectedDocumentUrl.value = url;
    selectedDocumentName.value = name || 'Document Preview';
    isDocumentPreviewOpen.value = true;
}

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

const contactForm = useForm({
    name: contactPerson.value?.name ?? '',
    type: contactPerson.value?.type_value ?? '',
    mobile_number: contactPerson.value?.mobile_number ?? '',
});

const headerActions = computed(() => [
    ...(!isEditing.value && canEditEmployee.value
        ? [
            {
                label: 'Edit Information',
                icon: Pencil,
                onClick: () => (isEditing.value = true),
            },
            {
                label: 'Edit Device',
                icon: LaptopMinimalCheck,
                onClick: () => {
                    deviceForm.desktop = deviceData.value?.desktop ?? '';
                    deviceForm.laptop = deviceData.value?.laptop ?? '';
                    deviceForm.clearErrors();
                    isUpdatingDevice.value = true;
                },
            },
            {
                label: 'Edit Contact',
                icon: Phone,
                onClick: () => {
                    contactForm.name = contactPerson.value?.name ?? '';
                    contactForm.type = contactPerson.value?.type_value ?? '';
                    contactForm.mobile_number = contactPerson.value?.mobile_number ?? '';
                    contactForm.clearErrors();
                    isUpdatingContactPerson.value = true;
                },
            },
            {
                label: 'Send Reset Password',
                icon: KeyRound,
                onClick: handleResetPassword,
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

function updateContactPerson(): void {
    if (!employeeData.value?.id) {
        return;
    }

    contactForm.patch(`/employees/${employeeData.value.id}/contact-person`, {
        preserveScroll: true,
        onSuccess: () => {
            isUpdatingContactPerson.value = false;
        },
    });
}

function handleResetPassword(): void {
    if (!employeeData.value?.id) {
        return;
    }

    if (!confirm('Are you sure you want to reset this employee\'s password? They will receive an email with a reset link.')) {
        return;
    }

    router.post(`/employees/${employeeData.value.id}/reset-password`, {}, {
        preserveScroll: true,
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
            >
                <template #badge>
                    <DataBadge :badge="employeeData?.status" />
                </template>
                <template #actions>
                    <DropdownMenu v-if="!isEditing && canEditEmployee">
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" size="sm">
                                <MoreHorizontal class="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="min-w-48">
                            <DropdownMenuItem v-for="(action, i) in headerActions" :key="i" @click="action.onClick">
                                <component :is="action.icon" class="mr-2 size-4" />
                                {{ action.label }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
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
                                <p class="text-xs font-medium text-muted-foreground mb-1">Position</p>
                                <p class="text-sm">
                                    {{ employeeData?.position }} - {{ employeeData?.position_level }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Department</p>
                                <p class="text-sm">
                                    {{ employeeData?.department }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">First Name</p>
                                <p class="text-sm">
                                    {{ employeeData?.first_name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Middle Name</p>
                                <p class="text-sm">
                                    {{ employeeData?.middle_name || '-' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Last Name</p>
                                <p class="text-sm">
                                    {{ employeeData?.last_name }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Birthdate</p>
                                <p class="text-sm">
                                    {{ employeeData?.birthdate_formatted ?? employeeData?.birthdate ?? '-' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Employee Type</p>
                                <p class="text-sm">
                                    {{ employeeData?.type }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Mobile Number</p>
                                <p class="text-sm">
                                    {{ employeeData?.mobile_number }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Email</p>
                                <p class="text-sm">
                                    {{ employeeData?.email }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Contact Person</CardTitle>
                        <CardDescription>Employee emergency or reference contact.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <div v-if="hasContactPerson" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Name</p>
                                <p class="text-sm">
                                    {{ contactPerson?.name || '-' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Type</p>
                                <p class="text-sm">
                                    {{ contactPerson?.type || '-' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Mobile Number</p>
                                <p class="text-sm">
                                    {{ contactPerson?.mobile_number || '-' }}
                                </p>
                            </div>
                        </div>

                        <div v-else class="text-sm text-muted-foreground">
                            No contact person provided.
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Employee Documents</CardTitle>
                        <CardDescription>Uploaded onboarding documents.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div
                                v-for="item in documentItems"
                                :key="item.key"
                                class="space-y-1.5"
                            >
                                <p class="text-xs font-medium text-muted-foreground mb-1">{{ item.label }}</p>

                                <div class="rounded-lg border border-dashed p-2.5">
                                    <div v-if="item.file?.url" class="flex items-center gap-3">
                                        <img
                                            v-if="isImageFile(item.file)"
                                            :src="item.file.url"
                                            :alt="item.file.name || `${item.label} document`"
                                            class="h-12 w-12 shrink-0 rounded-md border object-cover"
                                            loading="lazy"
                                        >

                                        <div
                                            v-else
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md border bg-muted text-[9px] text-muted-foreground"
                                        >
                                            No preview
                                        </div>

                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-center gap-2">
                                                <a
                                                    :href="item.file.url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="block min-w-0 truncate text-sm text-primary underline-offset-4 hover:underline"
                                                >
                                                    {{ item.file?.name || 'View file' }}
                                                </a>
                                            </div>

                                            <div class="text-xs text-muted-foreground">
                                                Size: {{ formatFileSize(item.file?.size) }}
                                            </div>
                                        </div>

                                        <div class="flex h-full items-center">
                                            <Button
                                                v-if="isImageFile(item.file)"
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 shrink-0 cursor-pointer border-0 bg-transparent p-0 text-muted-foreground hover:bg-transparent hover:text-foreground focus-visible:bg-transparent"
                                                @click="openDocumentPreview(item.file.url, item.file.name || item.label)"
                                            >
                                                <Eye class="h-5 w-5" />
                                            </Button>
                                        </div>
                                    </div>

                                    <div v-else class="text-xs text-muted-foreground">
                                        No file uploaded.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="!hasUploadedDocument" class="mt-4 text-sm text-muted-foreground">
                            No documents uploaded.
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
                                <p class="text-xs font-medium text-muted-foreground mb-1">Desktop</p>
                                <p class="text-sm">
                                    {{ deviceData?.desktop || '-' }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Laptop</p>
                                <p class="text-sm">
                                    {{ deviceData?.laptop || '-' }}
                                </p>
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
                                    :options="positions ?? []"
                                    required
                                    placeholder="-- Select a Position --"
                                    :error="form.errors.position_id"
                                    label="Position"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="first_name">
                                        First Name
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>

                                    <Input
                                        id="first_name"
                                        v-model="form.first_name"
                                        placeholder="Enter first name"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.first_name ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.first_name"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="middle_name">
                                        Middle Name
                                    </Label>

                                    <Input
                                        id="middle_name"
                                        v-model="form.middle_name"
                                        placeholder="Enter middle name"
                                        :disabled="form.processing"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label for="last_name">
                                        Last Name
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>

                                    <Input
                                        id="last_name"
                                        v-model="form.last_name"
                                        placeholder="Enter last name"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.last_name ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.last_name"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.last_name }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="birthdate">
                                        Birthdate
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>

                                    <Input
                                        id="birthdate"
                                        v-model="form.birthdate"
                                        type="date"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.birthdate ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.birthdate"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.birthdate }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <DataSelector
                                        id="type"
                                        v-model="form.type"
                                        :options="types ?? []"
                                        required
                                        placeholder="-- Select Type --"
                                        :error="form.errors.type"
                                        label="Employee Type"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="mobile_number">
                                        Mobile Number
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>

                                    <Input
                                        id="mobile_number"
                                        v-model="form.mobile_number"
                                        placeholder="Enter mobile number"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.mobile_number ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.mobile_number"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.mobile_number }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">
                                        Email
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>

                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Enter email address"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.email ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.email"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>
                            </div>

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
                    <DialogContent class="sm:max-w-100">
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

                <Dialog v-model:open="isUpdatingContactPerson">
                    <DialogContent class="sm:max-w-105">
                        <DialogHeader>
                            <DialogTitle>Update Contact Person</DialogTitle>
                            <DialogDescription>
                                Set or clear the employee emergency/reference contact.
                            </DialogDescription>
                        </DialogHeader>

                        <form
                            id="update-contact-person-form"
                            class="grid gap-6"
                            @submit.prevent="updateContactPerson"
                        >
                            <div class="space-y-2">
                                <Label for="contact_name">Name</Label>
                                <Input
                                    id="contact_name"
                                    v-model="contactForm.name"
                                    placeholder="Enter contact person name"
                                />
                                <p v-if="contactForm.errors.name" class="text-sm text-destructive">
                                    {{ contactForm.errors.name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <DataSelector
                                    id="contact_type"
                                    v-model="contactForm.type"
                                    :options="contactTypes ?? []"
                                    placeholder="-- Select Contact Type --"
                                    :error="contactForm.errors.type"
                                    label="Type"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="contact_mobile_number">Mobile Number</Label>
                                <Input
                                    id="contact_mobile_number"
                                    v-model="contactForm.mobile_number"
                                    placeholder="Enter contact mobile number"
                                />
                                <p v-if="contactForm.errors.mobile_number" class="text-sm text-destructive">
                                    {{ contactForm.errors.mobile_number }}
                                </p>
                            </div>
                        </form>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isUpdatingContactPerson = false"
                            >
                                Cancel
                            </Button>

                            <Button
                                type="submit"
                                form="update-contact-person-form"
                                :disabled="contactForm.processing"
                            >
                                {{ contactForm.processing ? 'Saving...' : 'Update Contact' }}
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="isDocumentPreviewOpen">
                    <DialogContent class="sm:max-w-2xl [&>button]:cursor-pointer [&>button]:border-0 [&>button]:bg-transparent [&>button]:text-muted-foreground [&>button:hover]:bg-transparent [&>button:hover]:text-foreground [&>button:focus-visible]:bg-transparent [&>button>svg]:h-5 [&>button>svg]:w-5">
                        <DialogHeader>
                            <DialogTitle>{{ selectedDocumentName }}</DialogTitle>
                            <DialogDescription>
                                Document image preview.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="overflow-hidden rounded-md border bg-muted/20">
                            <img
                                :src="selectedDocumentUrl"
                                :alt="selectedDocumentName"
                                class="max-h-[70vh] w-full object-contain"
                            >
                        </div>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
