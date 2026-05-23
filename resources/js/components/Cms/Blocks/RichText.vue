<script setup lang="ts">
defineProps<{
    data: {
        content: string;
    };
}>();
</script>

<template>
    <div class="prose prose-neutral dark:prose-invert max-w-none" v-html="data?.content" />
</template>
