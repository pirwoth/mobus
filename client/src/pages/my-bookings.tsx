import { useQuery } from "@tanstack/react-query";
import { useAuth } from "@/context/auth-context";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";

export default function MyBookings() {
  const { user } = useAuth();

  const { data: bookings, isLoading, error } = useQuery({
    queryKey: ["/api/bookings/user", user?.id],
    enabled: !!user?.id,
  });

  if (!user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <p className="text-gray-600">Please log in to view your bookings.</p>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <p className="text-red-600">Failed to load bookings.</p>
      </div>
    );
  }

  const bookingsArray: any[] = Array.isArray(bookings) ? bookings : [];
  if (bookingsArray.length === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <p className="text-gray-600">No bookings found.</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-3xl mx-auto px-2 sm:px-4 lg:px-8">
        <h2 className="text-2xl md:text-3xl font-bold text-gray-900 mb-6 text-center">My Bookings</h2>
        <div className="space-y-6">
          {bookingsArray.map((booking: any) => (
            <Card key={booking.id}>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span>Booking #{booking.id}</span>
                  <Badge variant={booking.paymentStatus === "paid" ? "default" : "destructive"}>
                    {booking.paymentStatus === "paid" ? "Paid" : "Pending"}
                  </Badge>
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                <div className="flex flex-wrap gap-4 text-sm">
                  <div>
                    <span className="text-gray-600">Route:</span> {booking.fromCity} → {booking.toCity}
                  </div>
                  <div>
                    <span className="text-gray-600">Date:</span> {new Date(booking.bookingDate).toLocaleDateString()}
                  </div>
                  <div>
                    <span className="text-gray-600">Seats:</span> {booking.seatNumbers?.join(", ")}
                  </div>
                  <div>
                    <span className="text-gray-600">Total:</span> ${booking.totalAmount}
                  </div>
                </div>
                <Separator />
                <div className="text-xs text-gray-400">Booked on {new Date(booking.createdAt).toLocaleString()}</div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
}
