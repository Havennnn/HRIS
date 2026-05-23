<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { approve, cancel, complete, index, reject } from '@/routes/requests';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Ban, Check, Save, X } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { computed } from 'vue';
import type { RequestShowResource } from './index';
import { RequestStatus } from './index';

const props = defineProps<{
    data: RequestShowResource;
}>();

const page = usePage();
const pageTitle = 'Request Details';

const requestData = computed(() => props.data?.data);

// Determine actions based on status_value
const headerActions = computed(() => {
    const actions = [];
    const status = requestData.value?.status_value;

    // Back button always available
    actions.push({
        label: 'Back',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    });

    // REVIEWING (2): Show Approve/Reject buttons
    if (status === RequestStatus.REVIEWING) {
        actions.push({
            label: 'Reject',
            variant: 'destructive' as const,
            size: 'sm' as const,
            icon: X,
            onClick: () => handleReject(),
        });
        actions.push({
            label: 'Approve',
            variant: 'default' as const,
            size: 'sm' as const,
            icon: Check,
            onClick: () => handleApprove(),
        });
    }

    // APPROVED (3): Show Cancel/Complete buttons
    if (status === RequestStatus.APPROVED) {
        actions.push({
            label: 'Cancel',
            variant: 'outline' as const,
            size: 'sm' as const,
            icon: Ban,
            onClick: () => handleCancel(),
        });
        actions.push({
            label: 'Complete',
            variant: 'default' as const,
            size: 'sm' as const,
            icon: Check,
            onClick: () => handleComplete(),
        });
    }

    return actions;
});

function handleApprove(): void {
    if (confirm('Are you sure you want to approve this request?')) {
        router.post(approve({ request: requestData.value?.id as number }).url);
    }
}

function handleReject(): void {
    if (confirm('Are you sure you want to reject this request?')) {
        router.post(reject({ request: requestData.value?.id as number }).url);
    }
}

function handleCancel(): void {
    if (confirm('Are you sure you want to cancel this request?')) {
        router.post(cancel({ request: requestData.value?.id as number }).url);
    }
}

function handleComplete(): void {
    if (confirm('Are you sure you want to mark this request as completed?')) {
        router.post(complete({ request: requestData.value?.id as number }).url);
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Data Header -->
            <DataHeader
                variant="profile"
                :title="pageTitle"
                :subtitle="`Request #${requestData?.id}`"
                :use-avatar="false"
                :actions="headerActions"
            />

            <!-- Data Controller -->
            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                show-tabs
                :show-date-range="false"
                :show-search="false"
            />

            <!-- Activity Logs Tab -->
            <div v-if="activeTab === 'activity_logs'" class="mx-auto w-full">
                <ActivityLogTable />
            </div>

            <!-- Information Tab -->
            <div v-else class="mx-auto w-full max-w-4xl space-y-6">
                <!-- Request Details Card -->
                <Card class="border-dashed">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Save class="h-5 w-5" />
                            Request Information
                        </CardTitle>
                        <CardDescription>
                            View the details of this request.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-6">
                            <!-- Status Badge -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">Status:</span>
                                <DataBadge v-if="requestData?.status" :badge="requestData.status" />
                            </div>

                            <!-- Employee Information -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Employee Name</p>
                                    <p class="text-sm">{{ requestData?.full_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Position</p>
                                    <p class="text-sm">{{ requestData?.position }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Level</p>
                                    <p class="text-sm">{{ requestData?.position_level }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Department</p>
                                    <p class="text-sm">{{ requestData?.department }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Request Type</p>
                                    <p class="text-sm">{{ requestData?.type }}</p>
                                </div>
                            </div>

                            <!-- Date Information -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Requested Date</p>
                                    <p class="text-sm">{{ requestData?.requested_date }}</p>
                                </div>
                                <div v-if="requestData?.end_date" class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">End Date</p>
                                    <p class="text-sm">{{ requestData?.end_date }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-muted-foreground mb-1">Days / Hours</p>
                                    <p class="text-sm">
                                        {{ requestData?.days ? `${requestData.days} day(s)` : (requestData?.overtime_hours ? `${requestData.overtime_hours} hour(s)` : '-') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="space-y-2">
                                <p class="text-xs font-medium text-muted-foreground mb-1">Message / Reason</p>
                                <p class="rounded-md border p-3 bg-muted/50 text-sm">
                                    {{ requestData?.message || 'No message provided' }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
