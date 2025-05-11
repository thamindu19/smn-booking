<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoyaDay;
use App\Models\Booking;
use App\Http\Resources\PoyaDayResource;

class PoyaDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $poyaDays = PoyaDay::with('booking')->get();
        $pendingBookings = Booking::where('status', 'pending')
            ->whereIn('poya_day_id', $poyaDays->pluck('id'))
            ->get()
            ->groupBy('poya_day_id');
        $poyaDays->each(function ($poyaDay) use ($pendingBookings) {
            $poyaDay->pending_bookings = $pendingBookings->get($poyaDay->id, collect())->count();
        });

        return PoyaDayResource::collection($poyaDays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
        ]);
        $poyaDay = PoyaDay::create($validated);

        return new PoyaDayResource($poyaDay);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poyaDay = PoyaDay::with('booking')->findOrFail($id);

        return new PoyaDayResource($poyaDay);
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
