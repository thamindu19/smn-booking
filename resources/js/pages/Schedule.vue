<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import multiMonthPlugin from '@fullcalendar/multimonth';
import FullCalendar from '@fullcalendar/vue3';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';
import 'tippy.js/themes/material.css';
import { computed, onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Schedule',
        href: '/schedule',
    },
];

type Booking = {
    id: number;
    poya_day_id: number;
    name: string;
    email: string;
    phone: string;
    notes: string;
    status: string;
    created_at: string;
    date: string;
    month: string;
};
type Event = {
    id: string;
    title: string;
    start: string;
    booking_id: string | null;
    booking: Booking | null;
    pending_bookings;
    status: string;
};

const events = ref<Event[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/api/poya-days');

        events.value = response.data.data.map((event: any) => ({
            id: event.id,
            title: `${event.month.split(' ').slice(0, -1).join(' ')} Full Moon Poya Day`,
            start: new Date(event.date).toISOString().split('T')[0],
            extendedProps: {
                booking_id: event.booking_id || null,
                booking: event.booking || null,
                status: event.booking_id ? 'Booked' : event.pending_bookings > 0 ? 'Pending' : 'Available',
            },

            backgroundColor: event.booking_id ? 'gray' : event.pending_bookings > 0 ? 'yellow' : 'green',
            borderColor: event.booking_id ? 'gray' : event.pending_bookingsh > 0 ? 'yellow' : 'green',
            textColor: event.booking_id ? 'white' : event.pending_bookings > 0 ? 'black' : 'white',
        }));
    } catch (error) {
        console.error('Error fetching events:', error);
    }
});

const calendarOptions = computed(() => ({
    plugins: [listPlugin, multiMonthPlugin, interactionPlugin],
    headerToolbar: {
        left: 'title',
        center: 'listYear,multiMonthYear',
        right: 'prev,next',
    },
    buttonText: {
        multiMonthYear: 'Calendar View',
        listYear: 'List View',
    },
    initialView: 'listYear',
    events: events.value,
    editable: false,
    selectable: false,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    eventClick: null,
    height: 'auto',
    eventDidMount: function (info) {
        info.el.style.overflow = 'hidden';
        if (info.event.extendedProps.status === 'Booked') {
            tippy(info.el, {
                content: `
                    <div class="booking-tooltip">
                    <strong>Name:</strong> ${info.event.extendedProps.booking.name}<br/>
                    <strong>Email:</strong> ${info.event.extendedProps.booking.email}<br/>
                    <strong>Date:</strong> ${info.event.extendedProps.booking.created_at.split('T')[0]}<br/>
                    <strong>Phone:</strong> ${info.event.extendedProps.booking.phone}<br/>
                    <strong>Notes:</strong> ${info.event.extendedProps.booking.notes || '—'}
                    </div>
                `,
                allowHTML: true,
                placement: 'auto',
                theme: 'light',
                interactive: true,
                appendTo: () => document.body,
                zIndex: 9999,
            });
        }
    },
}));
</script>

<template>
    <Head title="Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-7 text-sm">
            <FullCalendar class="demo-app-calendar" :options="calendarOptions">
                <template v-slot:eventContent="arg">
                    <div v-if="arg.view.type === 'listYear'" class="flex w-full items-center justify-between">
                        <span class="w-2/5 font-medium">{{ arg.event.title }}</span>
                        <span v-if="arg.event.extendedProps.status === 'Booked'" class="w-2/5 text-left text-sm text-gray-600">...</span>
                        <Badge
                            class="rounded-full px-2 py-1 font-semibold sm:w-1/5"
                            :class="{
                                'bg-green-100 text-green-700': arg.event.extendedProps.status === 'Available',
                                'bg-yellow-100 text-yellow-700': arg.event.extendedProps.status === 'Pending',
                                'bg-gray-100 text-gray-700': arg.event.extendedProps.status === 'Booked',
                            }"
                            >{{ arg.event.extendedProps.status }}</Badge
                        >
                    </div>
                    <div v-else>
                        <b>{{ arg.timeText }}</b>
                        <i>{{ arg.event.title }}</i>
                    </div>
                </template>
            </FullCalendar>
        </div>
    </AppLayout>
</template>

<style>
.fc-list-event:hover td {
    background-color: #ffffff !important;
}
</style>
