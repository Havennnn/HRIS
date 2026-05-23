<script setup lang="ts">
import FooterBlock from '@/Pages/Cms/Templates/Footer.vue';

defineProps<{
    title?: string;
    footer?: Record<string, any>;
}>();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <header class="border-b">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <a href="/" class="text-lg font-semibold">
                    {{ title ?? 'HRIS' }}
                </a>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <FooterBlock v-if="footer" :content="footer" />
        <footer v-else class="border-t py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" />
        </footer>
    </div>
</template>
