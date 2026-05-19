<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Component } from 'vue';

type Props = {
    icon: Component;
    label: string;
    description?: string;
    actionLabel?: string;
    actionVariant?: 'default' | 'outline' | 'ghost' | 'secondary' | 'destructive';
};

withDefaults(defineProps<Props>(), {
    actionLabel: 'Edit',
    actionVariant: 'outline',
});

const emit = defineEmits<{
    action: [];
}>();
</script>

<template>
    <div
        class="flex items-center justify-between gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/40"
    >
        <div class="flex items-start gap-4 min-w-0">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border bg-background">
                <component :is="icon" class="h-4 w-4 text-muted-foreground" />
            </div>
            <div class="min-w-0 space-y-0.5">
                <p class="text-sm font-medium">{{ label }}</p>
                <p v-if="description" class="text-xs text-muted-foreground">
                    {{ description }}
                </p>
            </div>
        </div>
        <Button
            :variant="actionVariant"
            size="sm"
            class="shrink-0"
            @click="emit('action')"
        >
            {{ actionLabel }}
        </Button>
    </div>
</template>
