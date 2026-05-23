<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineProps<{
    page: {
        id: number;
        title: string;
        slug: string;
        content: Record<string, any>;
        meta: Record<string, string> | null;
        published_at: string | null;
    };
}>();
</script>

<template>
    <Head :title="page.title">
        <meta v-if="page.meta?.meta_title" name="title" :content="page.meta.meta_title" />
        <meta v-if="page.meta?.meta_description" name="description" :content="page.meta.meta_description" />
    </Head>

    <PublicLayout>
        <!-- Hero -->
        <div v-if="page.content?.hero_image" class="relative h-[40vh] min-h-[300px] w-full overflow-hidden">
            <img :src="page.content.hero_image" :alt="page.content.hero_heading" class="h-full w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12">
                <h1 class="text-3xl font-bold text-white md:text-5xl">{{ page.content.hero_heading }}</h1>
            </div>
        </div>
        <div v-else class="bg-muted/30 px-4 py-16">
            <h1 class="text-center text-4xl font-bold">{{ page.content.hero_heading }}</h1>
        </div>

        <!-- Body -->
        <div class="mx-auto max-w-4xl px-4 py-12 md:py-16">
            <div v-if="page.content?.body" class="prose prose-neutral dark:prose-invert max-w-none text-lg leading-relaxed" v-html="page.content.body"></div>

            <!-- Mission & Vision -->
            <div v-if="page.content?.mission || page.content?.vision" class="mt-12 grid gap-8 md:grid-cols-2">
                <div v-if="page.content?.mission" class="rounded-xl border bg-card p-6">
                    <h2 class="mb-3 text-xl font-semibold">Our Mission</h2>
                    <p class="text-muted-foreground">{{ page.content.mission }}</p>
                </div>
                <div v-if="page.content?.vision" class="rounded-xl border bg-card p-6">
                    <h2 class="mb-3 text-xl font-semibold">Our Vision</h2>
                    <p class="text-muted-foreground">{{ page.content.vision }}</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
