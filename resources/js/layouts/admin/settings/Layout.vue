<script setup lang="ts">
import AdminSettingsLayout from 'piacore/components/AdminSettingsLayout.vue';
import type { AdminSettingsNavItem } from 'piacore/components/AdminSettingsLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import payoutConfigurations from '@/routes/settings/payout-configurations/index';
import type { BreadcrumbItem } from '@/types';
import { ClipboardList, DollarSign } from 'lucide-vue-next';

defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const settingsNavItems: AdminSettingsNavItem[] = [
    {
        title: 'KPIs',
        href: '/settings/kpis',
        icon: ClipboardList,
        description: 'Define Key Performance Indicators used in performance reviews.',
    },
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
        <AdminSettingsLayout :nav-items="settingsNavItems">
            <slot />
        </AdminSettingsLayout>
    </AppLayout>
</template>
