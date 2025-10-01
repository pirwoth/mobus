import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-react";
import BottomNav from "@/components/BottomNav";

const ROWS = 10;

const Seats = () => {
  const navigate = useNavigate();
  const [selectedSeats, setSelectedSeats] = useState<number[]>([]);
  const pricePerSeat = 45000;

  const toggleSeat = (seatNumber: number) => {
    setSelectedSeats((prev) =>
      prev.includes(seatNumber)
        ? prev.filter((s) => s !== seatNumber)
        : [...prev, seatNumber]
    );
  };

  const totalPrice = selectedSeats.length * pricePerSeat;

  return (
    <div className="min-h-screen bg-background flex flex-col pb-24">
      <header className="px-6 py-4 border-b border-border flex items-center gap-4">
        <Button
          variant="ghost"
          size="icon"
          onClick={() => navigate("/buses")}
          className="rounded-full"
        >
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <h1 className="text-xl font-semibold">Select Seats</h1>
      </header>

      <main className="flex-1 px-6 py-6 pb-40">
        <div className="max-w-sm mx-auto">
          {/* Legend */}
          <div className="mb-6 flex items-center justify-center gap-6 text-sm">
            <div className="flex items-center gap-2">
              <div className="w-7 h-7 rounded-lg bg-muted border border-border" />
              <span className="text-muted-foreground">Available</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-7 h-7 rounded-lg bg-foreground" />
              <span className="text-muted-foreground">Selected</span>
            </div>
          </div>

          {/* Driver indicator */}
          <div className="mb-4 flex justify-end">
            <div className="bg-muted rounded-ios px-4 py-2">
              <span className="text-sm font-medium text-muted-foreground">Driver</span>
            </div>
          </div>

          {/* Bus Layout: 2 seats - corridor - 2 seats */}
          <div className="bg-card rounded-ios-lg shadow-ios p-4 space-y-2">
            {Array.from({ length: ROWS }).map((_, rowIndex) => {
              // Calculate seat numbers: left side (2 seats) and right side (2 seats)
              const leftSeat1 = rowIndex * 4 + 1;
              const leftSeat2 = rowIndex * 4 + 2;
              const rightSeat1 = rowIndex * 4 + 3;
              const rightSeat2 = rowIndex * 4 + 4;

              return (
                <div key={rowIndex} className="flex items-center gap-2">
                  {/* Left side - 2 seats */}
                  <div className="flex gap-1.5">
                    {[leftSeat1, leftSeat2].map((seatNumber) => {
                      const isSelected = selectedSeats.includes(seatNumber);
                      return (
                        <button
                          key={seatNumber}
                          onClick={() => toggleSeat(seatNumber)}
                          className={`w-10 h-10 rounded-lg border-2 transition-all text-sm font-medium ${
                            isSelected
                              ? "bg-foreground text-background border-foreground"
                              : "bg-muted border-border hover:border-foreground"
                          }`}
                        >
                          {seatNumber}
                        </button>
                      );
                    })}
                  </div>

                  {/* Corridor */}
                  <div className="flex-1 min-w-[24px]" />

                  {/* Right side - 2 seats */}
                  <div className="flex gap-1.5">
                    {[rightSeat1, rightSeat2].map((seatNumber) => {
                      const isSelected = selectedSeats.includes(seatNumber);
                      return (
                        <button
                          key={seatNumber}
                          onClick={() => toggleSeat(seatNumber)}
                          className={`w-10 h-10 rounded-lg border-2 transition-all text-sm font-medium ${
                            isSelected
                              ? "bg-foreground text-background border-foreground"
                              : "bg-muted border-border hover:border-foreground"
                          }`}
                        >
                          {seatNumber}
                        </button>
                      );
                    })}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </main>

      <footer className="fixed bottom-16 left-0 right-0 bg-card border-t border-border z-10">
        <div className="px-6 py-4">
          <div className="max-w-sm mx-auto">
            <div className="bg-muted rounded-ios-lg p-4 mb-4">
              <div className="flex items-center justify-between mb-1">
                <span className="text-muted-foreground">Selected Seats</span>
                <span className="font-semibold">{selectedSeats.length}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-lg font-semibold">Total</span>
                <span className="text-xl font-bold">UGX {totalPrice.toLocaleString()}</span>
              </div>
            </div>
            <Button
              onClick={() => navigate("/payment")}
              disabled={selectedSeats.length === 0}
              className="w-full rounded-ios h-14 text-base font-medium"
            >
              Select Payment
            </Button>
          </div>
        </div>
      </footer>

      <BottomNav />
    </div>
  );
};

export default Seats;
