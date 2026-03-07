<script  setup  lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem } from '@/types';

type  Props  = {
    breadcrumbs?:
    BreadcrumbItem[]
  };

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell  variant="sidebar">
    <AppSidebar />
	    <AppContent  variant="sidebar"  class="overflow-x-hidden">
	    <!-- You can Add a action by passing it action-items -->
	    <AppSidebarHeader
		    :breadcrumbs="breadcrumbs"
		    :show-user-menu="true"
	    />
		    <slot  />
	    </AppContent>
    </AppShell>
</template>
