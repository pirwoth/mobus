import { Button } from "@/components/ui/button";
import { Download } from "lucide-react";
import BottomNav from "@/components/BottomNav";

const Ticket = () => {
  return (
    <div className="min-h-screen bg-background pb-24">
      <header className="px-6 py-6 border-b border-border">
        <h1 className="text-2xl font-bold">My Ticket</h1>
      </header>

      <main className="px-6 py-8">
        <div className="max-w-md mx-auto">
          <div className="bg-foreground text-background rounded-ios-lg p-8 mb-6 flex items-center justify-center min-h-[280px]">
            <div className="text-center">
              <div className="w-48 h-48 bg-background rounded-ios mx-auto mb-4 flex items-center justify-center">
                <div className="text-foreground text-xs font-mono">
                  <div className="grid grid-cols-6 gap-1">
                    {Array.from({ length: 36 }).map((_, i) => (
                      <div key={i} className="w-3 h-3 bg-foreground rounded-sm" />
                    ))}
                  </div>
                </div>
              </div>
              <p className="text-sm text-background/80">Scan at boarding</p>
            </div>
          </div>

          <div className="bg-card rounded-ios-lg shadow-ios p-6 space-y-4 mb-6">
            <div>
              <p className="text-sm text-muted-foreground mb-1">Passenger</p>
              <p className="font-semibold text-lg">John Doe</p>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-muted-foreground mb-1">From</p>
                <p className="font-semibold">New York</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-1">To</p>
                <p className="font-semibold">Boston</p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-muted-foreground mb-1">Date</p>
                <p className="font-semibold">Jan 15, 2025</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-1">Departure</p>
                <p className="font-semibold">08:00 AM</p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-sm text-muted-foreground mb-1">Seat(s)</p>
                <p className="font-semibold">12, 13, 14</p>
              </div>
              <div>
                <p className="text-sm text-muted-foreground mb-1">Booking ID</p>
                <p className="font-semibold font-mono">MB45782</p>
              </div>
            </div>
          </div>

          <Button className="w-full rounded-ios h-14 text-base font-medium" variant="outline">
            <Download className="mr-2 h-5 w-5" />
            Download Ticket
          </Button>
        </div>
      </main>

      <BottomNav />
    </div>
  );
};

export default Ticket;
