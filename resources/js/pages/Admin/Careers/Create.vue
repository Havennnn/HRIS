<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
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
import { computed, ref } from 'vue';

const props = defineProps<{
    positions?: Option[];
}>();

const pageTitle = 'Create Career';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Careers', href: index().url },
    { title: 'Create', href: create().url },
]);

const salaryType = ref<'fixed' | 'range'>('fixed');

const form = useForm({
    position_id: '',
    description: '',
    salary: '',
    salary_fixed: '',
    salary_min: '',
    salary_max: '',
    meta_title: '',
    meta_description: '',
    og_title: '',
    og_description: '',
    og_image: '',
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

function toggleSalaryType(type: 'fixed' | 'range'): void {
    salaryType.value = type;
    form.salary_fixed = '';
    form.salary_min = '';
    form.salary_max = '';
    form.salary = '';
}

function submit(): void {
    // Format salary value before submit
    if (salaryType.value === 'fixed' && form.salary_fixed) {
        form.salary = form.salary_fixed;
    } else if (salaryType.value === 'range' && form.salary_min && form.salary_max) {
        form.salary = `${form.salary_min}-${form.salary_max}`;
    } else {
        form.salary = '';
    }
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
                                    :options="positions ?? []"
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

                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    placeholder="Enter job description"
                                    rows="4"
                                    :disabled="form.processing"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    :aria-invalid="form.errors.description ? 'true' : undefined"
                                />

                                <p
                                    v-if="form.errors.description"
                                    class="text-destructive text-xs font-medium"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Salary -->
                            <div class="space-y-3">
                                <Label>Salary</Label>

                                <!-- Toggle -->
                                <div class="flex gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :class="salaryType === 'fixed' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : ''"
                                        @click="toggleSalaryType('fixed')"
                                    >
                                        Fixed
                                    </button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :class="salaryType === 'range' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : ''"
                                        @click="toggleSalaryType('range')"
                                    >
                                        Range
                                    </button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="text-muted-foreground"
                                        @click="form.salary_fixed = ''; form.salary_min = ''; form.salary_max = ''; form.salary = ''"
                                    >
                                        Clear
                                    </button>
                                </div>

                                <!-- Fixed input -->
                                <div v-if="salaryType === 'fixed'" class="space-y-1">
                                    <Input
                                        v-model="form.salary_fixed"
                                        type="number"
                                        min="0"
                                        placeholder="e.g. 15000"
                                        :disabled="form.processing"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Enter the monthly salary amount.
                                    </p>
                                </div>

                                <!-- Range inputs -->
                                <div v-else class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <Input
                                            v-model="form.salary_min"
                                            type="number"
                                            min="0"
                                            placeholder="Min e.g. 12000"
                                            :disabled="form.processing"
                                        />
                                    </div>
                                    <div class="space-y-1">
                                        <Input
                                            v-model="form.salary_max"
                                            type="number"
                                            min="0"
                                            placeholder="Max e.g. 15000"
                                            :disabled="form.processing"
                                        />
                                    </div>
                                    <p class="col-span-2 text-xs text-muted-foreground">
                                        Enter the minimum and maximum monthly salary range.
                                    </p>
                                </div>

                                <p
                                    v-if="form.errors.salary"
                                    class="text-destructive text-xs font-medium"
                                >
                                    {{ form.errors.salary }}
                                </p>
                            </div>

                            <!-- SEO Meta -->
                            <div class="rounded-lg border bg-card p-5">
                                <h3 class="mb-1 text-sm font-semibold">SEO Meta</h3>
                                <p class="mb-4 text-xs text-muted-foreground">Search engine optimization and social sharing settings.</p>

                                <div class="grid gap-5">
                                    <div class="space-y-3">
                                        <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Google Search</h4>
                                        <div class="space-y-1.5">
                                            <Label for="career_meta_title">Meta Title</Label>
                                            <Input id="career_meta_title" v-model="form.meta_title" placeholder="Title for search results" :disabled="form.processing" />
                                        </div>
                                        <div class="space-y-1.5">
                                            <Label for="career_meta_description">Meta Description</Label>
                                            <textarea
                                                id="career_meta_description"
                                                class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                                v-model="form.meta_description"
                                                placeholder="Brief description (150-160 chars)"
                                                :disabled="form.processing"
                                            />
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Social Sharing (Open Graph)</h4>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="space-y-1.5">
                                                <Label for="career_og_title">OG Title</Label>
                                                <Input id="career_og_title" v-model="form.og_title" placeholder="Title for social shares" :disabled="form.processing" />
                                            </div>
                                            <div class="space-y-1.5">
                                                <Label for="career_og_image">OG Image URL</Label>
                                                <Input id="career_og_image" v-model="form.og_image" placeholder="https://..." :disabled="form.processing" />
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <Label for="career_og_description">OG Description</Label>
                                            <textarea
                                                id="career_og_description"
                                                class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                                v-model="form.og_description"
                                                placeholder="Description for social shares"
                                                :disabled="form.processing"
                                            />
                                        </div>
                                    </div>
                                </div>
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
