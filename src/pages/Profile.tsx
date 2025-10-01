import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { LogOut, Moon, Sun, MapPin, Calendar } from "lucide-react";
import { useNavigate } from "react-router-dom";
import BottomNav from "@/components/BottomNav";

const mockTravelHistory = [
  {
    id: 1,
    from: "New York",
    to: "Boston",
    date: "Jan 15, 2025",
    status: "Completed",
  },
  {
    id: 2,
    from: "Boston",
    to: "Philadelphia",
    date: "Dec 28, 2024",
    status: "Completed",
  },
  {
    id: 3,
    from: "Philadelphia",
    to: "Washington DC",
    date: "Dec 15, 2024",
    status: "Completed",
  },
];

const Profile = () => {
  const navigate = useNavigate();
  const [darkMode, setDarkMode] = useState(false);

  const handleLogout = () => {
    navigate("/login");
  };

  return (
    <div className="min-h-screen bg-background pb-24">
      <header className="px-6 py-6 border-b border-border">
        <h1 className="text-2xl font-bold">Profile</h1>
      </header>

      <main className="px-6 py-6 space-y-6">
        {/* Account Info */}
        <div className="bg-card rounded-ios-lg shadow-ios p-6">
          <div className="flex items-center gap-4 mb-4">
            <div className="w-16 h-16 rounded-full bg-foreground text-background flex items-center justify-center text-2xl font-bold">
              JD
            </div>
            <div>
              <h2 className="text-xl font-bold">John Doe</h2>
              <p className="text-sm text-muted-foreground">john@example.com</p>
            </div>
          </div>
          <div className="pt-4 border-t border-border space-y-2">
            <p className="text-sm text-muted-foreground">Phone</p>
            <p className="font-medium">+1 234 567 8900</p>
          </div>
        </div>

        {/* Theme Settings */}
        <div className="bg-card rounded-ios-lg shadow-ios p-6">
          <h3 className="font-semibold text-lg mb-4">Appearance</h3>
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              {darkMode ? (
                <Moon className="h-5 w-5 text-muted-foreground" />
              ) : (
                <Sun className="h-5 w-5 text-muted-foreground" />
              )}
              <Label htmlFor="dark-mode" className="text-base">
                Dark Mode
              </Label>
            </div>
            <Switch
              id="dark-mode"
              checked={darkMode}
              onCheckedChange={setDarkMode}
            />
          </div>
        </div>

        {/* Travel History */}
        <div className="bg-card rounded-ios-lg shadow-ios p-6">
          <h3 className="font-semibold text-lg mb-4">Travel History</h3>
          <div className="space-y-4">
            {mockTravelHistory.map((trip) => (
              <div
                key={trip.id}
                className="flex items-start gap-3 pb-4 border-b border-border last:border-0 last:pb-0"
              >
                <div className="w-10 h-10 rounded-full bg-muted flex items-center justify-center flex-shrink-0">
                  <MapPin className="h-5 w-5 text-muted-foreground" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="font-semibold">
                    {trip.from} → {trip.to}
                  </p>
                  <div className="flex items-center gap-2 mt-1 text-sm text-muted-foreground">
                    <Calendar className="h-4 w-4" />
                    <span>{trip.date}</span>
                  </div>
                </div>
                <span className="text-xs font-medium text-muted-foreground flex-shrink-0">
                  {trip.status}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Logout */}
        <Button
          onClick={handleLogout}
          variant="outline"
          className="w-full rounded-ios h-14 text-base font-medium"
        >
          <LogOut className="mr-2 h-5 w-5" />
          Logout
        </Button>
      </main>

      <BottomNav />
    </div>
  );
};

export default Profile;
