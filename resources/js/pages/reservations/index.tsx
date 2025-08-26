import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { router } from '@inertiajs/react';
import { Calendar, Users, Plus } from 'lucide-react';

interface Reservation {
  id: number;
  reservation_number: string;
  status: string;
  check_in_date: string;
  check_out_date: string;
  adults: number;
  children: number;
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

interface Props {
  reservations: {
    data: Reservation[];
    links: Record<string, unknown>;
    meta: Record<string, unknown>;
  };
  [key: string]: unknown;
}

export default function ReservationsIndex({ reservations }: Props) {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed': return 'bg-blue-100 text-blue-800';
      case 'checked_in': return 'bg-green-100 text-green-800';
      case 'checked_out': return 'bg-gray-100 text-gray-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-yellow-100 text-yellow-800';
    }
  };

  return (
    <AppShell>
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">📅 Reservations</h1>
            <p className="text-gray-600 mt-1">Manage hotel reservations and bookings</p>
          </div>
          <Button onClick={() => router.visit('/reservations/create')}>
            <Plus className="h-4 w-4 mr-2" />
            New Reservation
          </Button>
        </div>

        {/* Reservations List */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5" />
              All Reservations
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {reservations.data.map(reservation => (
                <div key={reservation.id} className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer"
                     onClick={() => router.visit(`/reservations/${reservation.id}`)}>
                  <div className="flex-1">
                    <div className="flex items-center gap-3 mb-2">
                      <h3 className="font-semibold text-lg">{reservation.guest.full_name}</h3>
                      <Badge className={getStatusColor(reservation.status)}>
                        {reservation.status.replace('_', ' ').toUpperCase()}
                      </Badge>
                    </div>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                      <div>
                        <span className="font-medium">Reservation:</span> #{reservation.reservation_number}
                      </div>
                      <div>
                        <span className="font-medium">Room:</span> {reservation.room?.room_number || 'Not assigned'} ({reservation.room_type.name})
                      </div>
                      <div>
                        <span className="font-medium">Check-in:</span> {reservation.check_in_date}
                      </div>
                      <div>
                        <span className="font-medium">Check-out:</span> {reservation.check_out_date}
                      </div>
                    </div>
                    <div className="flex items-center gap-4 mt-2 text-sm text-gray-500">
                      <div className="flex items-center gap-1">
                        <Users className="h-4 w-4" />
                        <span>{reservation.adults} adults</span>
                        {reservation.children > 0 && <span>, {reservation.children} children</span>}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
              {reservations.data.length === 0 && (
                <p className="text-gray-500 text-center py-8">No reservations found</p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </AppShell>
  );
}