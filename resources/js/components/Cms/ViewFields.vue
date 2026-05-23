<script setup lang="ts">
import type { BlockField } from '@/Pages/Admin/Pages/index';

const props = defineProps<{
    content: Record<string, any>;
    fields: Record<string, BlockField>;
}>();

function hasValue(key: string): boolean {
    return props.content[key] !== undefined && props.content[key] !== null && props.content[key] !== '';
}
</script>

<template>
    <div class="grid gap-6">
        <template v-for="(fieldConfig, fieldKey) in fields" :key="fieldKey">
            <!-- Text -->
            <div v-if="fieldConfig.type === 'text' && hasValue(fieldKey)" class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <p class="text-sm">{{ content[fieldKey] }}</p>
            </div>

            <!-- Textarea / Rich text -->
            <div v-else-if="fieldConfig.type === 'textarea' && hasValue(fieldKey)" class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <div class="prose prose-neutral dark:prose-invert max-w-none text-sm" v-html="content[fieldKey]"></div>
            </div>

            <!-- Select -->
            <div v-else-if="fieldConfig.type === 'select' && hasValue(fieldKey)" class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <p class="text-sm capitalize">{{ content[fieldKey] }}</p>
            </div>

            <!-- File / Image -->
            <div v-else-if="fieldConfig.type === 'file' && hasValue(fieldKey)" class="space-y-1">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <img
                    :src="content[fieldKey]"
                    :alt="fieldConfig.label"
                    class="max-h-64 rounded-lg border object-cover"
                />
            </div>

            <!-- Repeater -->
            <div v-else-if="fieldConfig.type === 'repeater' && content[fieldKey]?.length" class="space-y-2">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <div class="grid gap-3">
                    <div
                        v-for="(item, i) in content[fieldKey]"
                        :key="i"
                        class="flex items-start gap-4 rounded-lg border bg-card p-4"
                    >
                        <img
                            v-if="item.has_image === 'yes' && item.image"
                            :src="item.image"
                            :alt="item.text"
                            class="h-14 w-14 shrink-0 rounded-md object-cover"
                        />
                        <div class="min-w-0">
                            <p class="text-sm font-medium">{{ item.text }}</p>
                            <div v-if="item.role" class="text-xs text-muted-foreground">{{ item.role }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty field (has key but no value) -->
            <div v-else-if="content[fieldKey] !== undefined && !hasValue(fieldKey)" class="space-y-1 opacity-50">
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">{{ fieldConfig.label }}</p>
                <p class="text-sm italic text-muted-foreground">Not set</p>
            </div>
        </template>

        <!-- Empty state when no fields at all -->
        <div v-if="!Object.keys(fields).length" class="py-12 text-center text-sm text-muted-foreground">
            This template has no fields defined.
        </div>
    </div>
</template>
