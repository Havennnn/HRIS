<script setup lang="ts">
import { computed } from 'vue';
import type { CmsBlock } from '@/Pages/Admin/Pages/index';

const props = defineProps<{
    block: CmsBlock;
    blocks: Record<string, unknown>;
}>();

const resolved = computed(() => {
    const comp = props.blocks[props.block.type];
    return comp;
});
</script>

<template>
    <component :is="resolved" v-if="resolved" :data="block.data" />
</template>
