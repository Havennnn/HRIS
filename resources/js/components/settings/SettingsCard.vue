<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

type Props = {
    title: string;
    description?: string;
};

defineProps<Props>();
</script>

<template>
    <Card class="border shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="text-base">{{ title }}</CardTitle>
            <CardDescription v-if="description">
                {{ description }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-1 pt-0">
            <slot />
        </CardContent>
    </Card>
</template>
