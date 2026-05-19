<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import AppLayout from '@/layouts/AppLayout.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import payoutConfigurations from '@/routes/settings/payout-configurations/index';
import type { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Cog, DollarSign } from 'lucide-vue-next';

type NavItem = {
    title: string;
    href: string;
    icon: typeof Cog;
    description: string;
};

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const { isCurrentOrParentUrl } = useCurrentUrl();

const settingsNavItems: NavItem[] = [
    {
        title: 'Payout Configurations',
        href: payoutConfigurations.index(),
        icon: DollarSign,
        description: 'Configure payroll cutoff and disbursement schedules.',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1">
            <Sidebar
                collapsible="none"
                class="w-64 shrink-0 border-r"
                side="left"
            >
                <SidebarHeader class="border-b px-4 py-3">
                    <div class="flex items-center gap-2">
                        <Cog class="h-5 w-5" />
                        <span class="font-semibold">Settings</span>
                    </div>
                </SidebarHeader>

                <SidebarContent class="p-2">
                    <SidebarGroup>
                        <SidebarGroupLabel class="px-2 py-1.5 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            System
                        </SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem
                                    v-for="item in settingsNavItems"
                                    :key="item.title"
                                    class="px-0"
                                >
                                    <SidebarMenuButton
                                        as-child
                                        :is-active="isCurrentOrParentUrl(item.href)"
                                        :tooltip="item.title"
                                        class="h-auto items-start gap-3 py-3 px-2"
                                    >
                                        <Link :href="item.href" class="min-w-0">
                                            <item.icon class="mt-0.5 h-4 w-4 shrink-0" />
                                            <div class="flex flex-col items-start gap-0.5 min-w-0">
                                                <span class="text-sm font-medium">{{ item.title }}</span>
                                                <span class="text-xs text-muted-foreground line-clamp-2">
                                                    {{ item.description }}
                                                </span>
                                            </div>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>
            </Sidebar>

            <div class="flex-1 overflow-auto">
                <slot />
            </div>
        </div>
    </AppLayout>
</template>
