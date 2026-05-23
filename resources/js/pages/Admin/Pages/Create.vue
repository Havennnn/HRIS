<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import BlockEditor from '@/Components/Cms/BlockEditor.vue';
import DynamicForm from '@/Components/Cms/DynamicForm.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminSettingsLayout from '@/layouts/admin/settings/Layout.vue';
import { create, index, store } from '@/routes/pages';
import type { BreadcrumbItem } from '@/types';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataSelector from 'piacore/components/DataSelector.vue';
import type { BlockConfig, CmsBlock, TemplateOption } from './index';

const props = defineProps<{
    templates?: TemplateOption[];
    templateConfigs?: Record<string, any>;
    blocks?: Record<string, BlockConfig>;
}>();

const pageTitle = 'Create Page';

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Pages', href: index().url },
    { title: 'Create', href: create().url },
]);

const form = useForm({
    title: '',
    slug: '',
    template: 'default',
    status: '0',
    content: [] as CmsBlock[] | Record<string, any>,
    meta: {
        meta_title: '',
        meta_description: '',
    },
});

/** Check if selected template uses the block editor */
const usesBlockEditor = computed(() => {
    if (!props.templateConfigs) return true;
    const cfg = props.templateConfigs[form.template];
    return cfg?.use_block_editor ?? true;
});

/** Get the field definitions for the selected template (dynamic form mode) */
const templateFields = computed(() => {
    if (!props.templateConfigs) return {};
    const cfg = props.templateConfigs[form.template];
    return cfg?.fields ?? {};
});

/** When template changes, reset content */
watch(() => form.template, () => {
    if (usesBlockEditor.value) {
        form.content = [];
    } else {
        form.content = {};
    }
});

const headerActions = computed(() => [
    {
        label: 'Back to Pages',
        href: index().url,
        icon: ArrowLeft,
        variant: 'outline' as const,
        size: 'sm' as const,
    },
]);

let blockIdCounter = 0;

function addBlock(type: string): void {
    const config = props.blocks?.[type];
    if (!config) return;

    blockIdCounter++;
    const block: CmsBlock = {
        id: `block_${blockIdCounter}_${Date.now()}`,
        type,
        data: {},
    };

    (form.content as CmsBlock[]).push(block);
}

function removeBlock(index: number): void {
    (form.content as CmsBlock[]).splice(index, 1);
}

function moveBlock(fromIndex: number, toIndex: number): void {
    const blocks = form.content as CmsBlock[];
    const [moved] = blocks.splice(fromIndex, 1);
    blocks.splice(toIndex, 0, moved);
}

function submit(): void {
    store(form);
}
</script>

<template>
    <Head :title="pageTitle" />

    <AdminSettingsLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="form"
                title="Create Page"
                description="Create a new page for your website."
                :actions="headerActions"
            />

            <form @submit.prevent="submit">
                <div class="grid gap-6">
                    <!-- Basic Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Page Information</CardTitle>
                            <CardDescription>Basic page details and settings.</CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="title">Title <span class="text-destructive">*</span></Label>
                                <Input id="title" v-model="form.title" placeholder="Page title" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="slug">URL Slug</Label>
                                <Input id="slug" v-model="form.slug" placeholder="about-us" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="template">Template</Label>
                                <select
                                    id="template"
                                    v-model="form.template"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                >
                                    <option v-for="tpl in templates" :key="tpl.value" :value="tpl.value">
                                        {{ tpl.label }}
                                    </option>
                                </select>
                                <p v-if="templates?.find(t => t.value === form.template)?.description" class="text-xs text-muted-foreground">
                                    {{ templates?.find(t => t.value === form.template)?.description }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="status">Status</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                >
                                    <option value="0">Draft</option>
                                    <option value="1">Published</option>
                                </select>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Content Editor -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Content</CardTitle>
                            <CardDescription>
                                {{ usesBlockEditor ? 'Add and arrange content blocks.' : 'Fill in the page fields.' }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <!-- Block editor mode -->
                            <BlockEditor
                                v-if="usesBlockEditor && blocks"
                                :blocks="form.content as CmsBlock[]"
                                :block-types="blocks"
                                @add-block="addBlock"
                                @remove="removeBlock"
                                @move="moveBlock"
                            />

                            <!-- Dynamic form mode -->
                            <DynamicForm
                                v-else-if="!usesBlockEditor"
                                :model-value="form.content as Record<string, any>"
                                :fields="templateFields"
                                @update:model-value="form.content = $event"
                            />

                            <p v-else class="py-8 text-center text-sm text-muted-foreground">
                                Select a template above to start editing.
                            </p>
                        </CardContent>
                    </Card>

                    <!-- SEO Meta -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">SEO Meta</CardTitle>
                            <CardDescription>Search engine optimization settings.</CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="meta_title">Meta Title</Label>
                                <Input id="meta_title" v-model="form.meta.meta_title" placeholder="Page title for search results" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="meta_description">Meta Description</Label>
                                <textarea
                                    id="meta_description"
                                    v-model="form.meta.meta_description"
                                    class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Brief description for search results"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Submit -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 disabled:opacity-50"
                        >
                            <Plus class="h-4 w-4" />
                            {{ form.processing ? 'Creating...' : 'Create Page' }}
                        </button>
                        <a :href="index().url" class="text-sm text-muted-foreground hover:text-foreground">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </AdminSettingsLayout>
</template>
