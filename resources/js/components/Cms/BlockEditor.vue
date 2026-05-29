<script setup lang="ts">
import { GripVertical, Loader2, Pencil, Plus, Trash2, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BlockConfig, BlockField, CmsBlock } from '@/Pages/Admin/Pages/index';
const upload = () => ({ url: '/admin/pages/upload', method: 'post' });
import HeadingBlock from './Blocks/Heading.vue';
import ImageBlock from './Blocks/Image.vue';
import ListBlock from './Blocks/List.vue';
import RichTextBlock from './Blocks/RichText.vue';

const props = defineProps<{
    blocks: CmsBlock[];
    blockTypes: Record<string, BlockConfig>;
}>();

const emit = defineEmits<{
    remove: [index: number];
    move: [fromIndex: number, toIndex: number];
}>();

const editingIndex = ref<number | null>(null);
const uploadingField = ref<string | null>(null);

const blockRenderers: Record<string, any> = {
    heading: HeadingBlock,
    rich_text: RichTextBlock,
    image: ImageBlock,
    list: ListBlock,
};

function getBlockConfig(type: string): BlockConfig | undefined {
    return props.blockTypes[type];
}

function toggleEdit(index: number): void {
    editingIndex.value = editingIndex.value === index ? null : index;
}

function onDragStart(event: DragEvent, index: number): void {
    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(index));
    }
}

function onDrop(event: DragEvent, targetIndex: number): void {
    event.preventDefault();
    const fromIndex = parseInt(event.dataTransfer?.getData('text/plain') ?? '', 10);
    if (isNaN(fromIndex) || fromIndex === targetIndex) return;
    emit('move', fromIndex, targetIndex);
}

function onDragOver(event: DragEvent): void {
    event.preventDefault();
    if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'move';
    }
}

const blockTypeOptions = computed(() => {
    return Object.entries(props.blockTypes).map(([key, config]) => ({
        value: key,
        label: config.label,
        icon: config.icon,
    }));
});

function getPlaceholder(field: BlockField): string {
    return (field as any).placeholder ?? `Enter ${field.label.toLowerCase()}`;
}

function addRepeaterItem(block: CmsBlock, fieldKey: string, fieldConfig: BlockField): void {
    const items = (block.data[fieldKey] as any[]) ?? [];
    const defaults: Record<string, any> = {};
    for (const k of Object.keys(fieldConfig.fields ?? {})) {
        const f = fieldConfig.fields[k];
        defaults[k] = f.type === 'select' ? (f.options?.[0]?.value ?? '') : '';
    }
    items.push(defaults);
    block.data[fieldKey] = items;
}

function removeRepeaterItem(block: CmsBlock, fieldKey: string, index: number): void {
    const items = (block.data[fieldKey] as any[]) ?? [];
    items.splice(index, 1);
}

function setRepeaterValue(block: CmsBlock, fieldKey: string, itemIndex: number, subKey: string, value: string): void {
    const items = (block.data[fieldKey] as any[]) ?? [];
    if (items[itemIndex]) {
        items[itemIndex][subKey] = value;
    }
}

async function handleFileUploadToRepeater(block: CmsBlock, fieldKey: string, itemIndex: number, subKey: string): Promise<void> {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';

    input.onchange = async () => {
        const file = input.files?.[0];
        if (!file) return;

        uploadingField.value = `${block.id}_${fieldKey}_${itemIndex}_${subKey}`;

        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch(upload().url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
            });

            const result = await response.json();
            if (result.url) {
                const items = (block.data[fieldKey] as any[]) ?? [];
                if (items[itemIndex]) {
                    items[itemIndex][subKey] = result.url;
                }
            }
        } catch (error) {
            console.error('Upload failed:', error);
        } finally {
            uploadingField.value = null;
        }
    };

    input.click();
}

async function handleFileUpload(block: CmsBlock, fieldKey: string): Promise<void> {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';

    input.onchange = async () => {
        const file = input.files?.[0];
        if (!file) return;

        uploadingField.value = `${block.id}_${fieldKey}`;

        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch(upload().url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
            });

            const result = await response.json();
            if (result.url) {
                block.data[fieldKey] = result.url;
            }
        } catch (error) {
            console.error('Upload failed:', error);
        } finally {
            uploadingField.value = null;
        }
    };

    input.click();
}
</script>

<template>
    <div class="grid gap-4">
        <div v-if="blocks.length === 0" class="py-12 text-center">
            <div class="text-4xl text-muted-foreground/30 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <p class="text-sm text-muted-foreground">No blocks yet. Add one below to start building your page.</p>
        </div>

        <div class="grid gap-3">
            <div
                v-for="(block, index) in blocks"
                :key="block.id"
                class="group relative rounded-xl border-2 bg-card transition-all hover:border-primary/30"
                :class="[editingIndex === index ? 'border-primary shadow-md' : 'border-border']"
            >
                <!-- Floating toolbar -->
                <div class="absolute -top-3 right-2 z-10 flex items-center gap-1 rounded-md border bg-background px-1.5 py-1 shadow-sm opacity-0 transition-opacity group-hover:opacity-100">
                    <span
                        class="cursor-grab rounded p-0.5 text-muted-foreground hover:bg-accent hover:text-foreground"
                        draggable="true"
                        @dragstart="onDragStart($event, index)"
                        @dragover="onDragOver"
                        @drop.prevent="onDrop($event, index)"
                    >
                        <GripVertical class="h-3.5 w-3.5" />
                    </span>
                    <button
                        type="button"
                        class="rounded p-0.5 text-muted-foreground hover:bg-accent hover:text-foreground"
                        @click="toggleEdit(index)"
                    >
                        <Pencil class="h-3.5 w-3.5" />
                    </button>
                    <button
                        type="button"
                        class="rounded p-0.5 text-destructive hover:bg-destructive/10"
                        @click="emit('remove', index)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>

                <!-- Block type badge -->
                <div class="absolute -left-3 top-3 z-10 rounded bg-muted px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-muted-foreground shadow-sm">
                    {{ getBlockConfig(block.type)?.label ?? block.type }}
                </div>

                <!-- Live preview -->
                <div
                    class="min-h-[60px] cursor-pointer px-6 py-5"
                    @click="toggleEdit(index)"
                >
                    <component
                        :is="blockRenderers[block.type]"
                        v-if="blockRenderers[block.type]"
                        :data="block.data"
                    />
                    <div v-else class="rounded bg-muted p-4 text-center text-sm text-muted-foreground">
                        Unknown block type: {{ block.type }}
                    </div>
                </div>

                <!-- Inline editor (expands when editing) -->
                <div
                    v-if="editingIndex === index"
                    class="border-t bg-muted/30 px-6 py-4"
                >
                    <div class="grid gap-4">
                        <template
                            v-for="(fieldConfig, fieldKey) in getBlockConfig(block.type)?.fields"
                            :key="fieldKey"
                        >
                            <div v-if="fieldConfig.type === 'text'" class="space-y-1.5">
                                <Label :for="`${block.id}_${fieldKey}`" class="text-xs font-medium">
                                    {{ fieldConfig.label }}
                                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                                </Label>
                                <Input
                                    :id="`${block.id}_${fieldKey}`"
                                    :model-value="block.data[fieldKey] as string"
                                    @update:model-value="block.data[fieldKey] = $event"
                                    :placeholder="getPlaceholder(fieldConfig)"
                                    class="h-8 text-sm"
                                />
                            </div>

                            <div v-else-if="fieldConfig.type === 'textarea'" class="space-y-1.5">
                                <Label :for="`${block.id}_${fieldKey}`" class="text-xs font-medium">
                                    {{ fieldConfig.label }}
                                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                                </Label>
                                <textarea
                                    :id="`${block.id}_${fieldKey}`"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    :value="block.data[fieldKey] as string"
                                    @input="block.data[fieldKey] = ($event.target as HTMLTextAreaElement).value"
                                    :placeholder="getPlaceholder(fieldConfig)"
                                />
                            </div>

                            <div v-else-if="fieldConfig.type === 'select'" class="space-y-1.5">
                                <Label :for="`${block.id}_${fieldKey}`" class="text-xs font-medium">
                                    {{ fieldConfig.label }}
                                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                                </Label>
                                <select
                                    :id="`${block.id}_${fieldKey}`"
                                    class="flex h-8 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    :value="block.data[fieldKey] as string"
                                    @change="block.data[fieldKey] = ($event.target as HTMLSelectElement).value"
                                >
                                    <option v-for="opt in fieldConfig.options" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>

                            <div v-else-if="fieldConfig.type === 'repeater'" class="space-y-2">
                                <Label class="text-xs font-medium">
                                    {{ fieldConfig.label }}
                                </Label>
                                <div class="grid gap-2">
                                    <div
                                        v-for="(item, ri) in (block.data[fieldKey] as any[] ?? [])"
                                        :key="ri"
                                        class="rounded-lg border bg-background p-3"
                                    >
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="text-[10px] font-medium uppercase text-muted-foreground">
                                                Item {{ ri + 1 }}
                                            </span>
                                            <button
                                                type="button"
                                                class="text-destructive hover:text-destructive/80"
                                                @click="removeRepeaterItem(block, fieldKey, ri)"
                                            >
                                                <Trash2 class="h-3 w-3" />
                                            </button>
                                        </div>
                                    <div class="grid gap-2">
                                        <template
                                            v-for="(subField, subKey) in fieldConfig.fields"
                                            :key="subKey"
                                        >
                                            <div v-if="subField.type === 'text'" class="space-y-1">
                                                <Label :for="`${block.id}_${fieldKey}_${ri}_${subKey}`" class="text-[11px]">
                                                    {{ subField.label }}
                                                    <span v-if="subField.required" class="text-destructive">*</span>
                                                </Label>
                                                <Input
                                                    :id="`${block.id}_${fieldKey}_${ri}_${subKey}`"
                                                    :model-value="(item as any)[subKey] as string"
                                                    @update:model-value="setRepeaterValue(block, fieldKey, ri, subKey, $event)"
                                                    :placeholder="getPlaceholder(subField)"
                                                    class="h-7 text-xs"
                                                />
                                            </div>

                                            <div v-else-if="subField.type === 'select'" class="space-y-1">
                                                <Label :for="`${block.id}_${fieldKey}_${ri}_${subKey}`" class="text-[11px]">
                                                    {{ subField.label }}
                                                    <span v-if="subField.required" class="text-destructive">*</span>
                                                </Label>
                                                <select
                                                    :id="`${block.id}_${fieldKey}_${ri}_${subKey}`"
                                                    class="flex h-7 w-full rounded-md border border-input bg-background px-2 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                                    :value="(item as any)[subKey] as string"
                                                    @change="setRepeaterValue(block, fieldKey, ri, subKey, ($event.target as HTMLSelectElement).value)"
                                                >
                                                    <option value="" disabled>Select...</option>
                                                    <option v-for="opt in subField.options" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div v-else-if="subField.type === 'file'"
                                                v-show="subKey !== 'image' || (item as any)['has_image'] === 'yes'"
                                                class="space-y-1">
                                                <Label :for="`${block.id}_${fieldKey}_${ri}_${subKey}`" class="text-[11px]">
                                                    {{ subField.label }}
                                                </Label>
                                                <div class="flex flex-col gap-1.5">
                                                    <!-- Upload button -->
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-dashed border-input bg-background px-3 py-2 text-xs font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                                                        :disabled="uploadingField === `${block.id}_${fieldKey}_${ri}_${subKey}`"
                                                        @click="handleFileUploadToRepeater(block, fieldKey, ri, subKey)"
                                                    >
                                                        <Loader2 v-if="uploadingField === `${block.id}_${fieldKey}_${ri}_${subKey}`" class="h-3.5 w-3.5 animate-spin" />
                                                        <Upload v-else class="h-3.5 w-3.5" />
                                                        {{ uploadingField === `${block.id}_${fieldKey}_${ri}_${subKey}` ? 'Uploading...' : 'Click to upload image' }}
                                                    </button>
                                                    <!-- Preview + remove -->
                                                    <div v-if="(item as any)[subKey]" class="flex items-center gap-2 rounded-md border bg-muted/20 p-2">
                                                        <img
                                                            :src="(item as any)[subKey] as string"
                                                            class="h-12 w-12 shrink-0 rounded object-cover"
                                                        />
                                                        <span class="min-w-0 flex-1 truncate text-[10px] text-muted-foreground">
                                                            Image uploaded
                                                        </span>
                                                        <button
                                                            type="button"
                                                            class="shrink-0 text-destructive hover:text-destructive/80"
                                                            @click="setRepeaterValue(block, fieldKey, ri, subKey, '')"
                                                            title="Remove image"
                                                        >
                                                            <Trash2 class="h-3 w-3" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            </template>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center gap-1 rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground"
                                        @click="addRepeaterItem(block, fieldKey, fieldConfig)"
                                    >
                                        <Plus class="h-3 w-3" />
                                        Add {{ fieldConfig.label.slice(0, -1) || 'Item' }}
                                    </button>
                                </div>
                            </div>

                            <div v-else-if="fieldConfig.type === 'file'" class="space-y-1.5">
                                <Label :for="`${block.id}_${fieldKey}`" class="text-xs font-medium">
                                    {{ fieldConfig.label }}
                                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                                </Label>
                                <div class="flex flex-col gap-2">
                                    <button
                                        type="button"
                                        variant="outline"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-dashed border-input bg-background px-4 py-2.5 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                                        :disabled="uploadingField === `${block.id}_${fieldKey}`"
                                        @click="handleFileUpload(block, fieldKey)"
                                    >
                                        <Loader2 v-if="uploadingField === `${block.id}_${fieldKey}`" class="h-4 w-4 animate-spin" />
                                        <Upload v-else class="h-4 w-4" />
                                        {{ uploadingField === `${block.id}_${fieldKey}` ? 'Uploading...' : 'Click to upload image' }}
                                    </button>
                                    <div v-if="block.data[fieldKey]" class="flex items-center gap-3 rounded-lg border bg-muted/20 p-3">
                                        <img
                                            :src="block.data[fieldKey] as string"
                                            class="h-16 w-16 shrink-0 rounded-md object-cover border"
                                            @error="(e) => { (e.target as HTMLImageElement).classList.add('hidden') }"
                                        />
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-medium truncate">Uploaded image</p>
                                        </div>
                                        <button
                                            type="button"
                                            class="shrink-0 text-destructive hover:text-destructive/80"
                                            @click="block.data[fieldKey] = ''"
                                            title="Remove image"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drop zone indicator for reordering -->
        <div
            v-for="(block, index) in blocks"
            :key="`drop-${block.id}`"
            class="h-1 rounded-full transition-all"
            @dragover="onDragOver"
            @drop="onDrop($event, index)"
        />

        <!-- Add Block -->
        <div class="flex items-center justify-center border-2 border-dashed rounded-xl py-8 transition-colors hover:border-primary/50 hover:bg-muted/20">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <span class="text-sm text-muted-foreground mr-1">Add:</span>
                <button
                    v-for="opt in blockTypeOptions"
                    :key="opt.value"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border bg-background px-3 py-1.5 text-sm font-medium text-foreground shadow-sm transition-all hover:border-primary hover:text-primary hover:shadow-md"
                    @click="$emit('addBlock', opt.value)"
                >
                    {{ opt.label }}
                </button>
            </div>
        </div>
    </div>
</template>
