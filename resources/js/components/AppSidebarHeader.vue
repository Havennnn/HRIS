<script  setup  lang="ts">
import NavUser from '@/components/NavUser.vue';
import type { BreadcrumbItem } from '@/types';
import AppSidebarHeaderDefault from 'piacore/components/AppSidebarHeaderDefault.vue';
import type { Component } from 'vue';
import { useSlots } from 'vue';

type  HeaderActionItem  = {
	key: string;
	icon: Component;
	href?: string;
	count?: number;
};

const  slots  =  useSlots();

withDefaults(
	defineProps<{
		breadcrumbs?: BreadcrumbItem[];
		showUserMenu?: boolean;
		actionItems?: HeaderActionItem[];
	}>(),
	{
		breadcrumbs: () => [],
		showUserMenu: true,
		actionItems: () => [],
	},
);
</script>

<template>
	<AppSidebarHeaderDefault
		:breadcrumbs="breadcrumbs"
		:show-user-menu="showUserMenu"
		:action-items="actionItems"
	>

		<template  v-if="slots.left" #left="slotProps">
		    <slot  name="left"  v-bind="slotProps"  />
		</template>

		<template  v-if="slots.trigger" #trigger>
		    <slot  name="trigger"  />
		</template>

		<template  v-if="slots.breadcrumbs" #breadcrumbs="slotProps">
		    <slot  name="breadcrumbs"  v-bind="slotProps"  />
		</template>

		<template  v-if="slots.actions" #actions="slotProps">
		    <slot  name="actions"  v-bind="slotProps"  />
		</template>

		<template  v-if="slots.right" #right>
		    <slot  name="right"  />
		</template>

		<template  v-else #right>
			<div  class="flex items-center gap-1.5">
				<template  v-for="item  in  actionItems" :key="item.key">
					<a
						v-if="item.href"
						:href="item.href"
						class="relative inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
						:aria-label="item.key"
					>
						<component :is="item.icon"  class="size-4" />
						<span
						v-if="item.count  &&  item.count  >  0"
						class="absolute -right-1 -top-1 inline-flex min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-medium leading-none text-primary-foreground"
						>
							{{  item.count  }}
						</span>
					</a>
				<button
					v-else
					type="button"
					class="relative inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
					:aria-label="item.key"
				>
					<component :is="item.icon"  class="size-4" />
					<span
						v-if="item.count  &&  item.count  >  0"
						class="absolute -right-1 -top-1 inline-flex min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-medium leading-none text-primary-foreground"
					>
					{{  item.count  }}
					</span>
				</button>
			    </template>
			    <NavUser  v-if="showUserMenu" />
			</div>
		</template>
	</AppSidebarHeaderDefault>
</template>
