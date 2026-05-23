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
        <article class="min-w-0">
            <header class="border-b bg-muted/30 px-4 py-12 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">{{ page.title }}</h1>
                    <p v-if="page.published_at" class="mt-3 text-base text-muted-foreground">
                        Published {{ page.published_at }}
                    </p>
                </div>
            </header>

            <div class="px-4 py-8 sm:px-6 lg:px-8">
                <div v-if="page.content?.length" class="mx-auto max-w-7xl space-y-8">
                    <template v-for="block in page.content" :key="block.id">
                        <div class="prose prose-neutral dark:prose-invert max-w-none">
                            <component
                                :is="resolveBlock(block.type)"
                                v-if="resolveBlock(block.type)"
                                :data="block.data"
                            />
                        </div>
                    </template>
                </div>

                <div v-else class="py-12 text-center text-muted-foreground">
                    This page has no content yet.
                </div>
            </div>
        </article>
    </PublicLayout>
</template>
