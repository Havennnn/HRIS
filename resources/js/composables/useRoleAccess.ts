import { useAuth } from 'piacore/composables/useAuth';

export const SUPER_ADMIN_ONLY = ['super_admin'] as const;
export const HR_ROLES = ['super_admin', 'head_hr', 'hr_officer'] as const;
export const FINANCE_ROLES = ['super_admin', 'finance_manager', 'finance_officer'] as const;
export const PROJECT_MANAGEMENT_ROLES = ['super_admin', 'head_hr', 'hr_officer', 'project_manager'] as const;
export const HR_FINANCE_ROLES = ['super_admin', 'head_hr', 'hr_officer', 'finance_manager', 'finance_officer'] as const;
export const ALL_BUSINESS_ROLES = ['super_admin', 'head_hr', 'hr_officer', 'finance_manager', 'finance_officer', 'project_manager'] as const;

export type RoleGroup = readonly string[];

export function useRoleAccess() {
    const { hasAnyRole } = useAuth();

    function canAccessRoles(roles?: RoleGroup | string[]): boolean {
        return !roles || roles.length === 0 || hasAnyRole([...roles]);
    }

    return {
        SUPER_ADMIN_ONLY,
        HR_ROLES,
        FINANCE_ROLES,
        PROJECT_MANAGEMENT_ROLES,
        HR_FINANCE_ROLES,
        ALL_BUSINESS_ROLES,
        canAccessRoles,
    };
}
