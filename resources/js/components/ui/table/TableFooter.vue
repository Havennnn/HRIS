<script setup lang="ts">
import { cn } from '@/lib/utils'
import { type HTMLAttributes, computed } from 'vue'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})
</script>

<template>
    <tfoot :class="cn('border-t bg-muted/50 font-medium [&>tr]:last:border-b-0', props.class)" v-bind="delegatedProps">
        <slot />
    </tfoot>
</template>
