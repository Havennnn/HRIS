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
import { getApplicationById, getInterviewByApplicationId, getPositionOptions, getStatusOptions, type ApplicationData } from '@/dummy/DummyData';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show, update } from '@/routes/applications';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Check, ClipboardCheck, X } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import type { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';

const props = defineProps<{
    data?: ApplicationData;
    jobs?: Option[];
    statuses?: Option[];
    application?: number;
}>();

// Get application ID from route params
const applicationId = computed(() => {
    if (props.application) return props.application;
    const pageProps = usePage().props as Record<string, unknown>;
    return (pageProps.application as number) ?? 1;
});

// Job/Position options from dummy data
const jobs = computed<Option[]>(() => getPositionOptions());

// Status options from dummy data
const statuses = computed<Option[]>(() => getStatusOptions());

// HR Admin options for interviewer selector
const hrAdmins = computed<Option[]>(() => getHrAdminOptions());

// Get application data from dummy
const dummyApplicationData = computed(() => {
    const app = getApplicationById(applicationId.value);
    if (app) {
        return {
            id: app.id,
            job_id: app.id,
            job_name: app.career,
            first_name: app.full_name.split(' ')[0],
            last_name: app.full_name.split(' ').slice(1).join(' ') || '',
            middle_name: '',
            birthdate: app.birthdate,
            mobile_number: app.mobile_number,
            email: app.email,
            status: app.status[0] || { label: 'Pending', variant: 'badge-pending' },
            status_value: getStatusValue(app.status[0]?.label || 'Pending'),
            created_at: new Date().toISOString().split('T')[0],
        };
    }
    return null;
});

// Get interview data
const interviewData = computed(() => {
    return getInterviewByApplicationId(applicationId.value);
});

// Helper to get status value from label
function getStatusValue(label: string): number {
    const statusMap: Record<string, number> = {
        'Pending': 1,
        'Reviewing': 2,
        'Interview': 3,
        'Rejected': 4,
        'Hired': 5,
    };
    return statusMap[label] || 1;
}

// Current status value
const currentStatusValue = ref(dummyApplicationData.value?.status_value || 1);

// Computed status label
const currentStatusLabel = computed(() => {
    const statusLabels: Record<number, string> = {
        1: 'Pending',
        2: 'Reviewing',
        3: 'Interview',
        4: 'Rejected',
        5: 'Hired',
    };
    return statusLabels[currentStatusValue.value] || 'Pending';
});

// UI State
const isEditing = ref(false);
const isSchedulingInterview = ref(false);
const activeTab = ref('information');

// Form for application
const form = useForm({
    job_id: dummyApplicationData.value?.job_id ?? '',
    first_name: dummyApplicationData.value?.first_name ?? '',
    last_name: dummyApplicationData.value?.last_name ?? '',
    middle_name: dummyApplicationData.value?.middle_name ?? '',
    birthdate: dummyApplicationData.value?.birthdate ?? '',
    mobile_number: dummyApplicationData.value?.mobile_number ?? '',
    email: dummyApplicationData.value?.email ?? '',
    status: dummyApplicationData.value?.status_value ?? '',
});

// Form for interview scheduling
const interviewForm = useForm({
    scheduled_at: interviewData.value?.scheduled_at || '',
    location: interviewData.value?.location || '',
    interviewer_name: interviewData.value?.interviewer_name || '',
    notes: interviewData.value?.notes || '',
});

// Form for interview scores
const scoreForm = useForm({
    score: interviewData.value?.score ?? null,
    feedback: interviewData.value?.feedback || '',
    result: interviewData.value?.result || 'pending',
});

const page = usePage();
const pageTitle = 'Application';

const applicationData = computed(() => dummyApplicationData.value);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Applications', href: index().url },
    {
        title: 'Edit',
        href: show({ application: applicationData.value?.id ?? 1 }).url,
    },
]);

// Default status for DataBadge
const defaultStatus = { label: 'Pending', variant: 'badge-pending' };
const applicationStatus = computed(() => applicationData.value?.status ?? defaultStatus);

// Tabs
const tabs = computed(() => {
    const tabList = [
        { key: 'information', label: 'Information' },
    ];

    // Add Interview tab only if status is Interview or higher
    if (currentStatusValue.value >= 3) {
        tabList.push({ key: 'interview', label: 'Interview' });
    }

    tabList.push({ key: 'activity_logs', label: 'Activity Log' });

    return tabList;
});

// Header actions based on current status
const headerActions = computed(() => {
    const actions = [
        {
            label: 'Back',
            href: index().url,
            icon: ArrowLeft,
            variant: 'outline' as const,
            size: 'sm' as const,
        },
    ];

    // Pending -> Start Review
    if (currentStatusValue.value === 1) {
        actions.push({
            label: 'Start Review',
            icon: ClipboardCheck,
            variant: 'default' as const,
            size: 'sm' as const,
            onClick: () => updateStatus(2), // Reviewing
        });
    }

    // Reviewing -> Schedule Interview
    if (currentStatusValue.value === 2) {
        actions.push({
            label: 'Schedule Interview',
            icon: Calendar,
            variant: 'default' as const,
            size: 'sm' as const,
            onClick: () => isSchedulingInterview.value = true,
        });
    }

    // Interview -> Hire or Reject
    if (currentStatusValue.value === 3) {
        actions.push({
            label: 'Reject',
            icon: X,
            variant: 'destructive' as const,
            size: 'sm' as const,
            onClick: () => updateStatus(4), // Rejected
        });
        actions.push({
            label: 'Hire',
            icon: Check,
            variant: 'default' as const,
            size: 'sm' as const,
            class: 'bg-green-600 hover:bg-green-700',
            onClick: () => updateStatus(5), // Hired
        });
    }

    return actions;
});

// Update status
function updateStatus(newStatus: number): void {
    currentStatusValue.value = newStatus;
    form.status = newStatus;
    // In real app, would submit to backend
    console.log('Updating status to:', newStatus);
}

// Submit application form
function submit(): void {
    if (!applicationData.value?.id) return;
    form.patch(update({ application: applicationData.value.id }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}

// Submit interview scheduling
function submitInterviewSchedule(): void {
    // In real app, would submit to backend
    console.log('Scheduling interview:', interviewForm);
    isSchedulingInterview.value = false;
    // Update status to Interview
    updateStatus(3);
}

// Submit interview scores
function submitInterviewScore(): void {
    // In real app, would submit to backend
    console.log('Submitting score:', scoreForm);
}

// Check if can show action buttons
const canShowActions = computed(() => currentStatusValue.value <= 3);

// Get result options for selector
const resultOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'passed', label: 'Passed' },
    { value: 'failed', label: 'Failed' },
];
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Header -->
            <DataHeader
                variant="form"
                :title="applicationData?.full_name || 'Application'"
                subtitle="Job Application"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge :badge="applicationStatus" />
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

            <!-- Interview Tab -->
            <div v-else-if="activeTab === 'interview'" class="mx-auto w-full max-w-4xl">
                <!-- Interview Schedule Card -->
                <Card class="mb-6 border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Interview Schedule
                        </CardTitle>
                        <CardDescription>
                            Details of the scheduled interview.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Scheduled Date & Time</Label>
                                <div class="py-1 text-sm">
                                    {{ interviewData?.scheduled_at || 'Not scheduled' }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label>Location</Label>
                                <div class="py-1 text-sm">
                                    {{ interviewData?.location || 'Not specified' }}
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label>Interviewer</Label>
                            <div class="py-1 text-sm">
                                {{ interviewData?.interviewer_name || 'Not assigned' }}
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label>Notes</Label>
                            <div class="py-1 text-sm">
                                {{ interviewData?.notes || 'No notes' }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Interview Score Card -->
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <ClipboardCheck class="h-5 w-5" />
                            Interview Score
                        </CardTitle>
                        <CardDescription>
                            Evaluate the candidate's interview performance.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submitInterviewScore">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="score">
                                        Score (0-100)
                                    </Label>
                                    <Input
                                        id="score"
                                        v-model="scoreForm.score"
                                        type="number"
                                        min="0"
                                        max="100"
                                        placeholder="Enter score"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="result">
                                        Result
                                    </Label>
                                    <DataSelector
                                        id="result"
                                        v-model="scoreForm.result"
                                        :options="resultOptions"
                                        placeholder="Select result"
                                    />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="feedback">
                                    Feedback
                                </Label>
                                <textarea
                                    id="feedback"
                                    v-model="scoreForm.feedback"
                                    placeholder="Enter interview feedback"
                                    rows="4"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                />
                            </div>
                            <div class="flex justify-end gap-3">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                >
                                    Submit Score
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- Information Tab -->
            <div v-else class="mx-auto w-full max-w-4xl">
                <!-- VIEW MODE -->
                <Card v-if="!isEditing" class="border-dashed">
                    <CardHeader>
                        <CardTitle>Application Information</CardTitle>
                        <CardDescription>
                            Basic job application details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="space-y-2">
                            <Label>Position</Label>
                            <div class="py-1 text-sm">
                                {{ applicationData?.job_name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>First Name</Label>
                                <div class="py-1 text-sm">
                                    {{ applicationData?.first_name }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Middle Name</Label>
                                <div class="py-1 text-sm">
                                    {{ applicationData?.middle_name || '-' }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Last Name</Label>
                                <div class="py-1 text-sm">
                                    {{ applicationData?.last_name }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Birthdate</Label>
                                <div class="py-1 text-sm">
                                    {{ applicationData?.birthdate }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label>Mobile Number</Label>
                                <div class="py-1 text-sm">
                                    {{ applicationData?.mobile_number }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Email</Label>
                            <div class="py-1 text-sm">
                                {{ applicationData?.email }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- EDIT MODE -->
                <Card v-else class="border-dashed">
                    <CardHeader>
                        <CardTitle>Edit Application</CardTitle>
                        <CardDescription>
                            Update job application details.
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
                                    <Label for="first_name">First Name</Label>
                                    <Input
                                        id="first_name"
                                        v-model="form.first_name"
                                        placeholder="Enter first name"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label for="middle_name">Middle Name</Label>
                                    <Input
                                        id="middle_name"
                                        v-model="form.middle_name"
                                        placeholder="Enter middle name"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label for="last_name">Last Name</Label>
                                    <Input
                                        id="last_name"
                                        v-model="form.last_name"
                                        placeholder="Enter last name"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="birthdate">Birthdate</Label>
                                    <Input
                                        id="birthdate"
                                        v-model="form.birthdate"
                                        type="date"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label for="mobile_number">Mobile Number</Label>
                                    <Input
                                        id="mobile_number"
                                        v-model="form.mobile_number"
                                        placeholder="Enter mobile number"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Enter email address"
                                    required
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                                    @click="isEditing = false"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- Interview Schedule Modal -->
            <div v-if="isSchedulingInterview" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <Card class="w-full max-w-lg mx-4">
                    <CardHeader>
                        <CardTitle>Schedule Interview</CardTitle>
                        <CardDescription>
                            Set up an interview for this applicant.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form class="grid gap-4" @submit.prevent="submitInterviewSchedule">
                            <div class="space-y-2">
                                <Label for="scheduled_at">Date & Time</Label>
                                <Input
                                    id="scheduled_at"
                                    v-model="interviewForm.scheduled_at"
                                    type="datetime-local"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="location">Location</Label>
                                <Input
                                    id="location"
                                    v-model="interviewForm.location"
                                    placeholder="Enter location"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="interviewer_name">Interviewer</Label>
                                <DataSelector
                                    id="interviewer_name"
                                    v-model="interviewForm.interviewer_name"
                                    :options="hrAdmins"
                                    placeholder="Select an HR Admin"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="notes">Notes</Label>
                                <textarea
                                    id="notes"
                                    v-model="interviewForm.notes"
                                    placeholder="Enter any notes"
                                    rows="3"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                />
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                                    @click="isSchedulingInterview = false"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2"
                                >
                                    Schedule Interview
                                </button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
