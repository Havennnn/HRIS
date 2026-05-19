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
import { edit, index, update } from '@/routes/performance-reviews';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Plus, Save, Trash2 } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { computed, ref } from 'vue';
import type { EmployeeOption, KpiOption, PerformanceReviewEditResource, StatusOption } from './index';

const props = defineProps<{
    data: PerformanceReviewEditResource;
    employees?: EmployeeOption[];
    kpis?: KpiOption[];
    statuses?: StatusOption[];
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Performance Review';

const reviewData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Performance Reviews', href: index().url },
    {
        title: 'Edit',
        href: edit({ performance_review: reviewData.value?.id as number }).url,
    },
]);

const feedbackTypes = [
    { value: 1, label: 'Strength' },
    { value: 2, label: 'Improvement' },
    { value: 3, label: 'General' },
];

const form = useForm({
    employee_id: reviewData.value?.employee_id ?? '',
    reviewer_id: reviewData.value?.reviewer_id ?? '',
    review_date: reviewData.value?.review_date ?? '',
    status: reviewData.value?.status ?? '',
    scores: (reviewData.value?.scores ?? []).map(s => ({
        id: s.id,
        kpi_id: s.kpi_id,
        score: s.score,
    })),
    feedback: (reviewData.value?.feedback ?? []).map(f => ({
        id: f.id,
        type: f.type,
        feedback: f.feedback,
    })),
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
    { key: 'default', label: 'Information' },
    { key: 'activity_logs', label: 'Activity Log' },
]);

function addScore(): void {
    form.scores.push({ id: null, kpi_id: '', score: 0 });
}

function removeScore(index: number): void {
    form.scores.splice(index, 1);
}

function addFeedback(): void {
    form.feedback.push({ id: null, type: '', feedback: '' });
}

function removeFeedback(index: number): void {
    form.feedback.splice(index, 1);
}

function submit(): void {
    form.patch(update({ performance_review: reviewData.value?.id as number }).url, {
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
            <DataHeader
                variant="form"
                :title="'Performance Review'"
                subtitle="Review Details"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="reviewData?.status as any" v-if="reviewData?.status" />
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

            <div v-else class="mx-auto w-full max-w-4xl">
                <!-- VIEW MODE -->
                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Review Information</CardTitle>
                        <CardDescription>Performance review details with scores and feedback.</CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Employee</Label>
                                <div class="py-1 text-sm">Employee #{{ reviewData?.employee_id }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label>Reviewer</Label>
                                <div class="py-1 text-sm">Reviewer #{{ reviewData?.reviewer_id }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Review Date</Label>
                                <div class="py-1 text-sm">{{ reviewData?.review_date }}</div>
                            </div>
                            <div class="space-y-2">
                                <Label>Overall Score</Label>
                                <div class="py-1 text-sm">{{ reviewData?.overall_score ?? '-' }}</div>
                            </div>
                        </div>

                        <div v-if="reviewData?.scores && reviewData.scores.length > 0" class="space-y-2">
                            <Label>Scores</Label>
                            <div class="py-1 text-sm space-y-1">
                                <div v-for="score in reviewData.scores" :key="score.id" class="flex gap-2">
                                    <span class="text-muted-foreground">KPI #{{ score.kpi_id }}:</span>
                                    <span class="font-medium">{{ score.score }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="reviewData?.feedback && reviewData.feedback.length > 0" class="space-y-2">
                            <Label>Feedback</Label>
                            <div class="py-1 text-sm space-y-2">
                                <div v-for="item in reviewData.feedback" :key="item.id" class="border rounded p-2">
                                    <span class="text-xs text-muted-foreground">Type #{{ item.type }}</span>
                                    <p class="mt-1">{{ item.feedback }}</p>
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
                            Edit Performance Review
                        </CardTitle>
                        <CardDescription>Update review information, scores, and feedback.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <DataSelector
                                    id="employee_id"
                                    v-model="form.employee_id"
                                    :options="(employees ?? []).map(e => ({ value: e.id, label: `${e.first_name} ${e.last_name}` }))"
                                    label="Employee"
                                    required
                                    placeholder="-- Select Employee --"
                                    :error="form.errors.employee_id"
                                />
                                <DataSelector
                                    id="reviewer_id"
                                    v-model="form.reviewer_id"
                                    :options="(employees ?? []).map(e => ({ value: e.id, label: `${e.first_name} ${e.last_name}` }))"
                                    label="Reviewer"
                                    required
                                    placeholder="-- Select Reviewer --"
                                    :error="form.errors.reviewer_id"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="review_date">Review Date <span class="text-destructive ml-0.5">*</span></Label>
                                    <Input id="review_date" v-model="form.review_date" type="date" required :disabled="form.processing" />
                                    <p v-if="form.errors.review_date" class="text-xs text-destructive">{{ form.errors.review_date }}</p>
                                </div>
                                <DataSelector
                                    id="status"
                                    v-model="form.status"
                                    :options="(statuses ?? []).map(s => ({ value: s.value, label: s.label }))"
                                    label="Status"
                                    required
                                    placeholder="-- Select Status --"
                                    :error="form.errors.status"
                                />
                            </div>

                            <!-- Scores -->
                            <Card class="border-dashed">
                                <CardHeader class="flex flex-row items-center justify-between">
                                    <div>
                                        <CardTitle class="text-lg">KPI Scores</CardTitle>
                                        <CardDescription>Add scores for each relevant KPI.</CardDescription>
                                    </div>
                                    <Button type="button" variant="outline" size="sm" @click="addScore">
                                        <Plus class="h-4 w-4 mr-1" /> Add Score
                                    </Button>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div v-for="(score, index) in form.scores" :key="index" class="grid grid-cols-1 gap-3 md:grid-cols-3 items-end border rounded-lg p-4">
                                        <DataSelector
                                            v-model="score.kpi_id"
                                            :options="(kpis ?? []).map(k => ({ value: k.id, label: k.name }))"
                                            label="KPI"
                                            required
                                            placeholder="-- Select KPI --"
                                        />
                                        <div class="space-y-2">
                                            <Label>Score (0-100)</Label>
                                            <Input v-model="score.score" type="number" min="0" max="100" placeholder="Score" />
                                        </div>
                                        <Button type="button" variant="destructive" size="sm" class="self-end" @click="removeScore(index)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Feedback -->
                            <Card class="border-dashed">
                                <CardHeader class="flex flex-row items-center justify-between">
                                    <div>
                                        <CardTitle class="text-lg">Feedback</CardTitle>
                                        <CardDescription>Add strengths, improvements, or general feedback.</CardDescription>
                                    </div>
                                    <Button type="button" variant="outline" size="sm" @click="addFeedback">
                                        <Plus class="h-4 w-4 mr-1" /> Add Feedback
                                    </Button>
                                </CardHeader>
                                <CardContent class="space-y-4">
                                    <div v-for="(item, index) in form.feedback" :key="index" class="grid grid-cols-1 gap-3 md:grid-cols-3 items-end border rounded-lg p-4">
                                        <DataSelector
                                            v-model="item.type"
                                            :options="feedbackTypes"
                                            label="Type"
                                            required
                                            placeholder="-- Select Type --"
                                        />
                                        <div class="space-y-2 md:col-span-1">
                                            <Label>Feedback</Label>
                                            <textarea v-model="item.feedback" placeholder="Enter feedback" class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                                        </div>
                                        <Button type="button" variant="destructive" size="sm" class="self-end" @click="removeFeedback(index)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" @click="isEditing = false">Cancel</Button>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Update' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
