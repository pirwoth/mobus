import { useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { ArrowLeft, Clock } from "lucide-react";

const mockBuses = [
  {
    id: 1,
    operator: "Express Transit",
    departure: "08:00 AM",
    arrival: "02:00 PM",
    duration: "6h",
    price: "$45",
  },
  {
    id: 2,
    operator: "City Connect",
    departure: "10:30 AM",
    arrival: "04:30 PM",
    duration: "6h",
    price: "$40",
  },
  {
    id: 3,
    operator: "Premium Lines",
    departure: "01:00 PM",
    arrival: "07:00 PM",
    duration: "6h",
    price: "$55",
  },
  {
    id: 4,
    operator: "Budget Bus",
    departure: "04:00 PM",
    arrival: "10:00 PM",
    duration: "6h",
    price: "$35",
  },
];

const Buses = () => {
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
        <h1 className="text-xl font-semibold">Available Buses</h1>
      </header>

      <main className="px-6 py-6 space-y-4">
        {mockBuses.map((bus) => (
          <button
            key={bus.id}
            onClick={() => navigate("/seats")}
            className="w-full bg-card rounded-ios-lg shadow-ios p-5 text-left hover:shadow-ios-lg transition-shadow"
          >
            <div className="flex items-start justify-between mb-3">
              <div>
                <h3 className="font-semibold text-lg mb-1">{bus.operator}</h3>
                <div className="flex items-center gap-1 text-sm text-muted-foreground">
                  <Clock className="h-4 w-4" />
                  <span>{bus.duration}</span>
                </div>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold">{bus.price}</p>
              </div>
            </div>
            <div className="flex items-center justify-between text-sm">
              <span className="font-medium">{bus.departure}</span>
              <span className="text-muted-foreground">→</span>
              <span className="font-medium">{bus.arrival}</span>
            </div>
          </button>
        ))}
      </main>
    </div>
  );
};

export default Buses;
