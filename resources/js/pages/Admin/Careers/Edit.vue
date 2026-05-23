<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { draft as draftRoute, edit, index, publish as publishRoute, update } from '@/routes/careers';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Pencil, Save, Send, X } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import type { CareerEditResource } from './index';

const props = defineProps<{
    data: CareerEditResource;
    positions?: Option[];
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Career';

const careerData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Careers', href: index().url },
    {
        title: 'Edit',
        href: careerData.value?.id ? edit({ career: careerData.value.id }).url : '#',
    },
]);

// Parse salary into fixed/range
function parseSalary(val: string | null | undefined): { type: 'fixed' | 'range'; fixed: string; min: string; max: string } {
    if (!val) {
        return { type: 'fixed', fixed: '', min: '', max: '' };
    }
    const parts = val.split('-');
    if (parts.length === 2) {
        return { type: 'range', fixed: '', min: parts[0].trim(), max: parts[1].trim() };
    }
    return { type: 'fixed', fixed: val.trim(), min: '', max: '' };
}

const salaryParsed = parseSalary(careerData.value?.salary);
const salaryType = ref<'fixed' | 'range'>(salaryParsed.type);

const form = useForm({
    position_id: careerData.value?.position_id?.toString() ?? '',
    description: careerData.value?.description ?? '',
    salary: careerData.value?.salary ?? '',
    salary_fixed: salaryParsed.fixed,
    salary_min: salaryParsed.min,
    salary_max: salaryParsed.max,
    meta_title: careerData.value?.meta_title ?? '',
    meta_description: careerData.value?.meta_description ?? '',
    og_title: careerData.value?.og_title ?? '',
    og_description: careerData.value?.og_description ?? '',
    og_image: careerData.value?.og_image ?? '',
});

const isPublished = computed(() => careerData.value?.status_value === 1);

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

function toggleSalaryType(type: 'fixed' | 'range'): void {
    salaryType.value = type;
    form.salary_fixed = '';
    form.salary_min = '';
    form.salary_max = '';
    form.salary = '';
}

function formatSalaryDisplay(val: string | null | undefined): string {
    if (!val) return '-';
    const parts = val.split('-');
    if (parts.length === 2) {
        return `₱${Number(parts[0]).toLocaleString()} - ₱${Number(parts[1]).toLocaleString()}`;
    }
    return `₱${Number(val).toLocaleString()}`;
}

function handleToggleStatus(): void {
    if (!careerData.value?.id) return;
    if (isPublished.value) {
        router.post(draftRoute({ career: careerData.value.id }).url);
    } else {
        router.post(publishRoute({ career: careerData.value.id }).url);
    }
}

function submit(): void {
    if (!careerData.value?.id) return;

    // Format salary value before submit
    if (salaryType.value === 'fixed' && form.salary_fixed) {
        form.salary = form.salary_fixed;
    } else if (salaryType.value === 'range' && form.salary_min && form.salary_max) {
        form.salary = `${form.salary_min}-${form.salary_max}`;
    } else {
        form.salary = '';
    }

    form.patch(update({ career: careerData.value.id }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="careerData" class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <DataHeader
                variant="form"
                :title="careerData?.position"
                subtitle="Career Position"
                :use-avatar="false"
            >
                <template #badge>
                    <DataBadge :badge="careerData?.status" />
                </template>
                <template #actions>
                    <div v-if="!isEditing" class="flex items-center gap-2">
                        <Button variant="outline" size="sm" @click="isEditing = true">
                            <Pencil class="mr-1.5 size-4" />
                            Edit Information
                        </Button>
                        <Button
                            :variant="isPublished ? 'outline' : 'default'"
                            size="sm"
                            @click="handleToggleStatus"
                        >
                            <component :is="isPublished ? X : Send" class="mr-1.5 size-4" />
                            {{ isPublished ? 'Move to Draft' : 'Publish' }}
                        </Button>
                    </div>
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
                        <CardTitle>Career Information</CardTitle>
                        <CardDescription>
                            Basic career position details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-5">
                        <div>
                            <p class="text-xs font-medium text-muted-foreground mb-1">Position</p>
                            <p class="text-sm">{{ careerData?.position ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground mb-1">Description</p>
                            <p class="text-sm whitespace-pre-wrap">{{ careerData?.description || '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground mb-1">Salary</p>
                            <p class="text-sm">{{ formatSalaryDisplay(careerData?.salary) }}</p>
                        </div>

                        <!-- SEO View -->
                        <div v-if="careerData?.meta_title || careerData?.meta_description || careerData?.og_title">
                            <p class="text-xs font-medium text-muted-foreground mb-1">SEO Meta</p>
                            <div class="space-y-1">
                                <p v-if="careerData?.meta_title" class="text-sm"><span class="text-xs text-muted-foreground">Title:</span> {{ careerData.meta_title }}</p>
                                <p v-if="careerData?.meta_description" class="text-sm"><span class="text-xs text-muted-foreground">Description:</span> {{ careerData.meta_description }}</p>
                                <p v-if="careerData?.og_title" class="text-sm"><span class="text-xs text-muted-foreground">OG Title:</span> {{ careerData.og_title }}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground mb-1">Created Date</p>
                            <p class="text-sm">{{ careerData?.created_at ?? '-' }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- EDIT MODE -->
                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Edit Career
                        </CardTitle>

                        <CardDescription>
                            Update career information.
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

                            <div class="space-y-2">
                                <Label>Description</Label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                />

                                <p
                                    v-if="form.errors.description"
                                    class="text-xs text-destructive"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Salary -->
                            <div class="space-y-3">
                                <Label>Salary</Label>

                                <div class="flex gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :class="salaryType === 'fixed' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : ''"
                                        @click="toggleSalaryType('fixed')"
                                    >
                                        Fixed
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        :class="salaryType === 'range' ? 'bg-primary text-primary-foreground hover:bg-primary/90' : ''"
                                        @click="toggleSalaryType('range')"
                                    >
                                        Range
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="text-muted-foreground"
                                        @click="form.salary_fixed = ''; form.salary_min = ''; form.salary_max = ''; form.salary = ''"
                                    >
                                        Clear
                                    </Button>
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
                                    class="text-xs text-destructive"
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
                                            <Label for="edit_meta_title">Meta Title</Label>
                                            <Input id="edit_meta_title" v-model="form.meta_title" placeholder="Title for search results" :disabled="form.processing" />
                                        </div>
                                        <div class="space-y-1.5">
                                            <Label for="edit_meta_description">Meta Description</Label>
                                            <textarea
                                                id="edit_meta_description"
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
                                                <Label for="edit_og_title">OG Title</Label>
                                                <Input id="edit_og_title" v-model="form.og_title" placeholder="Title for social shares" :disabled="form.processing" />
                                            </div>
                                            <div class="space-y-1.5">
                                                <Label for="edit_og_image">OG Image URL</Label>
                                                <Input id="edit_og_image" v-model="form.og_image" placeholder="https://..." :disabled="form.processing" />
                                            </div>
                                        </div>
                                        <div class="space-y-1.5">
                                            <Label for="edit_og_description">OG Description</Label>
                                            <textarea
                                                id="edit_og_description"
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
