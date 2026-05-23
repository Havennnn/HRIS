<script setup lang="ts">
defineProps<{
    data: {
        items?: {
            text: string;
            has_image?: string;
            image?: string;
        }[];
    };
}>();
</script>

<template>
    <div v-if="data?.items?.length" class="not-prose grid gap-4 my-6">
        <div
            v-for="(item, i) in data.items"
            :key="i"
            class="flex items-start gap-4 rounded-lg border bg-card p-4"
        >
            <img
                v-if="item.has_image === 'yes' && item.image"
                :src="item.image"
                :alt="item.text"
                class="h-16 w-16 shrink-0 rounded-md object-cover"
            />
            <div class="min-w-0">
                <p class="text-sm font-medium">{{ item.text }}</p>
            </div>
        </div>
    </div>
</template>
