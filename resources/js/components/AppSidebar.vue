<script  setup  lang="ts">
import AppSidebarDefault from 'piacore/components/AppSidebarDefault.vue';
</script>

<template>
    <!-- Add props to AppSidebarDefault to pass section-items and settings-tem -->
    <AppSidebarDefault>
    <slot  />
    </AppSidebarDefault>
</template>
