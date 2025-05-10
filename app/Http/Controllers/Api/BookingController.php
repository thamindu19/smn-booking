<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PoyaDay;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BookingResource::collection(Booking::with('poyaDay')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'poya_day_id' => 'required|exists:poya_days,id',
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:254',
            'phone' => 'required|string|max:20',
            'notes' => 'required|string'
        ]);
        $alreadyBooked = Booking::where('poya_day_id', $validated['poya_day_id'])
            ->where('status', 'approved')
            ->exists();
        if ($alreadyBooked) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'poya_day_id' => ['The selected poya day is already booked.']
                ]
            ], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'status' => 'pending'
        ]);

        return new BookingResource($booking);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with('poyaDay')->findOrFail($id);

        return new BookingResource($booking);
    }

    /**
     * Approve the specified resource.
     */
    public function approve(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'approved';
        $booking->save();

        $poyaDay = PoyaDay::find($booking->poya_day_id);
        $poyaDay->booking_id = $booking->id;
        $poyaDay->save();

        return new BookingResource($booking);
    }

    /**
     * Reject the specified resource.
     */
    public function reject(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();

        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
