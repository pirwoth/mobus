import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { useToast } from "@/hooks/use-toast";
import { apiRequest } from "@/lib/queryClient";
import { CreditCard, Smartphone, Wallet, Lock } from "lucide-react";

export default function Payment() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  
  // Parse URL parameters
  const searchParams = new URLSearchParams(window.location.search);
  const bookingId = searchParams.get("bookingId");

  const [paymentMethod, setPaymentMethod] = useState("card");
  const [cardDetails, setCardDetails] = useState({
    number: "",
    expiry: "",
    cvv: "",
    name: "",
  });

  // Fetch booking details
  const { data, isLoading } = useQuery({
    queryKey: ["/api/bookings", bookingId],
    enabled: !!bookingId,
  });

  // Ensure booking is always an object with expected properties
  const booking: any = data && typeof data === 'object' ? data : {};

  const paymentMutation = useMutation({
    mutationFn: async () => {
      // Mock payment processing
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      // Update booking payment status
      const response = await apiRequest("PATCH", `/api/bookings/${bookingId}`, {
        paymentStatus: Math.random() > 0.1 ? "paid" : "failed", // 90% success rate
        paymentMethod,
      });
      return response.json();
    },
    onSuccess: (updatedBooking) => {
      queryClient.invalidateQueries({ queryKey: ["/api/bookings", bookingId] });
      
      if (updatedBooking.paymentStatus === "paid") {
        toast({
          title: "Payment successful!",
          description: "Your booking has been confirmed.",
        });
        setLocation(`/booking-confirmation?bookingId=${bookingId}`);
      } else {
        toast({
          title: "Payment failed",
          description: "Please try again with a different payment method.",
          variant: "destructive",
        });
      }
    },
    onError: () => {
      toast({
        title: "Payment error",
        description: "Something went wrong. Please try again.",
        variant: "destructive",
      });
    },
  });

  if (!bookingId) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-red-600" data-testid="error-missing-booking">Missing booking information</p>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (!booking) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-red-600" data-testid="error-booking-not-found">Booking not found</p>
      </div>
    );
  }

  const handlePayment = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (paymentMethod === "card") {
      if (!cardDetails.number || !cardDetails.expiry || !cardDetails.cvv || !cardDetails.name) {
        toast({
          title: "Missing card details",
          description: "Please fill in all card information",
          variant: "destructive",
        });
        return;
      }
    }
    
    paymentMutation.mutate();
  };

  const serviceFee = 2.50;
  const taxes = 3.50;
  const totalAmount = parseFloat(booking.totalAmount) + serviceFee + taxes;

  return (
    <div className="min-h-screen bg-gray-50 py-6 md:py-12">
      <div className="max-w-2xl mx-auto px-2 sm:px-4 lg:px-8">
        <div className="text-center mb-6 md:mb-8">
          <h2 className="text-2xl md:text-3xl font-bold text-gray-900 mb-2" data-testid="text-payment-title">
            Payment
          </h2>
          <p className="text-gray-600">Complete your booking with secure payment</p>
        </div>

        <Card>
          <CardContent className="p-8">
            <form onSubmit={handlePayment} className="space-y-8">
              {/* Payment Methods */}
              <div>
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                <RadioGroup value={paymentMethod} onValueChange={setPaymentMethod}>
                  <div className="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <RadioGroupItem value="card" id="card" />
                    <Label htmlFor="card" className="flex items-center cursor-pointer flex-1">
                      <CreditCard className="text-gray-400 mr-3 h-5 w-5" />
                      <span className="font-medium">Credit/Debit Card</span>
                    </Label>
                  </div>
                  <div className="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <RadioGroupItem value="mobile_money" id="mobile_money" />
                    <Label htmlFor="mobile_money" className="flex items-center cursor-pointer flex-1">
                      <Smartphone className="text-gray-400 mr-3 h-5 w-5" />
                      <span className="font-medium">Mobile Money</span>
                    </Label>
                  </div>
                  <div className="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <RadioGroupItem value="paypal" id="paypal" />
                    <Label htmlFor="paypal" className="flex items-center cursor-pointer flex-1">
                      <Wallet className="text-gray-400 mr-3 h-5 w-5" />
                      <span className="font-medium">PayPal</span>
                    </Label>
                  </div>
                </RadioGroup>
              </div>

              {/* Card Details */}
              {paymentMethod === "card" && (
                <div className="space-y-4">
                  <div>
                    <Label htmlFor="card-number">Card Number</Label>
                    <Input
                      id="card-number"
                      value={cardDetails.number}
                      onChange={(e) => setCardDetails(prev => ({ ...prev, number: e.target.value }))}
                      placeholder="1234 5678 9012 3456"
                      maxLength={19}
                      data-testid="input-card-number"
                    />
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="card-expiry">Expiry Date</Label>
                      <Input
                        id="card-expiry"
                        value={cardDetails.expiry}
                        onChange={(e) => setCardDetails(prev => ({ ...prev, expiry: e.target.value }))}
                        placeholder="MM/YY"
                        maxLength={5}
                        data-testid="input-card-expiry"
                      />
                    </div>
                    <div>
                      <Label htmlFor="card-cvv">CVV</Label>
                      <Input
                        id="card-cvv"
                        value={cardDetails.cvv}
                        onChange={(e) => setCardDetails(prev => ({ ...prev, cvv: e.target.value }))}
                        placeholder="123"
                        maxLength={4}
                        data-testid="input-card-cvv"
                      />
                    </div>
                  </div>
                  <div>
                    <Label htmlFor="card-name">Cardholder Name</Label>
                    <Input
                      id="card-name"
                      value={cardDetails.name}
                      onChange={(e) => setCardDetails(prev => ({ ...prev, name: e.target.value }))}
                      placeholder="John Doe"
                      data-testid="input-card-name"
                    />
                  </div>
                </div>
              )}

              {/* Order Summary */}
              <div className="bg-gray-50 rounded-lg p-6">
                <h3 className="font-semibold text-gray-900 mb-4">Order Summary</h3>
                <div className="space-y-2">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Booking ID</span>
                    <span className="font-mono text-sm" data-testid="text-booking-id">
                      {booking.id}
                    </span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Passenger</span>
                    <span data-testid="text-passenger-name">{booking.passengerName}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Seats</span>
                    <span data-testid="text-seat-numbers">{booking.seatNumbers?.join(", ")}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Ticket Price</span>
                    <span data-testid="text-ticket-price">${booking.totalAmount}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Service Fee</span>
                    <span data-testid="text-service-fee">${serviceFee.toFixed(2)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Taxes</span>
                    <span data-testid="text-taxes">${taxes.toFixed(2)}</span>
                  </div>
                  <Separator />
                  <div className="flex justify-between text-lg font-semibold">
                    <span>Total</span>
                    <span className="text-primary" data-testid="text-total-amount">
                      ${totalAmount.toFixed(2)}
                    </span>
                  </div>
                </div>
              </div>

              <Button
                type="submit"
                className="w-full"
                disabled={paymentMutation.isPending}
                data-testid="button-pay"
              >
                <Lock className="mr-2 h-4 w-4" />
                {paymentMutation.isPending ? "Processing..." : `Pay $${totalAmount.toFixed(2)}`}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
