<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['guest', 'room', 'roomType']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('check_out_date', '<=', $request->date_to);
        }

        $reservations = $query->orderBy('check_in_date', 'desc')->paginate(15);

        return Inertia::render('reservations/index', [
            'reservations' => $reservations,
            'filters' => $request->only(['status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guests = Guest::orderBy('last_name')->get();
        $roomTypes = RoomType::all();

        return Inertia::render('reservations/create', [
            'guests' => $guests,
            'room_types' => $roomTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        
        // Generate reservation number
        $validated['reservation_number'] = 'RES-' . strtoupper(uniqid());
        
        // Calculate total amount based on room type and dates
        $roomType = RoomType::findOrFail($validated['room_type_id']);
        $checkIn = new \Carbon\Carbon($validated['check_in_date']);
        $checkOut = new \Carbon\Carbon($validated['check_out_date']);
        $nights = $checkOut->diffInDays($checkIn);
        $validated['total_amount'] = floatval($roomType->base_price) * $nights;

        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['guest', 'room.roomType', 'roomType']);

        return Inertia::render('reservations/show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $reservation->load(['guest', 'room', 'roomType']);
        $guests = Guest::orderBy('last_name')->get();
        $roomTypes = RoomType::all();
        $rooms = Room::with('roomType')->get();

        return Inertia::render('reservations/edit', [
            'reservation' => $reservation,
            'guests' => $guests,
            'room_types' => $roomTypes,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();
        
        // Recalculate total amount if dates or room type changed
        if (isset($validated['room_type_id']) || isset($validated['check_in_date']) || isset($validated['check_out_date'])) {
            $roomTypeId = $validated['room_type_id'] ?? $reservation->room_type_id;
            $checkIn = new \Carbon\Carbon($validated['check_in_date'] ?? $reservation->check_in_date);
            $checkOut = new \Carbon\Carbon($validated['check_out_date'] ?? $reservation->check_out_date);
            $roomType = RoomType::findOrFail($roomTypeId);
            $nights = $checkOut->diffInDays($checkIn);
            $validated['total_amount'] = floatval($roomType->base_price) * $nights;
        }

        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}