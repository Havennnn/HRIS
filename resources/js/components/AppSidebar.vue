<script setup lang="ts">
import admins from '@/routes/admins/index';
import departments from '@/routes/departments/index';
import { dashboard } from '@/routes/index';
import {
    Briefcase,
    Building2,
    CalendarDays,
    ClipboardList,
    DollarSign,
    LayoutGrid,
    Shield,
    TrendingUp,
    UserPlus,
    Users
} from 'lucide-vue-next';
import AppSidebarDefault from 'piacore/components/AppSidebarDefault.vue';

const sectionItems = [
    {
        label: 'Overview',
        items: [
            { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
        ],
    },
    {
        label: 'Human Resources',
        items: [
            { title: 'Departments', href: departments.index(), icon: Building2 },
            { title: 'Positions', href: '/positions', icon: Briefcase },
            { title: 'Employees', href: '/employees', icon: Users },
            { title: 'Attendance Logs', href: '/attendance-logs', icon: CalendarDays },
        ],
    },
    {
        label: 'Compensation & Benefits',
        items: [
            { title: 'Leave Requests', href: '/leave-requests', icon: CalendarDays },
            { title: 'Payroll', href: '/payrolls', icon: DollarSign },
        ],
    },
    {
        label: 'Recruitment',
        items: [
            { title: 'Jobs', href: '/jobs', icon: Briefcase },
            { title: 'Applications', href: '/applications', icon: UserPlus },
        ],
    },
    {
        label: 'Performance',
        items: [
            { title: 'Performance Reviews', href: '/performance-reviews', icon: TrendingUp },
            { title: 'KPIs', href: '/kpis', icon: ClipboardList },
        ],
    },
];

const settingsItem = {
    title: 'Admin',
    href: admins.index(),
    icon: Shield,
};
</script>

<template>
    <AppSidebarDefault 
        :section-items="sectionItems" 
        :settings-item="settingsItem"
    >
        <slot />
    </AppSidebarDefault>
</template>
