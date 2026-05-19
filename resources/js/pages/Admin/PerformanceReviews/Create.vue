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
import { create, index, store } from '@/routes/performance-reviews';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import { computed } from 'vue';
import type { EmployeeOption, KpiOption, StatusOption } from './index';

const props = defineProps<{
    employees?: EmployeeOption[];
    kpis?: KpiOption[];
    statuses?: StatusOption[];
}>();

const pageTitle = 'Create Performance Review';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Performance Reviews', href: index().url },
    { title: 'Create', href: create().url },
]);

const feedbackTypes = [
    { value: 1, label: 'Strength' },
    { value: 2, label: 'Improvement' },
    { value: 3, label: 'General' },
];

const form = useForm({
    employee_id: '',
    reviewer_id: '',
    review_date: '',
    status: '',
    scores: [] as Array<{ kpi_id: string; score: string }>,
    feedback: [] as Array<{ type: string; feedback: string }>,
});

function addScore(): void {
    form.scores.push({ kpi_id: '', score: '' });
}

function removeScore(index: number): void {
    form.scores.splice(index, 1);
}

function addFeedback(): void {
    form.feedback.push({ type: '', feedback: '' });
}

function removeFeedback(index: number): void {
    form.feedback.splice(index, 1);
}

const headerActions = computed(() => [
    {
        label: 'Back to Reviews',
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
            <DataHeader
                variant="profile"
                :title="pageTitle"
                subtitle="Create a new performance review with KPI scores and feedback."
                badge-text="New"
                badge-variant="secondary"
                :use-avatar="false"
                :actions="headerActions"
            />

            <div class="mx-auto w-full max-w-4xl">
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Plus class="h-5 w-5" />
                            Review Details
                        </CardTitle>
                        <CardDescription>
                            Fill in the information below to create a new performance review.
                        </CardDescription>
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
                                    <Label for="review_date">
                                        Review Date
                                        <span class="text-destructive ml-0.5">*</span>
                                    </Label>
                                    <Input
                                        id="review_date"
                                        v-model="form.review_date"
                                        type="date"
                                        required
                                        :disabled="form.processing"
                                        :aria-invalid="form.errors.review_date ? 'true' : undefined"
                                    />
                                    <p v-if="form.errors.review_date" class="text-destructive text-xs font-medium">{{ form.errors.review_date }}</p>
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

                            <!-- KPI Scores Section -->
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
                                    <p v-if="form.errors['scores']" class="text-destructive text-xs font-medium">{{ form.errors['scores'] }}</p>
                                </CardContent>
                            </Card>

                            <!-- Feedback Section -->
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
                                    <p v-if="form.errors['feedback']" class="text-destructive text-xs font-medium">{{ form.errors['feedback'] }}</p>
                                </CardContent>
                            </Card>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create Review' }}
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
