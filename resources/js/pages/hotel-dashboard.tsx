import React, { useState } from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { router, useForm } from '@inertiajs/react';
import { 
  Home, 
  Users, 
  Calendar, 
  Bed, 
  ClipboardList, 
  TrendingUp,
  LogIn,
  LogOut,
  Wrench,
  AlertCircle
} from 'lucide-react';

interface Stats {
  total_rooms: number;
  available_rooms: number;
  occupied_rooms: number;
  maintenance_rooms: number;
  total_guests: number;
  active_reservations: number;
  pending_tasks: number;
  today_checkins: number;
  today_checkouts: number;
}

interface Reservation {
  id: number;
  reservation_number: string;
  status: string;
  check_in_date: string;
  check_out_date: string;
  guest: {
    full_name: string;
  };
  room: {
    room_number: string;
  } | null;
  room_type: {
    name: string;
  };
}

interface HousekeepingTask {
  id: number;
  task_type: string;
  priority: string;
  description: string;
  room: {
    room_number: string;
    room_type: {
      name: string;
    };
  };
  assigned_to: {
    name: string;
  } | null;
}

interface RoomType {
  id: number;
  name: string;
  rooms_count: number;
  available_count: number;
  occupied_count: number;
}

interface SearchResult {
  id: number;
  room_number: string;
  floor: string;
  room_type: {
    name: string;
    base_price: number;
  };
}

interface Props {
  stats: Stats;
  recent_reservations: Reservation[];
  pending_tasks: HousekeepingTask[];
  rooms_by_type: RoomType[];
  search_results?: SearchResult[];
  room_types?: RoomType[];
  search_params?: Record<string, unknown>;
  [key: string]: unknown;
}

interface SearchFormData {
  check_in: string;
  check_out: string;
  guests: number;
  room_type_id: string;
  [key: string]: string | number;
}

export default function HotelDashboard({ 
  stats, 
  recent_reservations, 
  pending_tasks, 
  rooms_by_type,
  search_results,
  room_types = []
}: Props) {
  const [showSearch, setShowSearch] = useState(false);
  
  const { data, setData, post, processing } = useForm<SearchFormData>({
    check_in: '',
    check_out: '',
    guests: 1,
    room_type_id: '',
  });

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('hotel-dashboard.store'), {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed': return 'bg-blue-100 text-blue-800';
      case 'checked_in': return 'bg-green-100 text-green-800';
      case 'checked_out': return 'bg-gray-100 text-gray-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-yellow-100 text-yellow-800';
    }
  };

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'high': return 'bg-red-100 text-red-800';
      case 'medium': return 'bg-yellow-100 text-yellow-800';
      case 'low': return 'bg-green-100 text-green-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <AppShell>
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">🏨 Hotel Management Dashboard</h1>
            <p className="text-gray-600 mt-1">Comprehensive hotel operations control center</p>
          </div>
          <div className="flex gap-2">
            <Button 
              onClick={() => setShowSearch(!showSearch)}
              variant="outline"
            >
              🔍 Room Availability
            </Button>
            <Button onClick={() => router.visit('/reservations/create')}>
              ➕ New Reservation
            </Button>
          </div>
        </div>

        {/* Room Availability Search */}
        {showSearch && (
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                🔍 Check Room Availability
              </CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSearch} className="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                  <Label htmlFor="check_in">Check-in Date</Label>
                  <Input
                    id="check_in"
                    type="date"
                    value={data.check_in}
                    onChange={e => setData('check_in', e.target.value)}
                    required
                  />
                </div>
                <div>
                  <Label htmlFor="check_out">Check-out Date</Label>
                  <Input
                    id="check_out"
                    type="date"
                    value={data.check_out}
                    onChange={e => setData('check_out', e.target.value)}
                    required
                  />
                </div>
                <div>
                  <Label htmlFor="guests">Guests</Label>
                  <Input
                    id="guests"
                    type="number"
                    min="1"
                    max="10"
                    value={data.guests}
                    onChange={e => setData('guests', parseInt(e.target.value))}
                  />
                </div>
                <div>
                  <Label htmlFor="room_type">Room Type (Optional)</Label>
                  <Select value={data.room_type_id} onValueChange={value => setData('room_type_id', value)}>
                    <SelectTrigger>
                      <SelectValue placeholder="Any room type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="">Any room type</SelectItem>
                      {room_types.map(type => (
                        <SelectItem key={type.id} value={type.id.toString()}>
                          {type.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="flex items-end">
                  <Button type="submit" disabled={processing} className="w-full">
                    {processing ? 'Searching...' : 'Search Rooms'}
                  </Button>
                </div>
              </form>

              {search_results && Array.isArray(search_results) && (
                <div className="mt-6">
                  <h3 className="text-lg font-semibold mb-4">
                    Available Rooms ({search_results.length} found)
                  </h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {search_results.map((room: SearchResult) => (
                      <Card key={room.id} className="border-green-200">
                        <CardContent className="p-4">
                          <div className="flex justify-between items-start mb-2">
                            <div>
                              <h4 className="font-semibold">Room {room.room_number}</h4>
                              <p className="text-sm text-gray-600">{room.room_type.name}</p>
                            </div>
                            <Badge variant="outline" className="bg-green-50 text-green-700">
                              Available
                            </Badge>
                          </div>
                          <p className="text-sm text-gray-600">Floor: {room.floor}</p>
                          <p className="text-lg font-semibold text-green-600 mt-2">
                            ${room.room_type.base_price}/night
                          </p>
                        </CardContent>
                      </Card>
                    ))}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        )}

        {/* Key Statistics */}
        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Total Rooms</p>
                  <p className="text-2xl font-bold">{stats.total_rooms}</p>
                </div>
                <Home className="h-8 w-8 text-blue-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Available</p>
                  <p className="text-2xl font-bold text-green-600">{stats.available_rooms}</p>
                </div>
                <Bed className="h-8 w-8 text-green-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Occupied</p>
                  <p className="text-2xl font-bold text-red-600">{stats.occupied_rooms}</p>
                </div>
                <Users className="h-8 w-8 text-red-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Today Check-ins</p>
                  <p className="text-2xl font-bold text-blue-600">{stats.today_checkins}</p>
                </div>
                <LogIn className="h-8 w-8 text-blue-600" />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Today Check-outs</p>
                  <p className="text-2xl font-bold text-purple-600">{stats.today_checkouts}</p>
                </div>
                <LogOut className="h-8 w-8 text-purple-600" />
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Recent Reservations */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Calendar className="h-5 w-5" />
                Recent Reservations
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recent_reservations.map(reservation => (
                  <div key={reservation.id} className="flex items-center justify-between p-3 border rounded-lg">
                    <div>
                      <p className="font-semibold">{reservation.guest.full_name}</p>
                      <p className="text-sm text-gray-600">
                        {reservation.room?.room_number || 'No room assigned'} - {reservation.room_type.name}
                      </p>
                      <p className="text-sm text-gray-500">
                        {reservation.check_in_date} to {reservation.check_out_date}
                      </p>
                    </div>
                    <div className="text-right">
                      <Badge className={getStatusColor(reservation.status)}>
                        {reservation.status.replace('_', ' ').toUpperCase()}
                      </Badge>
                      <p className="text-sm text-gray-500 mt-1">
                        #{reservation.reservation_number}
                      </p>
                    </div>
                  </div>
                ))}
                {recent_reservations.length === 0 && (
                  <p className="text-gray-500 text-center py-4">No recent reservations</p>
                )}
              </div>
              <div className="mt-4">
                <Button 
                  variant="outline" 
                  className="w-full"
                  onClick={() => router.visit('/reservations')}
                >
                  View All Reservations
                </Button>
              </div>
            </CardContent>
          </Card>

          {/* Pending Housekeeping Tasks */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <ClipboardList className="h-5 w-5" />
                Pending Housekeeping Tasks
                {stats.pending_tasks > 0 && (
                  <Badge variant="destructive">{stats.pending_tasks}</Badge>
                )}
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {pending_tasks.map(task => (
                  <div key={task.id} className="flex items-center justify-between p-3 border rounded-lg">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <Badge className={getPriorityColor(task.priority)}>
                          {task.priority.toUpperCase()}
                        </Badge>
                        <span className="text-sm font-medium">
                          Room {task.room.room_number}
                        </span>
                      </div>
                      <p className="font-semibold capitalize">{task.task_type}</p>
                      <p className="text-sm text-gray-600">{task.description}</p>
                      {task.assigned_to && (
                        <p className="text-sm text-gray-500 mt-1">
                          Assigned to: {task.assigned_to.name}
                        </p>
                      )}
                    </div>
                    {task.priority === 'high' && (
                      <AlertCircle className="h-5 w-5 text-red-500" />
                    )}
                  </div>
                ))}
                {pending_tasks.length === 0 && (
                  <p className="text-gray-500 text-center py-4">No pending tasks</p>
                )}
              </div>
              <div className="mt-4">
                <Button 
                  variant="outline" 
                  className="w-full"
                  onClick={() => router.visit('/housekeeping')}
                >
                  View All Tasks
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Room Status by Type */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <TrendingUp className="h-5 w-5" />
              Room Status Overview
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {rooms_by_type.map(roomType => (
                <div key={roomType.id} className="border rounded-lg p-4">
                  <h3 className="font-semibold text-lg mb-3">{roomType.name}</h3>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-gray-600">Total Rooms:</span>
                      <span className="font-semibold">{roomType.rooms_count}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-600">Available:</span>
                      <span className="font-semibold text-green-600">{roomType.available_count}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-red-600">Occupied:</span>
                      <span className="font-semibold text-red-600">{roomType.occupied_count}</span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-2 mt-3">
                      <div 
                        className="bg-red-600 h-2 rounded-full" 
                        style={{ 
                          width: `${roomType.rooms_count > 0 ? (roomType.occupied_count / roomType.rooms_count) * 100 : 0}%` 
                        }}
                      ></div>
                    </div>
                    <p className="text-sm text-gray-500 text-center">
                      {roomType.rooms_count > 0 ? Math.round((roomType.occupied_count / roomType.rooms_count) * 100) : 0}% Occupied
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Quick Actions */}
        <Card>
          <CardHeader>
            <CardTitle>Quick Actions</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <Button 
                variant="outline" 
                className="h-16 flex-col"
                onClick={() => router.visit('/reservations/create')}
              >
                <Calendar className="h-6 w-6 mb-1" />
                New Reservation
              </Button>
              <Button 
                variant="outline" 
                className="h-16 flex-col"
                onClick={() => router.visit('/guests')}
              >
                <Users className="h-6 w-6 mb-1" />
                Manage Guests
              </Button>
              <Button 
                variant="outline" 
                className="h-16 flex-col"
                onClick={() => router.visit('/rooms')}
              >
                <Bed className="h-6 w-6 mb-1" />
                Room Management
              </Button>
              <Button 
                variant="outline" 
                className="h-16 flex-col"
                onClick={() => router.visit('/housekeeping')}
              >
                <Wrench className="h-6 w-6 mb-1" />
                Housekeeping
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppShell>
  );
}