<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
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
    poya_day: {
        id: number;
        month: string;
        date: string;
    };
    date: string;
    month: string;
};

const bookings = ref<Booking[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/api/bookings', { withCredentials: true });
        bookings.value = response.data.data;
        bookings.value.forEach((booking) => {
            booking.poya_day.date = new Date(booking.poya_day.date).toISOString().split('T')[0];
            booking.poya_day.month = booking.poya_day.month.split(' ').slice(0, -1).join(' ');
            booking.created_at = new Date(booking.created_at).toISOString().split('T')[0] + ' ' + booking.created_at.split('T')[1].split('.')[0];
        });
    } catch (error) {
        console.error('Error fetching bookings:', error);
    }
});

const pendingRequests = ref(true);
const filteredBookings = computed(() => {
    return pendingRequests.value ? bookings.value.filter((b) => b.status === 'pending') : bookings.value;
});

function updateStatus(bookingId: number, action: string) {
    axios
        .patch(`/api/bookings/${bookingId}/${action}`)
        .then((response) => {
            const updatedBooking = response.data.data;
            console.log(updatedBooking);
            updatedBooking.poya_day.date = new Date(updatedBooking.poya_day.date).toISOString().split('T')[0];
            updatedBooking.poya_day.month = updatedBooking.poya_day.month.split(' ').slice(0, -1).join(' ');
            updatedBooking.created_at =
                new Date(updatedBooking.created_at).toISOString().split('T')[0] + ' ' + updatedBooking.created_at.split('T')[1].split('.')[0];
            const index = bookings.value.findIndex((b) => b.id === updatedBooking.id);
            if (index !== -1) {
                bookings.value[index] = updatedBooking;
            }
            toast.success(`Booking request ${bookingId} has been ${action === 'approve' ? 'approved' : 'rejected'}`);
        })
        .catch((error) => {
            console.error('Error updating status:', error);
        });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col rounded-xl p-7 text-sm">
            <div class="flex justify-center">
                <div class="items-right flex space-x-2 pb-4">
                    <Switch id="pending-requests" v-model="pendingRequests" />
                    <Label for="pending-requests">Pending Requests</Label>
                </div>
            </div>
            <div v-if="filteredBookings.length === 0" class="py-4 text-center text-gray-500">No requests</div>
            <Accordion v-else type="multiple" class="w-full" collapsible>
                <AccordionItem v-for="booking in filteredBookings" :key="booking.id" :value="`booking-${booking.id}`">
                    <AccordionTrigger
                        class="flex w-full cursor-pointer flex-col items-center justify-between gap-4 text-left hover:no-underline sm:flex-row sm:items-start"
                    >
                        <span class="truncate font-semibold sm:w-1/12">{{ booking.id }}</span>

                        <span class="truncate text-sm text-gray-500 sm:w-5/12"
                            >{{ booking.poya_day.date }} : {{ booking.poya_day.month }} Full Moon Poya Day (all-day)</span
                        >
                        <span class="truncate font-semibold sm:w-2/12">{{ booking.name }}</span>

                        <span class="truncate text-sm text-gray-500 sm:w-2/12">{{ booking.email }}</span>
                        <span class="truncate text-sm text-gray-500 sm:w-2/12">{{ booking.created_at }}</span>
                        <Badge
                            class="rounded-full px-2 py-0.5 sm:w-1/12"
                            :class="{
                                'bg-green-100 text-green-700': booking.status === 'approved',
                                'bg-yellow-100 text-yellow-700': booking.status === 'pending',
                                'bg-red-100 text-red-700': booking.status === 'rejected',
                            }"
                            >{{ booking.status[0].toUpperCase() + booking.status.slice(1) }}</Badge
                        >
                    </AccordionTrigger>
                    <AccordionContent class="grid grid-cols-1 rounded-md bg-gray-50 p-8 pt-4 sm:grid-cols-[3fr_3fr_1fr] dark:bg-gray-800">
                        <div class="p-2">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Event Details</h3>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Event ID:</strong> {{ booking.poya_day_id }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Event:</strong> {{ booking.poya_day.month }} Full Moon Poya Day
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Date:</strong> {{ booking.poya_day.date }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Time:</strong> All Day</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Booking Details</h3>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Booking ID:</strong> {{ booking.id }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Name:</strong> {{ booking.name }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Email:</strong> {{ booking.email }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Phone:</strong> {{ booking.phone }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Created At:</strong> {{ booking.created_at }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Notes:</strong> {{ booking.notes || '—' }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Status:</strong> {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                                </p>
                            </div>
                        </div>
                        <div v-if="booking.status === 'pending'" class="flex flex-row justify-center gap-2 p-2 sm:flex-col sm:justify-start">
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="default" class="cursor-pointer sm:w-full"> Approve </Button>
                                </DialogTrigger>
                                <DialogContent
                                    class="sm:max-w-md"
                                    @interact-outside="
                                        (event) => {
                                            const target = event.target as HTMLElement;
                                            if (target?.closest('[data-sonner-toaster]')) return event.preventDefault();
                                        }
                                    "
                                >
                                    <DialogHeader>
                                        <DialogTitle>Confirmation</DialogTitle>
                                        <DialogDescription>
                                            Do you want to approve booking request
                                            {{ booking.id }} ?
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4">
                                        <Button
                                            size="sm"
                                            variant="default"
                                            class="cursor-pointer px-3"
                                            @click="
                                                () => {
                                                    updateStatus(booking.id, 'approve');
                                                }
                                            "
                                        >
                                            Approve
                                        </Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="secondary" class="cursor-pointer sm:w-full"> Reject </Button>
                                </DialogTrigger>
                                <DialogContent
                                    class="sm:max-w-md"
                                    @interact-outside="
                                        (event) => {
                                            const target = event.target as HTMLElement;
                                            if (target?.closest('[data-sonner-toaster]')) return event.preventDefault();
                                        }
                                    "
                                >
                                    <DialogHeader>
                                        <DialogTitle>Confirmation</DialogTitle>
                                        <DialogDescription>
                                            Do you want to reject booking request
                                            {{ booking.id }} ?
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4">
                                        <Button
                                            size="sm"
                                            variant="secondary"
                                            class="cursor-pointer px-3"
                                            @click="
                                                () => {
                                                    updateStatus(booking.id, 'reject');
                                                }
                                            "
                                        >
                                            Reject
                                        </Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </div>
    </AppLayout>
</template>
