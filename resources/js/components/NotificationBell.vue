<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { Bell, BellRing, CheckCheck, ExternalLink, Inbox } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type NotificationItem = {
    id: string;
    type: string;
    subject: string;
    body: string;
    action_url: string | null;
    created_at: string;
    created_at_raw: string;
};

const page = usePage();
const unreadCount = ref(page.props.notifications?.unread_count ?? 0);
const notifications = ref<NotificationItem[]>([]);
const loading = ref(false);
const open = ref(false);
let pollTimer: ReturnType<typeof setInterval> | null = null;

async function fetchNotifications() {
    loading.value = true;
    try {
        const res = await fetch('/notifications', {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!res.ok) return;
        const data = await res.json();
        notifications.value = data.notifications;
        unreadCount.value = data.unread_count;
    } catch {
        // silent — don't spam console on network blips
    } finally {
        loading.value = false;
    }
}

function markAsRead(id: string) {
    router.post(`/notifications/${id}/read`, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            notifications.value = notifications.value.filter(n => n.id !== id);
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        },
    });
}

function markAllAsRead() {
    router.post('/notifications/read-all', {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            notifications.value = [];
            unreadCount.value = 0;
        },
    });
}

function handleOpenChange(val: boolean) {
    open.value = val;
    if (val) {
        fetchNotifications();
    }
}

function handleNotificationClick(n: NotificationItem) {
    if (n.action_url) {
        router.visit(n.action_url);
    }
    markAsRead(n.id);
}

onMounted(() => {
    // Poll every 30 seconds
    pollTimer = setInterval(fetchNotifications, 30_000);
});

onUnmounted(() => {
    if (pollTimer) clearInterval(pollTimer);
});
</script>

<template>
    <DropdownMenu @update:open="handleOpenChange">
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer"
            >
                <Bell
                    v-if="unreadCount === 0"
                    class="size-5 opacity-80 group-hover:opacity-100"
                />
                <BellRing
                    v-else
                    class="size-5 text-amber-500"
                />
                <span
                    v-if="unreadCount > 0"
                    class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            align="end"
            class="w-80 sm:w-96"
            :side-offset="8"
        >
            <DropdownMenuLabel class="flex items-center justify-between px-3 py-2">
                <span class="text-sm font-semibold">Notifications</span>
                <Button
                    v-if="unreadCount > 0"
                    variant="ghost"
                    size="sm"
                    class="h-7 gap-1 text-xs font-normal text-muted-foreground hover:text-foreground"
                    @click="markAllAsRead"
                >
                    <CheckCheck class="h-3.5 w-3.5" />
                    Mark all read
                </Button>
            </DropdownMenuLabel>

            <DropdownMenuSeparator />

            <div class="max-h-80 overflow-y-auto">
                <div v-if="notifications.length === 0 && !loading" class="flex flex-col items-center gap-2 px-4 py-10 text-center text-muted-foreground">
                    <Inbox class="h-10 w-10 opacity-40" />
                    <p class="text-sm font-medium">No new notifications</p>
                    <p class="text-xs">You're all caught up!</p>
                </div>

                <div v-if="loading && notifications.length === 0" class="flex items-center justify-center py-10">
                    <span class="text-sm text-muted-foreground">Loading...</span>
                </div>

                <template v-for="(n, i) in notifications" :key="n.id">
                    <DropdownMenuItem
                        class="flex cursor-pointer flex-col items-start gap-1 px-3 py-3"
                        @click="handleNotificationClick(n)"
                    >
                        <div class="flex w-full items-start justify-between gap-2">
                            <span class="text-sm font-medium leading-tight">{{ n.subject }}</span>
                            <span class="shrink-0 text-[10px] text-muted-foreground">{{ n.created_at }}</span>
                        </div>
                        <p class="line-clamp-2 text-xs leading-relaxed text-muted-foreground">
                            {{ n.body }}
                        </p>
                        <div v-if="n.action_url" class="mt-1 flex items-center gap-1 text-[10px] font-medium text-primary">
                            View details
                            <ExternalLink class="h-3 w-3" />
                        </div>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator v-if="i < notifications.length - 1" />
                </template>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
