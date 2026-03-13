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
import { create, index, store } from '@/routes/careers';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed } from 'vue';

const props = defineProps<{
    positions?: Option[];
}>();

// Position options from dummy data
const positions = computed<Option[]>(() => getPositionOptions());

const pageTitle = 'Create Career';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Careers', href: index().url },
    { title: 'Create', href: create().url },
]);

const form = useForm({
    position_id: '',
    description: '',
    is_active: true,
});

const headerActions = computed(() => [
    {
        label: 'Back to Careers',
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
                subtitle="Create a new career position within the organization."
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
                            Career Details
                        </CardTitle>
                        <CardDescription>
                            Fill in the information below to create a new career position. All fields marked with <span class="text-destructive">*</span> are required.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="space-y-2">
                                <DataSelector
                                    id="position_id"
                                    v-model="form.position_id"
                                    :options="positions"
                                    required
                                    placeholder="-- Select a Position --"
                                    :error="form.errors.position_id"
                                    label="Position"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">
                                    Description
                                </Label>

                                <Input
                                    id="description"
                                    v-model="form.description"
                                    type="textarea"
                                    placeholder="Enter job description"
                                    :disabled="form.processing"
                                    class="min-h-[120px] resize-y"
                                    :aria-invalid="form.errors.description ? 'true' : undefined"
                                />

                                <p
                                    v-if="form.errors.description"
                                    class="text-destructive text-xs font-medium"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <Label for="is_active">
                                    Active Status
                                </Label>
                                <span class="text-sm text-muted-foreground">
                                    {{ form.is_active ? 'This career is active and visible' : 'This career is inactive' }}
                                </span>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create Career' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
