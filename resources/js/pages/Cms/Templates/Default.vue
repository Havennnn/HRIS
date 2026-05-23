<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import HeadingBlock from '@/Components/Cms/Blocks/Heading.vue';
import ImageBlock from '@/Components/Cms/Blocks/Image.vue';
import ListBlock from '@/Components/Cms/Blocks/List.vue';
import RichTextBlock from '@/Components/Cms/Blocks/RichText.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { CmsBlock, BlockConfig } from '@/Pages/Admin/Pages/index';

defineProps<{
    page: {
        id: number;
        title: string;
        slug: string;
        content: CmsBlock[];
        meta: Record<string, string> | null;
        published_at: string | null;
    };
    blocks: Record<string, BlockConfig>;
}>();

const blockComponentMap: Record<string, any> = {
    heading: HeadingBlock,
    rich_text: RichTextBlock,
    image: ImageBlock,
    list: ListBlock,
};

function resolveBlock(type: string) {
    return blockComponentMap[type] ?? null;
}
</script>

<template>
    <Head :title="page.title">
        <meta v-if="page.meta?.meta_title" name="title" :content="page.meta.meta_title" />
        <meta v-if="page.meta?.meta_description" name="description" :content="page.meta.meta_description" />
    </Head>

    <PublicLayout>
        <article class="prose prose-neutral dark:prose-invert max-w-4xl mx-auto">
            <header class="mb-8 not-prose">
                <h1 class="text-3xl font-bold tracking-tight">{{ page.title }}</h1>
                <p v-if="page.published_at" class="mt-2 text-sm text-muted-foreground">
                    Published {{ page.published_at }}
                </p>
            </header>

            <div v-if="page.content?.length" class="space-y-6">
                <template v-for="block in page.content" :key="block.id">
                    <component
                        :is="resolveBlock(block.type)"
                        v-if="resolveBlock(block.type)"
                        :data="block.data"
                    />
                </template>
            </div>

            <div v-else class="py-12 text-center text-muted-foreground">
                This page has no content yet.
            </div>
        </article>
    </PublicLayout>
</template>
