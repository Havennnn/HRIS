<script setup lang="ts">
const props = defineProps<{
    data: {
        text: string;
        level?: number;
    };
}>();

const tag = props.data?.level && [1, 2, 3, 4, 5, 6].includes(props.data.level)
    ? `h${props.data.level}`
    : 'h2';
</script>

<template>
    <component :is="tag" class="scroll-m-20">
        {{ data?.text }}
    </component>
</template>
