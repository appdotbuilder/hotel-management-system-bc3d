<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HotelDashboardController extends Controller
{
    /**
     * Display the hotel management dashboard.
     */
    public function index()
    {
        // Get key statistics
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'total_guests' => Guest::count(),
            'active_reservations' => Reservation::whereIn('status', ['confirmed', 'checked_in'])->count(),
            'pending_tasks' => HousekeepingTask::where('status', 'pending')->count(),
            'today_checkins' => Reservation::where('check_in_date', today())->where('status', 'confirmed')->count(),
            'today_checkouts' => Reservation::where('check_out_date', today())->where('status', 'checked_in')->count(),
        ];

        // Get recent reservations
        $recentReservations = Reservation::with(['guest', 'room', 'roomType'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_in_date', 'desc')
            ->limit(5)
            ->get();

        // Get pending housekeeping tasks
        $pendingTasks = HousekeepingTask::with(['room.roomType', 'assignedTo'])
            ->where('status', 'pending')
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        // Get room status overview
        $roomsByType = RoomType::withCount([
            'rooms',
            'rooms as available_count' => function ($query) {
                $query->where('status', 'available');
            },
            'rooms as occupied_count' => function ($query) {
                $query->where('status', 'occupied');
            }
        ])->get();

        return Inertia::render('hotel-dashboard', [
            'stats' => $stats,
            'recent_reservations' => $recentReservations,
            'pending_tasks' => $pendingTasks,
            'rooms_by_type' => $roomsByType,
        ]);
    }

    /**
     * Get availability for room search.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:10',
            'room_type_id' => 'nullable|integer|exists:room_types,id',
        ]);

        // Find available rooms
        $availableRooms = Room::with('roomType')
            ->where('status', 'available')
            ->when($validated['room_type_id'], function ($query, $roomTypeId) {
                return $query->where('room_type_id', $roomTypeId);
            })
            ->whereDoesntHave('reservations', function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->whereBetween('check_in_date', [$validated['check_in'], $validated['check_out']])
                      ->orWhereBetween('check_out_date', [$validated['check_in'], $validated['check_out']])
                      ->orWhere(function ($q2) use ($validated) {
                          $q2->where('check_in_date', '<=', $validated['check_in'])
                             ->where('check_out_date', '>=', $validated['check_out']);
                      });
                })->whereIn('status', ['confirmed', 'checked_in']);
            })
            ->whereHas('roomType', function ($query) use ($validated) {
                $query->where('max_occupancy', '>=', $validated['guests']);
            })
            ->get();

        // Get all room types for filter
        $roomTypes = RoomType::all();

        return Inertia::render('hotel-dashboard', [
            'search_results' => $availableRooms,
            'room_types' => $roomTypes,
            'search_params' => $validated,
        ]);
    }
}