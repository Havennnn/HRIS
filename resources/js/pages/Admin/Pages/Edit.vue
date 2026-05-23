<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import DynamicForm from '@/Components/Cms/DynamicForm.vue';
import ViewFields from '@/Components/Cms/ViewFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { edit, index, update } from '@/routes/pages';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Pencil, Save } from 'lucide-vue-next';
import ActivityLogTable from 'piacore/components/ActivityLogTable.vue';
import DataBadge from 'piacore/components/DataBadge.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import DataTableControls from 'piacore/components/DataTableControls.vue';
import { computed, ref } from 'vue';
import type { PageEditResource } from './index';

const props = defineProps<{
    data: PageEditResource;
    templateConfigs?: Record<string, any>;
}>();

const isEditing = ref(false);

const page = usePage();
const pageTitle = 'Page';

const pageData = computed(() => props.data?.data);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Pages', href: index().url },
    {
        title: 'Edit',
        href: edit({ page: pageData.value?.id as number }).url,
    },
]);

const defaultMeta = {
    meta_title: '',
    meta_description: '',
    og_title: '',
    og_description: '',
    og_image: '',
};

const form = useForm({
    title: pageData.value?.title ?? '',
    slug: pageData.value?.slug ?? '',
    content: (pageData.value?.content ?? {}) as Record<string, any>,
    meta: (pageData.value?.meta ?? { ...defaultMeta }) as Record<string, string>,
});

/** Field definitions come from the PageEditResource response itself. */
const fields = computed(() => (pageData.value?.fields ?? {}) as Record<string, any>);

const templateConfig = computed(() => {
    if (!props.templateConfigs) return null;
    const key = pageData.value?.template ?? 'default';
    return props.templateConfigs[key] ?? null;
});

const templateFields = computed(() => templateConfig.value?.fields ?? {});

const tabs = computed(() => [
    { key: 'default', label: 'Information' },
    { key: 'activity_logs', label: 'Activity Log' },
]);

const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

function submit(): void {
    form.patch(update({ page: pageData.value?.id as number }).url, {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
}

function cancelEdit(): void {
    form.title = pageData.value?.title ?? '';
    form.slug = pageData.value?.slug ?? '';
    form.content = (pageData.value?.content ?? {}) as Record<string, any>;
    form.meta = (pageData.value?.meta ?? { ...defaultMeta }) as Record<string, string>;
    form.clearErrors();
    isEditing.value = false;
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <DataHeader
                variant="form"
                :title="pageData?.title"
                :subtitle="pageData?.slug ? `/${pageData.slug}` : ''"
                :use-avatar="false"
            >
                <template #badge>
                    <DataBadge :badge="pageData?.status" />
                </template>
                <template #actions>
                    <Button v-if="!isEditing" variant="default" size="sm" @click="isEditing = true">
                        <Pencil class="mr-1.5 size-4" />
                        Edit Content
                    </Button>
                </template>
            </DataHeader>

            <DataTableControls
                :tabs="tabs"
                :active-tab="activeTab"
                show-tabs
                :show-date-range="false"
                :show-search="false"
            />

            <div v-if="activeTab === 'activity_logs'" class="mx-auto w-full">
                <ActivityLogTable />
            </div>

            <div v-else class="mx-auto w-full max-w-5xl space-y-6">
                <!-- View mode — read-only field values -->
                <div v-if="!isEditing" class="mx-auto w-full max-w-4xl">
                    <div v-if="Object.keys(fields).length" class="rounded-lg border bg-card p-6">
                        <h3 class="mb-4 text-sm font-semibold">{{ pageData?.template_label ?? 'Content' }}</h3>
                        <ViewFields :content="pageData?.content ?? {}" :fields="fields" />
                    </div>
                    <div v-else class="py-12 text-center text-sm text-muted-foreground">
                        No content fields defined for this template.
                    </div>
                </div>

                <!-- Edit mode -->
                <form v-else @submit.prevent="submit" class="space-y-6">
                    <!-- Title & Slug -->
                    <div class="grid gap-4 rounded-lg border bg-card p-5 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <Label for="title">
                                Title <span class="text-destructive ml-0.5">*</span>
                            </Label>
                            <Input id="title" v-model="form.title" required :disabled="form.processing" />
                            <p v-if="form.errors.title" class="text-xs text-destructive">{{ form.errors.title }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <Label for="slug">
                                Slug <span class="text-destructive ml-0.5">*</span>
                            </Label>
                            <Input id="slug" v-model="form.slug" required :disabled="form.processing" />
                            <p v-if="form.errors.slug" class="text-xs text-destructive">{{ form.errors.slug }}</p>
                        </div>
                    </div>

                    <!-- Content fields from template — rendered dynamically from resource response -->
                    <div class="rounded-lg border bg-card p-5">
                        <h3 class="mb-1 text-sm font-semibold">{{ pageData?.template_label ?? 'Content' }}</h3>
                        <p class="mb-4 text-xs text-muted-foreground">Fill in the fields defined by this page template.</p>
                        <DynamicForm
                            :model-value="form.content"
                            :fields="fields"
                            @update:model-value="form.content = $event"
                        />
                    </div>

                    <!-- SEO Meta -->
                    <div class="rounded-lg border bg-card p-5">
                        <h3 class="mb-1 text-sm font-semibold">SEO Meta</h3>
                        <p class="mb-4 text-xs text-muted-foreground">Search engine optimization and social sharing settings.</p>

                        <div class="grid gap-5">
                            <!-- Google Search -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Google Search</h4>
                                <div class="space-y-1.5">
                                    <Label for="meta_title">Meta Title</Label>
                                    <Input id="meta_title" v-model="form.meta.meta_title" placeholder="Page title for search results" :disabled="form.processing" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="meta_description">Meta Description</Label>
                                    <textarea
                                        id="meta_description"
                                        class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                        v-model="form.meta.meta_description"
                                        placeholder="Brief description for search results (150-160 chars)"
                                        :disabled="form.processing"
                                    />
                                </div>
                            </div>

                            <!-- Open Graph (Facebook, LinkedIn) -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Social Sharing (Open Graph)</h4>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-1.5">
                                        <Label for="og_title">OG Title</Label>
                                        <Input id="og_title" v-model="form.meta.og_title" placeholder="Title for social shares" :disabled="form.processing" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label for="og_image">OG Image URL</Label>
                                        <Input id="og_image" v-model="form.meta.og_image" placeholder="https://example.com/image.jpg (1200x630)" :disabled="form.processing" />
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="og_description">OG Description</Label>
                                    <textarea
                                        id="og_description"
                                        class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                        v-model="form.meta.og_description"
                                        placeholder="Description for social shares"
                                        :disabled="form.processing"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <Button type="button" variant="outline" @click="cancelEdit">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <Save class="h-4 w-4" />
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
