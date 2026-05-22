<script setup lang="ts">
import { computed } from 'vue'
import type { SelectRootEmits, SelectRootProps } from "reka-ui"
import {
  SelectContent,
  SelectItem,
  SelectItemIndicator,
  SelectItemText,
  SelectRoot,
  SelectScrollDownButton,
  SelectScrollUpButton,
  SelectTrigger,
  SelectValue,
  SelectGroup,
  SelectLabel,
} from "reka-ui"
import { ChevronDown, ChevronUp, Check } from "lucide-vue-next"
import { cn } from "@/lib/utils"

const props = defineProps<{
  modelValue?: string | number
  options?: Array<{ value: string | number; label: string }>
  placeholder?: string
  class?: string
  label?: string
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void
}>()

const selectedLabel = computed(() => {
  if (!props.modelValue || !props.options) return ''
  const option = props.options.find(o => String(o.value) === String(props.modelValue))
  return option?.label ?? ''
})

function handleUpdate(value: string) {
  emit('update:modelValue', value)
}
</script>

<template>
  <SelectRoot
    :modelValue="modelValue"
    @update:modelValue="handleUpdate"
    :disabled="disabled"
  >
    <SelectTrigger
      :class="cn(
        'flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
        'focus:border-ring focus:ring-ring/50',
        props.class
      )"
    >
      <SelectValue :placeholder="placeholder">
        {{ selectedLabel || placeholder }}
      </SelectValue>
      <SelectIcon as-child>
        <ChevronDown class="h-4 w-4 opacity-50" />
      </SelectIcon>
    </SelectTrigger>

    <SelectPortal>
      <SelectContent position="popper" sideOffset="4" class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2">
        <SelectScrollUpButton class="flex cursor-default items-center justify-center py-1">
          <ChevronUp class="h-4 w-4" />
        </SelectScrollUpButton>
        <SelectViewport class="p-1">
          <SelectGroup v-if="label">
            <SelectLabel class="px-2 py-1.5 text-xs text-muted-foreground">{{ label }}</SelectLabel>
          </SelectGroup>
          <SelectItem
            v-for="option in options"
            :key="option.value"
            :value="String(option.value)"
            class="relative flex w-full cursor-default items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
          >
            <SelectItemIndicator class="absolute right-2 flex h-3.5 w-3.5 items-center justify-center">
              <Check class="h-4 w-4" />
            </SelectItemIndicator>
            <SelectItemText>{{ option.label }}</SelectItemText>
          </SelectItem>
        </SelectViewport>
        <SelectScrollDownButton class="flex cursor-default items-center justify-center py-1">
          <ChevronDown class="h-4 w-4" />
        </SelectScrollDownButton>
      </SelectContent>
    </SelectPortal>
  </SelectRoot>
</template>