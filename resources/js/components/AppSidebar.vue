<script setup lang="ts">
import { useRoleAccess } from '@/composables/useRoleAccess';
import applications from '@/routes/applications/index';
import attendanceLogs from '@/routes/attendance-logs/index';
import careers from '@/routes/careers/index';
import departments from '@/routes/departments/index';
import employees from '@/routes/employees/index';
import holidays from '@/routes/holidays/index';
import { dashboard } from '@/routes/index';
import payrolls from '@/routes/payrolls/index';
import positions from '@/routes/positions/index';
import requests from '@/routes/requests/index';
import payoutConfigurations from '@/routes/settings/payout-configurations/index';
import {
    Building2,
    CalendarDays,
    CalendarHeart,
    CalendarX,
    ClipboardList,
    Cog,
    DollarSign,
    DoorOpen,
    LayoutGrid,
    TrendingUp,
    UserCog,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import AppSidebarDefault from 'piacore/components/AppSidebarDefault.vue';
import { computed } from 'vue';

type SidebarItem = {
    title: string;
    href: string | { url?: string };
    icon: unknown;
    roles?: string[];
};

type SidebarSection = {
    label: string;
    items: SidebarItem[];
};

const { SUPER_ADMIN_ONLY, HR_ROLES, HR_FINANCE_ROLES, PROJECT_MANAGEMENT_ROLES, ALL_BUSINESS_ROLES, canAccessRoles } = useRoleAccess();

const sectionItems: SidebarSection[] = [
    {
        label: 'Overview',
        items: [
            { title: 'Dashboard', href: dashboard(), icon: LayoutGrid, roles: SUPER_ADMIN_ONLY },
        ],
    },
    {
        label: 'Human Resources',
        items: [
            { title: 'Departments', href: departments.index(), icon: Building2, roles: ALL_BUSINESS_ROLES },
            { title: 'Positions', href: positions.index(), icon: UserCog, roles: ALL_BUSINESS_ROLES },
            { title: 'Employees', href: employees.index(), icon: Users, roles: ALL_BUSINESS_ROLES },
            { title: 'Attendance Logs', href: attendanceLogs.index(), icon: CalendarDays, roles: HR_FINANCE_ROLES },
        ],
    },
    {
        label: 'Compensation & Benefits',
        items: [
            { title: 'Holiday Calendar', href: holidays.index(), icon: CalendarHeart, roles: HR_ROLES},
            { title: 'Requests', href: requests.index(), icon: CalendarX, roles: HR_ROLES},
            { title: 'Payroll', href: payrolls.index(), icon: DollarSign, roles: HR_FINANCE_ROLES },
        ],
    },
    {
        label: 'Recruitment',
        items: [
            { title: 'Careers', href: careers.index(), icon: DoorOpen, roles: HR_ROLES },
            { title: 'Applications', href: applications.index(), icon: UserPlus, roles: HR_ROLES },
        ],
    },
    {
        label: 'Performance',
        items: [
            { title: 'Performance Reviews', href: '/performance-reviews', icon: TrendingUp, roles: PROJECT_MANAGEMENT_ROLES },
            { title: 'KPIs tyest', href: '/kpis', icon: ClipboardList, roles: PROJECT_MANAGEMENT_ROLES },
        ],
    },
];

const settingsItem: SidebarItem = {
    title: 'Settings',
    href: payoutConfigurations.index(),
    icon: Cog,
    roles: SUPER_ADMIN_ONLY,
};

function canSeeItem(item: SidebarItem): boolean {
    return canAccessRoles(item.roles);
}

const visibleSectionItems = computed(() =>
    sectionItems
        .map((section) => ({
            ...section,
            items: section.items.filter(canSeeItem),
        }))
        .filter((section) => section.items.length > 0),
);

const visibleSettingsItem = computed(() => (canSeeItem(settingsItem) ? settingsItem : null));
</script>

<template>
    <AppSidebarDefault
        :section-items="visibleSectionItems"
        :settings-item="visibleSettingsItem"
    >
        <slot />
    </AppSidebarDefault>
</template>


