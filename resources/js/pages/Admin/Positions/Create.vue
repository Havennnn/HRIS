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
import { create, index, store } from '@/routes/positions';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed } from 'vue';

const props = defineProps<{
    departments?: Option[];
}>();

const pageTitle = 'Create Position';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Positions', href: index().url },
    { title: 'Create', href: create().url },
]);

const form = useForm({
    department_id: '',
    name: '',
    salary: '',
    allowance: '',
    level: '',
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
                subtitle="Create a new position within the organization."
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
                            Position Details
                        </CardTitle>
                        <CardDescription>
                            Fill in the information below to create a new position. All fields marked with <span class="text-destructive">*</span> are required.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <DataSelector
                                    id="department_id"
                                    :label="'Select Department'"
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

                            <div class="grid gap-6 md:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="level">
                                        Level
                                    </Label>

                                    <Input
                                        id="level"
                                        v-model="form.level"
                                        placeholder="e.g., Junior, Senior, Lead"
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.level ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.level"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.level }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="salary">
                                        Salary
                                    </Label>

                                    <Input
                                        id="salary"
                                        v-model="form.salary"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.salary ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.salary"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.salary }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="allowance">
                                        Allowance
                                    </Label>

                                    <Input
                                        id="allowance"
                                        v-model="form.allowance"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.allowance ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.allowance"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.allowance }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create Position' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
