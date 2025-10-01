import { Button } from "@/components/ui/button";
import { ArrowLeft, Download } from "lucide-react";
import { useNavigate } from "react-router-dom";

const Ticket = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-background">
      <header className="px-6 py-4 border-b border-border flex items-center gap-4">
        <Button
          variant="ghost"
          size="icon"
          onClick={() => navigate("/home")}
          className="rounded-full"
        >
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <h1 className="text-xl font-semibold">My Ticket</h1>
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
    </div>
  );
};

export default Ticket;
