import { useState, useEffect } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useToast } from "@/hooks/use-toast";
import { apiRequest } from "@/lib/queryClient";
import { LoaderPinwheel, User, Phone, Mail } from "lucide-react";

export default function SeatSelection() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  
  // Parse URL parameters
  const searchParams = new URLSearchParams(window.location.search);
  const routeId = searchParams.get("routeId");
  const date = searchParams.get("date");

  const [selectedSeats, setSelectedSeats] = useState<string[]>([]);
  const [passengerInfo, setPassengerInfo] = useState({
    name: "",
    phone: "",
    email: "",
  });

  // Fetch route details
  const { data: routes } = useQuery({
    queryKey: ["/api/routes/search", "", "", date],
    enabled: !!routeId && !!date,
  });

  // Find the specific route
  const route = Array.isArray(routes) ? routes.find((r: any) => r.id === routeId) : undefined;

  const bookingMutation = useMutation({
    mutationFn: async (bookingData: any) => {
      const response = await apiRequest("POST", "/api/bookings", bookingData);
      return response.json();
    },
    onSuccess: (booking) => {
      queryClient.invalidateQueries({ queryKey: ["/api/routes/search"] });
      toast({
        title: "Booking successful!",
        description: "Proceeding to payment...",
      });
      setLocation(`/payment?bookingId=${booking.id}`);
    },
    onError: (error: any) => {
      toast({
        title: "Booking failed",
        description: error.message || "Please try again",
        variant: "destructive",
      });
    },
  });

  if (!routeId || !date) {
    return (
      <div className="min-h-screen bg-white flex items-center justify-center">
        <p className="text-red-600" data-testid="error-missing-params">Missing route or date information</p>
      </div>
    );
  }

  if (!route) {
    return (
      <div className="min-h-screen bg-white flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    );
  }

  const generateSeatLayout = () => {
    const seats = [];
    const totalSeats = route.bus.totalSeats || 40;
    const seatsPerRow = 4;
    const rows = Math.ceil(totalSeats / seatsPerRow);

    for (let row = 1; row <= rows; row++) {
      for (let seatIndex = 0; seatIndex < seatsPerRow; seatIndex++) {
        if (seats.length >= totalSeats) break;
        
        const seatNumber = `${row}${String.fromCharCode(65 + seatIndex)}`;
        const isBooked = route.bookedSeats?.includes(seatNumber) || false;
        const isSelected = selectedSeats.includes(seatNumber);
        
        seats.push({
          number: seatNumber,
          isBooked,
          isSelected,
          isAisle: seatIndex === 1, // Aisle after 2nd seat
        });
      }
    }

    return seats;
  };

  const handleSeatClick = (seatNumber: string) => {
    if (route.bookedSeats?.includes(seatNumber)) return;
    
    setSelectedSeats(prev => 
      prev.includes(seatNumber)
        ? prev.filter(seat => seat !== seatNumber)
        : [...prev, seatNumber]
    );
  };

  const handleProceedToPayment = () => {
    if (selectedSeats.length === 0) {
      toast({
        title: "No seats selected",
        description: "Please select at least one seat",
        variant: "destructive",
      });
      return;
    }

    if (!passengerInfo.name || !passengerInfo.phone) {
      toast({
        title: "Missing passenger information",
        description: "Please fill in passenger name and phone number",
        variant: "destructive",
      });
      return;
    }

    const totalAmount = selectedSeats.length * parseFloat(route.price);

    bookingMutation.mutate({
      routeId: route.id,
      passengerName: passengerInfo.name,
      passengerPhone: passengerInfo.phone,
      passengerEmail: passengerInfo.email || undefined,
      seatNumbers: selectedSeats,
      bookingDate: date,
      totalAmount: totalAmount.toFixed(2),
      paymentStatus: "pending",
      bookingStatus: "confirmed",
      bookedBy: "passenger",
    });
  };

  const seats = generateSeatLayout();
  const totalAmount = selectedSeats.length * parseFloat(route.price);

  return (
    <div className="min-h-screen bg-white py-6 md:py-12">
      <div className="max-w-6xl mx-auto px-2 sm:px-4 lg:px-8">
        <div className="text-center mb-6 md:mb-8">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-900 mb-2" data-testid="text-seat-selection-title">
            Select Your Seats
          </h2>
          <p className="text-gray-600" data-testid="text-route-info">
            {route.bus.operator.companyName} - {route.bus.busType} | {route.departureTime} - {route.arrivalTime}
          </p>
        </div>

  <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
          {/* Seat Map */}
          <div className="lg:col-span-2 mb-6 lg:mb-0">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  Bus Layout
                  <div className="flex items-center space-x-4 text-sm">
                    <div className="flex items-center">
                      <div className="w-4 h-4 bg-gray-300 rounded mr-2"></div>
                      <span>Available</span>
                    </div>
                    <div className="flex items-center">
                      <div className="w-4 h-4 bg-red-400 rounded mr-2"></div>
                      <span>Occupied</span>
                    </div>
                    <div className="flex items-center">
                      <div className="w-4 h-4 bg-primary rounded mr-2"></div>
                      <span>Selected</span>
                    </div>
                  </div>
                </CardTitle>
              </CardHeader>
              <CardContent>
                {/* Driver Section */}
                <div className="mb-6">
                  <div className="bg-gray-200 rounded-lg p-3 text-center text-sm text-gray-600 mb-4 flex items-center justify-center">
                    <LoaderPinwheel className="mr-2 h-4 w-4" />
                    Driver
                  </div>
                </div>

                {/* Seat Grid */}
                <div className="grid grid-cols-4 gap-2 md:gap-3 max-w-xs md:max-w-md mx-auto">
                  {seats.map((seat, index) => (
                    <div key={seat.number} className="flex">
                      <Button
                        variant={seat.isSelected ? "default" : seat.isBooked ? "destructive" : "outline"}
                        className={`w-12 h-12 text-sm font-medium ${
                          seat.isBooked ? "cursor-not-allowed opacity-50" : "cursor-pointer"
                        }`}
                        disabled={seat.isBooked}
                        onClick={() => handleSeatClick(seat.number)}
                        data-testid={`seat-${seat.number}`}
                      >
                        {seat.number}
                      </Button>
                      {seat.isAisle && <div className="w-4"></div>}
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Passenger Information */}
            <Card className="mt-6">
              <CardHeader>
                <CardTitle>Passenger Information</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label htmlFor="passenger-name" className="flex items-center">
                    <User className="mr-2 h-4 w-4" />
                    Full Name *
                  </Label>
                  <Input
                    id="passenger-name"
                    value={passengerInfo.name}
                    onChange={(e) => setPassengerInfo(prev => ({ ...prev, name: e.target.value }))}
                    placeholder="Enter passenger full name"
                    required
                    data-testid="input-passenger-name"
                  />
                </div>
                <div>
                  <Label htmlFor="passenger-phone" className="flex items-center">
                    <Phone className="mr-2 h-4 w-4" />
                    Phone Number *
                  </Label>
                  <Input
                    id="passenger-phone"
                    type="tel"
                    value={passengerInfo.phone}
                    onChange={(e) => setPassengerInfo(prev => ({ ...prev, phone: e.target.value }))}
                    placeholder="Enter phone number"
                    required
                    data-testid="input-passenger-phone"
                  />
                </div>
                <div>
                  <Label htmlFor="passenger-email" className="flex items-center">
                    <Mail className="mr-2 h-4 w-4" />
                    Email Address (Optional)
                  </Label>
                  <Input
                    id="passenger-email"
                    type="email"
                    value={passengerInfo.email}
                    onChange={(e) => setPassengerInfo(prev => ({ ...prev, email: e.target.value }))}
                    placeholder="Enter email address"
                    data-testid="input-passenger-email"
                  />
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Booking Summary */}
          <div className="lg:col-span-1">
            <Card className="sticky top-4 md:top-24">
              <CardHeader>
                <CardTitle>Booking Summary</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-3">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Route</span>
                    <span className="font-medium" data-testid="text-summary-route">
                      {route.fromCity} → {route.toCity}
                    </span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Date</span>
                    <span className="font-medium" data-testid="text-summary-date">
                      {new Date(date).toLocaleDateString()}
                    </span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Selected Seats</span>
                    <span className="font-medium text-primary" data-testid="text-summary-seats">
                      {selectedSeats.join(", ") || "None"}
                    </span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Passengers</span>
                    <span className="font-medium" data-testid="text-summary-passengers">
                      {selectedSeats.length} {selectedSeats.length === 1 ? "Adult" : "Adults"}
                    </span>
                  </div>
                  <Separator />
                  <div className="flex justify-between text-lg font-semibold">
                    <span>Total</span>
                    <span className="text-primary" data-testid="text-summary-total">
                      ${totalAmount.toFixed(2)}
                    </span>
                  </div>
                </div>

                <div className="space-y-3 pt-4">
                  <Button
                    className="w-full"
                    onClick={handleProceedToPayment}
                    disabled={bookingMutation.isPending || selectedSeats.length === 0}
                    data-testid="button-proceed-payment"
                  >
                    {bookingMutation.isPending ? "Processing..." : "Proceed to Payment"}
                  </Button>
                  <Button
                    variant="outline"
                    className="w-full"
                    onClick={() => setLocation("/")}
                    data-testid="button-back-buses"
                  >
                    Back to Buses
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
