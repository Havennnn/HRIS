<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Bell, BellRing, CheckCheck, ChevronLeft, ChevronRight, ExternalLink, Inbox, ArrowLeft, CheckCircle, XCircle, Clock } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PaginatedData } from 'piacore/Interface/Pagination';

type NotificationItem = {
    id: string;
    type: string;
    subject: string;
    body: string;
    action_url: string | null;
    read_at: string | null;
    is_read: boolean;
    created_at: string;
    created_at_raw: string;
};

const props = defineProps<{
    notifications: PaginatedData<NotificationItem>;
    unread_count: number;
    selected_notification: NotificationItem | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Notifications', href: '/notifications/all' },
];

const hasSelection = computed(() => props.selected_notification !== null);

function formatBodyPreview(body: string, maxLen = 120): string {
    if (!body) return '';
    const cleaned = body.replace(/Row \d+: /g, '').replace(/\n/g, ' ').trim();
    return cleaned.length > maxLen ? cleaned.slice(0, maxLen) + '...' : cleaned;
}

function getTypeIcon(type: string) {
    if (type.includes('failed') || type.includes('error')) return XCircle;
    if (type.includes('completed') || type.includes('success')) return CheckCircle;
    return Bell;
}

function formatFullBody(body: string): string {
    if (!body) return 'No details available.';
    // Convert newlines to HTML breaks for display
    return body.replace(/\n/g, '<br>');
}

function handleListClick(n: NotificationItem) {
    // Navigate to the detail view — backend will mark as read
    router.visit(`/notifications/all/${n.id}`, {
        preserveState: true,
        preserveScroll: true,
    });
}

function handleBack() {
    router.visit('/notifications/all', {
        preserveState: true,
        preserveScroll: true,
    });
}

function markAllAsRead() {
    router.post('/notifications/read-all', {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function handlePageChange(url: string | null) {
    if (url) {
        router.visit(url, { preserveState: true, preserveScroll: true });
    }
}

// Get a user-friendly label from the notification type
function typeLabel(type: string): string {
    if (type === 'import_completed') return 'Import Success';
    if (type === 'import_failed') return 'Import Failed';
    if (type === 'new_request_submitted') return 'New Request';
    if (type === 'payroll_generated') return 'Payroll Generated';
    if (type === 'payroll_failed') return 'Payroll Failed';
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}
</script>

<template>
    <Head title="Notifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Mobile back button when a notification is selected -->
        <div v-if="hasSelection" class="sticky top-0 z-10 border-b bg-background p-2 md:hidden">
            <Button variant="ghost" size="sm" @click="handleBack">
                <ArrowLeft class="mr-1 h-4 w-4" />
                Back to all notifications
            </Button>
        </div>

        <div class="flex h-full flex-1 flex-col overflow-hidden md:flex-row">
            <!-- Left panel: Notification list (hidden on mobile when detail is shown) -->
            <div
                class="w-full border-b md:w-80 md:min-w-80 md:border-b-0 md:border-r lg:w-96 lg:min-w-96"
                :class="hasSelection ? 'hidden md:flex md:flex-col' : 'flex flex-col'"
            >
                <!-- Header -->
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div class="flex items-center gap-2">
                        <BellRing v-if="unread_count > 0" class="h-5 w-5 text-amber-500" />
                        <Bell v-else class="h-5 w-5" />
                        <div>
                            <h2 class="text-sm font-semibold">Notifications</h2>
                            <p class="text-[11px] text-muted-foreground">
                                {{ unread_count > 0 ? `${unread_count} unread` : 'All caught up' }}
                            </p>
                        </div>
                    </div>
                    <Button
                        v-if="unread_count > 0"
                        variant="ghost"
                        size="sm"
                        class="h-7 gap-1 text-xs font-normal text-muted-foreground hover:text-foreground"
                        @click="markAllAsRead"
                    >
                        <CheckCheck class="h-3.5 w-3.5" />
                        Mark all read
                    </Button>
                </div>

                <!-- Scrollable list -->
                <div class="flex-1 overflow-y-auto">
                    <div v-if="notifications.data.length === 0" class="flex flex-col items-center gap-2 px-4 py-16 text-center text-muted-foreground">
                        <Inbox class="h-10 w-10 opacity-40" />
                        <p class="text-sm font-medium">No notifications</p>
                        <p class="text-xs">You haven't received any notifications yet.</p>
                    </div>

                    <template v-for="(n, i) in notifications.data" :key="n.id">
                        <button
                            class="flex w-full cursor-pointer items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-accent/50"
                            :class="[
                                n.id === selected_notification?.id
                                    ? 'bg-accent border-l-2 border-l-primary'
                                    : n.is_read
                                        ? 'opacity-70 border-l-2 border-l-transparent'
                                        : 'border-l-2 border-l-primary bg-accent/20',
                            ]"
                            @click="handleListClick(n)"
                        >
                            <div class="mt-0.5 shrink-0">
                                <Component
                                    :is="getTypeIcon(n.type)"
                                    class="h-4 w-4"
                                    :class="n.is_read ? 'text-muted-foreground' : 'text-primary'"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <span
                                        class="truncate text-sm font-medium leading-tight"
                                        :class="n.is_read ? '' : 'text-foreground'"
                                    >
                                        {{ n.subject }}
                                    </span>
                                </div>
                                <p class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-muted-foreground">
                                    {{ formatBodyPreview(n.body) }}
                                </p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-[10px] text-muted-foreground/70">{{ n.created_at }}</span>
                                    <span v-if="!n.is_read" class="h-1.5 w-1.5 rounded-full bg-primary" />
                                    <Badge
                                        v-if="n.type.includes('failed') || n.type.includes('error')"
                                        variant="destructive"
                                        class="px-1 py-0 text-[9px] font-normal"
                                    >
                                        Error
                                    </Badge>
                                    <Badge
                                        v-else-if="n.type.includes('completed') || n.type.includes('success')"
                                        variant="secondary"
                                        class="px-1 py-0 text-[9px] font-normal"
                                    >
                                        Success
                                    </Badge>
                                </div>
                            </div>
                        </button>
                        <Separator v-if="i < notifications.data.length - 1" />
                    </template>

                </div>

                <!-- Pagination -->
                <div
                    v-if="notifications.total > notifications.per_page"
                    class="flex items-center justify-between border-t px-4 py-2"
                >
                    <span class="text-[10px] text-muted-foreground">
                        {{ notifications.from ?? 0 }}–{{ notifications.to ?? 0 }} of {{ notifications.total }}
                    </span>
                    <div class="flex items-center gap-1">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-7 w-7"
                            :disabled="!notifications.prev_page_url"
                            @click="handlePageChange(notifications.prev_page_url)"
                        >
                            <ChevronLeft class="h-3.5 w-3.5" />
                        </Button>
                        <span class="min-w-[2.5rem] text-center text-[10px] text-muted-foreground">
                            {{ notifications.current_page }}/{{ notifications.last_page }}
                        </span>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-7 w-7"
                            :disabled="!notifications.next_page_url"
                            @click="handlePageChange(notifications.next_page_url)"
                        >
                            <ChevronRight class="h-3.5 w-3.5" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Right panel: Notification detail -->
            <div
                class="flex-1 overflow-y-auto"
                :class="!hasSelection ? 'hidden md:flex md:items-center md:justify-center' : 'flex flex-col'"
            >
                <!-- Empty state -->
                <div v-if="!hasSelection" class="hidden md:flex md:flex-col md:items-center md:gap-3 md:px-4 md:py-24 md:text-center">
                    <Bell class="h-12 w-12 text-muted-foreground/30" />
                    <div>
                        <p class="text-base font-medium text-muted-foreground">Select a notification</p>
                        <p class="text-sm text-muted-foreground/60">Choose a notification from the list to view its details.</p>
                    </div>
                </div>

                <!-- Detail content -->
                <template v-if="selected_notification">
                    <!-- Detail header -->
                    <div class="border-b px-6 py-4">
                        <div class="mb-3 flex items-center justify-between">
                            <Badge variant="outline" class="gap-1 text-[10px] font-normal">
                                <Component
                                    :is="getTypeIcon(selected_notification.type)"
                                    class="h-3 w-3"
                                />
                                {{ typeLabel(selected_notification.type) }}
                            </Badge>
                            <span class="text-[11px] text-muted-foreground">{{ selected_notification.created_at }}</span>
                        </div>
                        <h1 class="text-xl font-semibold leading-tight">{{ selected_notification.subject }}</h1>
                    </div>

                    <!-- Detail body -->
                    <div class="flex-1 px-6 py-5">
                        <div
                            class="prose prose-sm max-w-none text-sm leading-relaxed text-foreground/80"
                            v-html="formatFullBody(selected_notification.body)"
                        />

                        <!-- Action button -->
                        <div v-if="selected_notification.action_url" class="mt-6">
                            <Link
                                :href="selected_notification.action_url"
                                class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"
                            >
                                <ExternalLink class="h-4 w-4" />
                                Go to related page
                            </Link>
                        </div>

                        <!-- Meta info -->
                        <div class="mt-8 rounded-lg border bg-muted/30 px-4 py-3">
                            <h3 class="mb-2 text-xs font-medium text-muted-foreground">Details</h3>
                            <dl class="space-y-1.5 text-xs">
                                <div class="flex justify-between">
                                    <dt class="text-muted-foreground">Type</dt>
                                    <dd class="font-medium">{{ selected_notification.type }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-muted-foreground">Received</dt>
                                    <dd class="font-medium">{{ selected_notification.created_at }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-muted-foreground">Status</dt>
                                    <dd class="flex items-center gap-1">
                                        <CheckCircle v-if="selected_notification.is_read" class="h-3 w-3 text-green-500" />
                                        <Clock v-else class="h-3 w-3 text-amber-500" />
                                        {{ selected_notification.is_read ? 'Read' : 'Unread' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
