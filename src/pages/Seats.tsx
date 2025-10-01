import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-react";

const ROWS = 10;
const COLS = 4;

const Seats = () => {
  const navigate = useNavigate();
  const [selectedSeats, setSelectedSeats] = useState<number[]>([]);
  const pricePerSeat = 45;

  const toggleSeat = (seatNumber: number) => {
    setSelectedSeats((prev) =>
      prev.includes(seatNumber)
        ? prev.filter((s) => s !== seatNumber)
        : [...prev, seatNumber]
    );
  };

  const totalPrice = selectedSeats.length * pricePerSeat;

  return (
    <div className="min-h-screen bg-background flex flex-col">
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

      <main className="flex-1 px-6 py-6 pb-32">
        <div className="max-w-md mx-auto">
          <div className="mb-6 flex items-center justify-center gap-6 text-sm">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-lg bg-muted border border-border" />
              <span className="text-muted-foreground">Available</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-lg bg-foreground" />
              <span className="text-muted-foreground">Selected</span>
            </div>
          </div>

          <div className="space-y-3">
            {Array.from({ length: ROWS }).map((_, rowIndex) => (
              <div key={rowIndex} className="flex justify-center gap-3">
                {Array.from({ length: COLS }).map((_, colIndex) => {
                  const seatNumber = rowIndex * COLS + colIndex + 1;
                  const isSelected = selectedSeats.includes(seatNumber);

                  return (
                    <button
                      key={seatNumber}
                      onClick={() => toggleSeat(seatNumber)}
                      className={`w-12 h-12 rounded-lg border-2 transition-all font-medium ${
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
            ))}
          </div>
        </div>
      </main>

      <footer className="fixed bottom-0 left-0 right-0 bg-card border-t border-border">
        <div className="px-6 py-4">
          <div className="bg-muted rounded-ios-lg p-4 mb-4">
            <div className="flex items-center justify-between mb-1">
              <span className="text-muted-foreground">Selected Seats</span>
              <span className="font-semibold">{selectedSeats.length}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-lg font-semibold">Total</span>
              <span className="text-2xl font-bold">${totalPrice}</span>
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
      </footer>
    </div>
  );
};

export default Seats;
