<script setup lang="ts">
defineProps<{
    data: {
        url: string;
        alt?: string;
        caption?: string;
    };
}>();
</script>

<template>
    <figure class="not-prose my-6">
        <img
            v-if="data?.url"
            :src="data.url"
            :alt="data?.alt ?? ''"
            class="mx-auto rounded-lg object-cover"
        />
        <figcaption v-if="data?.caption" class="mt-2 text-center text-sm text-muted-foreground">
            {{ data.caption }}
        </figcaption>
    </figure>
</template>
