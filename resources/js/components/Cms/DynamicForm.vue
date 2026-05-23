<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Loader2, Plus, Trash2, Upload } from 'lucide-vue-next';
import { ref } from 'vue';
import type { BlockField } from '@/Pages/Admin/Pages/index';

const props = defineProps<{
    modelValue: Record<string, any>;
    fields: Record<string, BlockField>;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>];
}>();

const uploadingField = ref<string | null>(null);

function updateField(key: string, value: any): void {
    emit('update:modelValue', { ...props.modelValue, [key]: value });
}

function addRepeaterItem(fieldKey: string, fieldConfig: BlockField): void {
    const items = [...((props.modelValue[fieldKey] as any[]) ?? [])];
    const defaults: Record<string, any> = {};
    for (const k of Object.keys(fieldConfig.fields ?? {})) {
        const f = fieldConfig.fields[k];
        defaults[k] = f.type === 'select' ? (f.options?.[0]?.value ?? '') : '';
    }
    items.push(defaults);
    updateField(fieldKey, items);
}

function removeRepeaterItem(fieldKey: string, index: number): void {
    const items = [...((props.modelValue[fieldKey] as any[]) ?? [])];
    items.splice(index, 1);
    updateField(fieldKey, items);
}

function setRepeaterValue(fieldKey: string, itemIndex: number, subKey: string, value: string): void {
    const items = [...((props.modelValue[fieldKey] as any[]) ?? [])];
    if (items[itemIndex]) {
        items[itemIndex] = { ...items[itemIndex], [subKey]: value };
    }
    updateField(fieldKey, items);
}

function handleFileUpload(fieldKey: string): void {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';

    input.onchange = () => {
        const file = input.files?.[0];
        if (!file) return;

        uploadingField.value = fieldKey;

        const reader = new FileReader();
        reader.onload = () => {
            updateField(fieldKey, reader.result as string);
            uploadingField.value = null;
        };
        reader.onerror = () => {
            uploadingField.value = null;
        };
        reader.readAsDataURL(file);
    };

    input.click();
}

function handleFileUploadToRepeater(fieldKey: string, itemIndex: number, subKey: string): void {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml';

    input.onchange = () => {
        const file = input.files?.[0];
        if (!file) return;

        uploadingField.value = `${fieldKey}_${itemIndex}_${subKey}`;

        const reader = new FileReader();
        reader.onload = () => {
            setRepeaterValue(fieldKey, itemIndex, subKey, reader.result as string);
            uploadingField.value = null;
        };
        reader.onerror = () => {
            uploadingField.value = null;
        };
        reader.readAsDataURL(file);
    };

    input.click();
}
</script>

<template>
    <div class="grid gap-5">
        <template v-for="(fieldConfig, fieldKey) in fields" :key="fieldKey">
            <!-- Text Input -->
            <div v-if="fieldConfig.type === 'text'" class="space-y-1.5">
                <Label :for="`field_${fieldKey}`">
                    {{ fieldConfig.label }}
                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                </Label>
                <Input
                    :id="`field_${fieldKey}`"
                    :model-value="(modelValue[fieldKey] as string) ?? ''"
                    @update:model-value="updateField(fieldKey, $event)"
                />
            </div>

            <!-- Textarea -->
            <div v-else-if="fieldConfig.type === 'textarea'" class="space-y-1.5">
                <Label :for="`field_${fieldKey}`">
                    {{ fieldConfig.label }}
                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                </Label>
                <textarea
                    :id="`field_${fieldKey}`"
                    class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    :value="(modelValue[fieldKey] as string) ?? ''"
                    @input="updateField(fieldKey, ($event.target as HTMLTextAreaElement).value)"
                />
            </div>

            <!-- Select -->
            <div v-else-if="fieldConfig.type === 'select'" class="space-y-1.5">
                <Label>
                    {{ fieldConfig.label }}
                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                </Label>
                <Select
                    :model-value="(modelValue[fieldKey] as string) ?? ''"
                    @update:model-value="updateField(fieldKey, $event)"
                >
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="opt in fieldConfig.options"
                            :key="String(opt.value)"
                            :value="String(opt.value)"
                        >
                            {{ opt.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- File Upload -->
            <div v-else-if="fieldConfig.type === 'file'" class="space-y-1.5">
                <Label>
                    {{ fieldConfig.label }}
                    <span v-if="fieldConfig.required" class="text-destructive ml-0.5">*</span>
                </Label>
                <div class="flex flex-col gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md border border-dashed border-input bg-background px-4 py-2.5 text-sm font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                        :disabled="uploadingField === fieldKey"
                        @click="handleFileUpload(fieldKey)"
                    >
                        <Loader2 v-if="uploadingField === fieldKey" class="h-4 w-4 animate-spin" />
                        <Upload v-else class="h-4 w-4" />
                        {{ uploadingField === fieldKey ? 'Uploading...' : 'Click to upload image' }}
                    </button>
                    <div v-if="modelValue[fieldKey]" class="flex items-center gap-3 rounded-lg border bg-muted/20 p-3">
                        <img :src="modelValue[fieldKey] as string" class="h-16 w-16 shrink-0 rounded-md object-cover border" />
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium truncate">Uploaded image</p>
                        </div>
                        <button
                            type="button"
                            class="shrink-0 text-destructive hover:text-destructive/80"
                            @click="updateField(fieldKey, '')"
                            title="Remove image"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Repeater -->
            <div v-else-if="fieldConfig.type === 'repeater'" class="space-y-2">
                <Label class="text-sm font-medium">{{ fieldConfig.label }}</Label>
                <div class="grid gap-2">
                    <div
                        v-for="(item, ri) in (modelValue[fieldKey] as any[] ?? [])"
                        :key="ri"
                        class="rounded-lg border bg-card p-4"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-medium uppercase text-muted-foreground">
                                {{ fieldConfig.label.slice(0, -1) || 'Item' }} {{ ri + 1 }}
                            </span>
                            <button
                                type="button"
                                class="text-destructive hover:text-destructive/80"
                                @click="removeRepeaterItem(fieldKey, ri)"
                            >
                                <Trash2 class="h-3 w-3" />
                            </button>
                        </div>
                        <div class="grid gap-3">
                            <template v-for="(subField, subKey) in fieldConfig.fields" :key="subKey">
                                <!-- Sub: Text -->
                                <div v-if="subField.type === 'text'" class="space-y-1">
                                    <Label :for="`${fieldKey}_${ri}_${subKey}`" class="text-[11px]">
                                        {{ subField.label }}
                                        <span v-if="subField.required" class="text-destructive">*</span>
                                    </Label>
                                    <Input
                                        :id="`${fieldKey}_${ri}_${subKey}`"
                                        :model-value="(item as any)[subKey] as string ?? ''"
                                        @update:model-value="setRepeaterValue(fieldKey, ri, subKey, $event)"
                                        class="h-7 text-xs"
                                    />
                                </div>

                                <!-- Sub: Select -->
                                <div v-else-if="subField.type === 'select'" class="space-y-1">
                                    <Label class="text-[11px]">
                                        {{ subField.label }}
                                        <span v-if="subField.required" class="text-destructive">*</span>
                                    </Label>
                                    <Select
                                        :model-value="(item as any)[subKey] as string ?? ''"
                                        @update:model-value="setRepeaterValue(fieldKey, ri, subKey, $event)"
                                    >
                                        <SelectTrigger class="w-full h-7 text-xs">
                                            <SelectValue placeholder="Select..." />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="opt in subField.options"
                                                :key="String(opt.value)"
                                                :value="String(opt.value)"
                                            >
                                                {{ opt.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Sub: File upload -->
                                <div v-else-if="subField.type === 'file'"
                                    v-show="subKey !== 'image' || (item as any)['has_image'] === 'yes'"
                                    class="space-y-1">
                                    <Label class="text-[11px]">{{ subField.label }}</Label>
                                    <div class="flex flex-col gap-1.5">
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-dashed border-input bg-background px-3 py-2 text-xs font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                                            :disabled="uploadingField === `${fieldKey}_${ri}_${subKey}`"
                                            @click="handleFileUploadToRepeater(fieldKey, ri, subKey)"
                                        >
                                            <Loader2 v-if="uploadingField === `${fieldKey}_${ri}_${subKey}`" class="h-3.5 w-3.5 animate-spin" />
                                            <Upload v-else class="h-3.5 w-3.5" />
                                            {{ uploadingField === `${fieldKey}_${ri}_${subKey}` ? 'Uploading...' : 'Click to upload' }}
                                        </button>
                                        <div v-if="(item as any)[subKey]" class="flex items-center gap-2 rounded-md border bg-muted/20 p-2">
                                            <img :src="(item as any)[subKey]" class="h-12 w-12 shrink-0 rounded object-cover" />
                                            <span class="min-w-0 flex-1 truncate text-[10px] text-muted-foreground">Image uploaded</span>
                                            <button type="button" class="shrink-0 text-destructive hover:text-destructive/80" @click="setRepeaterValue(fieldKey, ri, subKey, '')" title="Remove image">
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
                        class="inline-flex items-center justify-center gap-1 rounded-md border border-dashed border-input bg-background px-3 py-2 text-xs font-medium text-foreground shadow-sm hover:bg-accent hover:text-accent-foreground"
                        @click="addRepeaterItem(fieldKey, fieldConfig)"
                    >
                        <Plus class="h-3 w-3" />
                        Add {{ fieldConfig.label.slice(0, -1) || 'Item' }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
