<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Bell, BellRing, CheckCheck, ChevronLeft, ChevronRight, ExternalLink, Inbox } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Notifications', href: '/notifications/all' },
];

function markAsRead(id: string) {
    router.post(`/notifications/${id}/read`, {}, {
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
        router.visit(url, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Notifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div class="flex items-center gap-3">
                        <BellRing v-if="unread_count > 0" class="h-5 w-5 text-amber-500" />
                        <Bell v-else class="h-5 w-5" />
                        <div>
                            <CardTitle>Notifications</CardTitle>
                            <p class="text-sm text-muted-foreground">
                                {{ unread_count > 0 ? `${unread_count} unread` : 'All caught up' }}
                            </p>
                        </div>
                    </div>
                    <Button
                        v-if="unread_count > 0"
                        variant="outline"
                        size="sm"
                        @click="markAllAsRead"
                    >
                        <CheckCheck class="mr-1 h-4 w-4" />
                        Mark all read
                    </Button>
                </CardHeader>
                <CardContent class="space-y-0 p-0">
                    <template v-if="notifications.data.length === 0">
                        <div class="flex flex-col items-center gap-2 px-4 py-16 text-center text-muted-foreground">
                            <Inbox class="h-12 w-12 opacity-40" />
                            <p class="text-base font-medium">No notifications</p>
                            <p class="text-sm">You haven't received any notifications yet.</p>
                        </div>
                    </template>

                    <template v-for="(n, i) in notifications.data" :key="n.id">
                        <div
                            class="flex cursor-pointer items-start gap-3 px-6 py-4 transition-colors hover:bg-accent/50"
                            :class="n.is_read ? 'opacity-70' : 'border-l-2 border-l-primary bg-accent/20'"
                            @click="markAsRead(n.id)"
                        >
                            <div class="mt-0.5 shrink-0">
                                <Bell v-if="n.is_read" class="h-4 w-4 text-muted-foreground" />
                                <BellRing v-else class="h-4 w-4 text-primary" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <span
                                        class="truncate text-sm font-medium"
                                        :class="n.is_read ? '' : 'text-foreground'"
                                    >
                                        {{ n.subject }}
                                    </span>
                                    <span class="shrink-0 whitespace-nowrap text-[11px] text-muted-foreground">
                                        {{ n.created_at }}
                                    </span>
                                </div>
                                <p class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-muted-foreground">
                                    {{ n.body }}
                                </p>
                                <div v-if="n.action_url" class="mt-1.5">
                                    <Link
                                        :href="n.action_url"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-primary hover:underline"
                                    >
                                        View details
                                        <ExternalLink class="h-3 w-3" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-if="i < notifications.data.length - 1" class="mx-6 border-t" />
                    </template>

                    <!-- Pagination -->
                    <div
                        v-if="notifications.total > notifications.per_page"
                        class="flex items-center justify-between border-t px-6 py-3"
                    >
                        <span class="text-xs text-muted-foreground">
                            Showing {{ notifications.from ?? 0 }}–{{ notifications.to ?? 0 }} of {{ notifications.total }}
                        </span>
                        <div class="flex items-center gap-1">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8"
                                :disabled="!notifications.prev_page_url"
                                @click="handlePageChange(notifications.prev_page_url)"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </Button>
                            <span class="min-w-[3rem] text-center text-xs text-muted-foreground">
                                Page {{ notifications.current_page }} / {{ notifications.last_page }}
                            </span>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8"
                                :disabled="!notifications.next_page_url"
                                @click="handlePageChange(notifications.next_page_url)"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
