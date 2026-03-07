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
import { edit, index, update } from '@/routes/departments';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import { computed } from 'vue';
import type { DepartmentResource } from './Departments';

const props = defineProps<{
    data: DepartmentResource;
}>();

const pageTitle = 'Edit Department';

const departmentData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Departments', href: index().url },
    { title: 'Edit', href: edit({ department: departmentData.value?.id as number }).url },
]);

const form = useForm({
    name: departmentData.value?.name ?? '',
});

const headerActions = computed(() => [
    {
        label: 'Back to Departments',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    }
]);

function submit(): void {
    form.patch(update({ department: departmentData.value?.id as number }).url);
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
                :subtitle="departmentData?.name"
                :use-avatar="false"
                :actions="headerActions"
            />

            <!-- Form Container -->
            <div class="mx-auto w-full max-w-4xl">
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Department Details
                        </CardTitle>
                        <CardDescription>
                            Update the department information. All fields marked with <span class="text-destructive">*</span> are required.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <Label for="name">
                                    Department Name
                                    <span class="text-destructive ml-0.5">*</span>
                                </Label>

                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="Enter department name"
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
