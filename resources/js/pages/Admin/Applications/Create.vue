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
import { create, index, store } from '@/routes/applications';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed } from 'vue';

const props = defineProps<{
    jobs?: Option[];
    statuses?: Option[];
}>();

// Job/Position options from dummy data (active careers only)
const jobs = computed<Option[]>(() => getActiveCareers().map(c => ({
    value: c.id.toString(),
    label: c.position,
})));

// Status options from dummy data
const statuses = computed<Option[]>(() => getStatusOptions());

const pageTitle = 'Create Application';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Applications', href: index().url },
    { title: 'Create', href: create().url },
]);

const form = useForm({
    job_id: '',
    first_name: '',
    last_name: '',
    middle_name: '',
    birthdate: '',
    mobile_number: '',
    email: '',
});

const headerActions = computed(() => [
    {
        label: 'Back to Applications',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    }
]);

function submit(): void {
    form.post(store().url);
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full border-b flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Data Header -->
            <DataHeader
                variant="profile"
                :title="pageTitle"
                subtitle="Create a new job application within the organization."
                badge-text="New"
                badge-variant="secondary"
                :use-avatar="false"
                :actions="headerActions"
            />

            <!-- Form Container -->
            <div class="mx-auto w-full max-w-4xl">
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Plus class="h-5 w-5" />
                            Application Details
                        </CardTitle>
                        <CardDescription>
                            Fill in the information below to create a new job application. All fields marked with <span class="text-destructive">*</span> are required.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <DataSelector
                                    id="job_id"
                                    v-model="form.job_id"
                                    :options="jobs"
                                    required
                                    placeholder="-- Select a Position --"
                                    :error="form.errors.job_id"
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

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create Application' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
