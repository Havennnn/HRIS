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
import { create, index, store } from '@/routes/holidays';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed } from 'vue';

const props = defineProps<{
    types?: Option[];
}>();

const pageTitle = 'Create Holiday';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Holidays', href: index().url },
    { title: 'Create', href: create().url },
]);

const form = useForm({
    name: '',
    date: '',
    type: '',
    is_paid: true,
    description: '',
});

const headerActions = computed(() => [
    {
        label: 'Back to Holidays',
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
                subtitle="Create a new holiday within the organization."
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
                            Holiday Details
                        </CardTitle>
                        <CardDescription>
                            Fill in the information below to create a new holiday. All fields marked with <span class="text-destructive">*</span> are required.
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
                                    placeholder="Enter holiday name"
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
                                        :aria-invalid="form.errors.date ? 'true' : undefined"
                                    />

                                    <p
                                        v-if="form.errors.date"
                                        class="text-destructive text-xs font-medium"
                                    >
                                        {{ form.errors.date }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <DataSelector
                                        id="type"
                                        :label="'Holiday Type'"
                                        v-model="form.type"
                                        :options="types ?? []"
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
                                    placeholder="Enter description (optional)"
                                    :disabled="form.processing"
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create Holiday' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
