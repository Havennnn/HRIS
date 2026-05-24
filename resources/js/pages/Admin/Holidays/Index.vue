<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index, restore } from '@/routes/holidays';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Filter } from 'lucide-vue-next';
import type { DataCalendarActionsConfig, DataCalendarEventDisplay } from 'piacore/components/DataCalendar.vue';
import DataCalendar from 'piacore/components/DataCalendar.vue';
import DataHeader from 'piacore/components/DataHeader.vue';
import ImportExportDialog from 'piacore/components/ImportExportDialog.vue';
import type { PaginatedData } from 'piacore/Interface/Pagination';
import { Option } from 'piacore/Interface/Selector';
import { computed, ref } from 'vue';
import type { HolidayIndexResource } from './index';

const props = defineProps<{
    data: PaginatedData<HolidayIndexResource>;
    types?: Option[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Holidays',
        href: index().url,
    },
];

const page = usePage();
const activeTab = computed<string>(() => {
    const url = new URL(page.url, window.location.origin);
    return url.searchParams.get('tab') ?? 'default';
});

const search = ref<string>('');
const searchPlaceholder = ref('Search holidays...');

const tabs = computed(() => [
    {
        key: 'default',
        label: 'All',
        count: props.data.count?.defaultCount ?? 0,
    },
    {
        key: 'archived',
        label: 'Archived',
        count: props.data.count?.archivedCount ?? 0,
    },
    {
        key: 'activity_logs',
        label: 'Activity Log',
    },
]);

const typeFilterOptions = computed(() => {
    return (props.types ?? []).map(t => ({
        value: t.value,
        label: t.label,
    }));
});

const filters = [
    {
        key: 'type',
        label: 'Type',
        icon: Filter,
        options: typeFilterOptions.value,
    },
];

// Calendar events - convert from HolidayIndexResource to CalendarEvent format
const calendarEvents = computed(() => {
    return props.data.data.map(h => ({
        id: h.id,
        date: h.date_raw,
        name: h.name,
        type: h.type_value,
        type_label: h.type,
        description: h.description,
        created_at: h.created_at
    }));
});

// Event display configuration
const eventDisplay: DataCalendarEventDisplay = {
    label: (event) => event.name,
    dotClass: (event) => event.type === 1 ? 'bg-destructive' : 'bg-primary',
};

// Calendar actions configuration
const calendarActions: DataCalendarActionsConfig = {
    editRoute: (event) => edit({ holiday: event.id as number }).url,
    deleteRoute: (event) => destroy({ holiday: event.id as number }).url,
    restoreRoute: (event) => restore({ holiday: event.id as number }).url,
    destructiveAction: activeTab.value === 'archived' ? 'restore' : 'delete',
    deleteConfirmMessage: 'Are you sure you want to delete this holiday?',
    restoreConfirmMessage: 'Are you sure you want to restore this holiday?',
};
</script>

<template>
    <Head title="Holidays" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4 md:p-6">
            <Card>
                <DataHeader
                    variant="card"
                    title="Holidays"
                    description="Manage holidays within the organization."
                >
                    <template #actions>
                        <ImportExportDialog />
                        <Button as-child>
                            <Link :href="create().url">Add Holiday</Link>
                        </Button>
                    </template>
                </DataHeader>

                <CardContent class="-mt-3 space-y-0">
                    <DataCalendar
                        :events="calendarEvents"
                        :event-display="eventDisplay"
                        :active-tab="activeTab"
                        :show-tabs="true"
                        :tabs="tabs"
                        :search-query="search"
                        :search-placeholder="searchPlaceholder"
                        :show-search="true"
                        :filters="filters"
                        :actions="calendarActions"
                        date-key="date"
                        empty-message="No holidays found."
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
