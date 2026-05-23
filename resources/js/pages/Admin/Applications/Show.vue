<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, BriefcaseBusiness, UserRoundX } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    hire,
    index,
    interview,
    reject,
    show,
} from '@/routes/applications';
import type { BreadcrumbItem } from '@/types';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { ApplicationStatus } from './index';
import type { ApplicationShowResource } from './index';

const props = defineProps<{
    data: ApplicationShowResource | { data: ApplicationShowResource };
}>();

const page = usePage();
const pageTitle = 'Application';

const applicationData = computed<ApplicationShowResource>(() => {
    if ('data' in props.data) {
        return props.data.data;
    }

    return props.data;
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Applications',
        href: index().url,
    },
    {
        title: applicationData.value?.full_name ?? 'Application Details',
        href: applicationData.value?.id
            ? show({ application: applicationData.value.id }).url
            : '#',
    },
]);

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

    switch (applicationData.value?.status_value) {
        case ApplicationStatus.REVIEWING:
            actions.push(
                {
                    label: 'Reject',
                    icon: UserRoundX,
                    variant: 'destructive' as const,
                    size: 'sm' as const,
                    onClick: () => handleReject(),
                },
                {
                    label: 'Move to Interview',
                    icon: BriefcaseBusiness,
                    variant: 'default' as const,
                    size: 'sm' as const,
                    onClick: () => handleInterview(),
                },
            );
            break;
        case ApplicationStatus.INTERVIEW:
            actions.push(
                {
                    label: 'Reject',
                    icon: UserRoundX,
                    variant: 'destructive' as const,
                    size: 'sm' as const,
                    onClick: () => handleReject(),
                },
                {
                    label: 'Hire',
                    icon: BriefcaseBusiness,
                    variant: 'default' as const,
                    size: 'sm' as const,
                    onClick: () => handleHire(),
                },
            );
            break;
        default:
            break;
    }

    return actions;
});

const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const tabs = computed(() => [
    {
        key: 'default',
        label: 'Information',
    },
    {
        key: 'activity_logs',
        label: 'Activity Log',
    },
]);

function handleInterview(): void {
    if (!applicationData.value?.id) {
        return;
    }

    if (confirm('Move this application to interview?')) {
        router.post(interview({ application: applicationData.value.id }).url);
    }
}

function handleReject(): void {
    if (!applicationData.value?.id) {
        return;
    }

    if (confirm('Reject this application?')) {
        router.post(reject({ application: applicationData.value.id }).url);
    }
}

function handleHire(): void {
    if (!applicationData.value?.id) {
        return;
    }

    if (confirm('Mark this application as hired?')) {
        router.post(hire({ application: applicationData.value.id }).url);
    }
}
</script>

<template>
    <Head :title="applicationData?.full_name ? `${applicationData.full_name} - ${pageTitle}` : pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="form"
                :title="applicationData?.full_name ?? 'Application Details'"
                subtitle="Job Application Profile"
                :use-avatar="false"
                :actions="headerActions"
            >
                <template #badge>
                    <DataBadge v-if="applicationData?.status" :badge="applicationData.status" />
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
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle>Application Information</CardTitle>
                        <CardDescription>
                            Overview of the applicant profile and submission details.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Applicant Name</p>
                                <p class="text-sm">{{ applicationData?.full_name || '-' }}</p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Position Applied</p>
                                <p class="text-sm">{{ applicationData?.position || '-' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Birthdate</p>
                                <p class="text-sm">{{ applicationData?.birthdate || '-' }}</p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Applied At</p>
                                <p class="text-sm">{{ applicationData?.created_at || '-' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Mobile Number</p>
                                <p class="text-sm">{{ applicationData?.mobile_number || '-' }}</p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Email</p>
                                <p class="text-sm">{{ applicationData?.email || '-' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Current Status</p>
                                <p class="text-sm">
                                    <DataBadge v-if="applicationData?.status" :badge="applicationData.status" />
                                    <span v-else>-</span>
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Last Updated</p>
                                <p class="text-sm">{{ applicationData?.updated_at || '-' }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
