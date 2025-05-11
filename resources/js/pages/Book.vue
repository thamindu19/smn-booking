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
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Book',
        href: '/',
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
    pending_bookings: number;
    status: string;
};

const events = ref<Event[]>([]);
const selectedEvent = ref(null);
const fetchEvents = async () => {
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
            borderColor: event.booking_id ? 'gray' : event.pending_bookings > 0 ? 'yellow' : 'green',
            textColor: event.booking_id ? 'white' : event.pending_bookings > 0 ? 'black' : 'white',
        }));
    } catch (error) {
        console.error('Error fetching events:', error);
    }
};

onMounted(fetchEvents);

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
    eventClick: handleEventClick,
    height: 'auto',
    eventDidMount: function (info) {
        info.el.style.overflow = 'hidden';
        if (info.event.extendedProps.status === 'Available') {
            info.el.style.cursor = 'pointer';
            if (info.view.type === 'multiMonthYear') {
                tippy(info.el, {
                    content: 'Click to book',
                    placement: 'top',
                    theme: 'material',
                });
            }
        }
    },
}));
function handleEventClick(clickInfo) {
    if (clickInfo.event.extendedProps.status === 'Available') {
        selectedEvent.value = clickInfo.event;
        showBookingForm.value = true;
    }
}

const showBookingForm = ref(false);
const bookingForm = ref({
    name: '',
    email: '',
    phone: '',
    notes: '',
});
function resetForm() {
    bookingForm.value = {
        name: '',
        email: '',
        phone: '',
        notes: '',
    };
}
function submitBookingForm() {
    if (!selectedEvent.value) return;

    const payload = {
        poya_day_id: selectedEvent.value.id,
        name: bookingForm.value.name,
        email: bookingForm.value.email,
        phone: bookingForm.value.phone,
        notes: bookingForm.value.notes,
    };
    axios
        .post('/api/bookings', payload)
        .then(() => {
            closeModal();
            toast.success('Booking request successfully submitted');
            fetchEvents();
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                console.error('Validation Errors:', error.response.data.errors);
            } else {
                console.error('Failed to submit booking:', error);
            }
        });
}
function closeModal() {
    showBookingForm.value = false;
    resetForm();
}

const formattedSelectedDate = computed(() => {
    if (!selectedEvent.value) return '';
    const date = new Date(selectedEvent.value.start);
    return `${date.getFullYear()}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getDate().toString().padStart(2, '0')}`;
});
</script>

<template>
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-7 text-sm">
            <FullCalendar class="demo-app-calendar" :options="calendarOptions">
                <template v-slot:eventContent="arg">
                    <div v-if="arg.view.type === 'listYear'" class="flex w-full items-center justify-between">
                        <span class="w-2/5 font-medium">{{ arg.event.title }}</span>
                        <span v-if="arg.event.extendedProps.status === 'Available'" class="w-2/5 text-left text-sm text-gray-600">Click to book</span>
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

            <div v-if="showBookingForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="w-full max-w-lg space-y-6 rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                    <h2 class="text-xl font-semibold">Book Dhamma Sermon</h2>

                    <div class="text-center">
                        <div class="bg-primary/10 text-primary inline-block rounded-full px-3 py-1 text-sm font-medium">
                            {{ formattedSelectedDate }} - {{ selectedEvent.title }}
                        </div>
                    </div>
                    <form @submit.prevent="submitBookingForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input
                                v-model="bookingForm.name"
                                type="text"
                                class="border-input bg-background focus:ring-primary mt-1 w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:ring-2 focus:outline-none"
                                required
                                maxlength="150"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                            <input
                                v-model="bookingForm.email"
                                type="email"
                                class="border-input bg-background focus:ring-primary mt-1 w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:ring-2 focus:outline-none"
                                required
                                maxlength="254"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                            <input
                                v-model="bookingForm.phone"
                                type="numeric"
                                inputmode="numeric"
                                class="border-input bg-background focus:ring-primary mt-1 w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:ring-2 focus:outline-none"
                                required
                                maxlength="20"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                            <textarea
                                v-model="bookingForm.notes"
                                rows="5"
                                class="border-input bg-background focus:ring-primary mt-1 w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:ring-2 focus:outline-none"
                                required
                            ></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                type="button"
                                @click="closeModal"
                                class="bg-muted hover:bg-muted/80 border-input cursor-pointer rounded-md border px-4 py-2 text-sm"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="bg-primary hover:bg-primary/90 cursor-pointer rounded-md px-4 py-2 text-sm text-white">
                                Submit Booking Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.fc-list-event:hover td {
    background-color: #ffffff !important;
}
</style>
