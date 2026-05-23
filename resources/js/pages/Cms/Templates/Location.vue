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
        <div class="bg-muted/30 px-4 py-16">
            <h1 class="text-center text-4xl font-bold">{{ page.content?.heading || page.title }}</h1>
        </div>

        <div class="mx-auto max-w-6xl px-4 py-12 md:py-16">
            <div class="grid gap-10 md:grid-cols-2">
                <!-- Details -->
                <div class="space-y-6">
                    <div v-if="page.content?.address">
                        <h2 class="mb-2 text-lg font-semibold">Address</h2>
                        <p class="text-muted-foreground whitespace-pre-line">{{ page.content.address }}</p>
                    </div>

                    <div v-if="page.content?.phone">
                        <h2 class="mb-2 text-lg font-semibold">Phone</h2>
                        <p class="text-muted-foreground">{{ page.content.phone }}</p>
                    </div>

                    <div v-if="page.content?.email">
                        <h2 class="mb-2 text-lg font-semibold">Email</h2>
                        <p class="text-muted-foreground">{{ page.content.email }}</p>
                    </div>
                </div>

                <!-- Photo -->
                <div v-if="page.content?.image" class="overflow-hidden rounded-xl">
                    <img :src="page.content.image" alt="Location" class="h-full w-full object-cover" />
                </div>
            </div>

            <!-- Map -->
            <div v-if="page.content?.map_url" class="mt-12 overflow-hidden rounded-xl border">
                <iframe
                    :src="page.content.map_url"
                    class="h-[400px] w-full"
                    style="border:0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                />
            </div>
        </div>
    </PublicLayout>
</template>
