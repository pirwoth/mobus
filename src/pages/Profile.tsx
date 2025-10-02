import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { LogOut, Moon, Sun, MapPin, Calendar } from "lucide-react";
import { useNavigate } from "react-router-dom";
import BottomNav from "@/components/BottomNav";
import { supabase } from "@/integrations/supabase/client";
import { useToast } from "@/hooks/use-toast";

const mockTravelHistory = [
  {
    id: 1,
    from: "Kampala",
    to: "Entebbe",
    date: "Jan 15, 2025",
    status: "Completed",
  },
  {
    id: 2,
    from: "Kampala",
    to: "Jinja",
    date: "Dec 28, 2024",
    status: "Completed",
  },
  {
    id: 3,
    from: "Entebbe",
    to: "Kampala",
    date: "Dec 15, 2024",
    status: "Completed",
  },
];

interface UserProfile {
  full_name: string | null;
  email: string;
  phone: string | null;
}

const Profile = () => {
  const navigate = useNavigate();
  const { toast } = useToast();
  const [darkMode, setDarkMode] = useState(false);
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    loadProfile();
  }, []);

  const loadProfile = async () => {
    try {
      const { data: { session } } = await supabase.auth.getSession();
      
      if (!session) {
        navigate("/login");
        return;
      }

      const { data, error } = await supabase
        .from("profiles")
        .select("full_name, email, phone")
        .eq("id", session.user.id)
        .single();

      if (error) {
        toast({
          title: "Error loading profile",
          description: error.message,
          variant: "destructive",
        });
      } else {
        setProfile(data);
      }
    } catch (error) {
      console.error("Error loading profile:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleLogout = async () => {
    try {
      const { error } = await supabase.auth.signOut();
      
      if (error) {
        toast({
          title: "Logout failed",
          description: error.message,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Logged out",
          description: "You've been successfully logged out.",
        });
        navigate("/login");
      }
    } catch (error) {
      toast({
        title: "Error",
        description: "An unexpected error occurred",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="min-h-screen bg-background pb-24">
      <header className="px-6 py-6 border-b border-border">
        <h1 className="text-2xl font-bold">Profile</h1>
      </header>

      <main className="px-6 py-6 space-y-6">
        {/* Account Info */}
        <div className="bg-card rounded-ios-lg shadow-ios p-6">
          {isLoading ? (
            <div className="animate-pulse space-y-4">
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 rounded-full bg-muted"></div>
                <div className="space-y-2 flex-1">
                  <div className="h-6 bg-muted rounded w-32"></div>
                  <div className="h-4 bg-muted rounded w-48"></div>
                </div>
              </div>
            </div>
          ) : profile ? (
            <>
              <div className="flex items-center gap-4 mb-4">
                <div className="w-16 h-16 rounded-full bg-foreground text-background flex items-center justify-center text-2xl font-bold">
                  {profile.full_name?.charAt(0).toUpperCase() || profile.email.charAt(0).toUpperCase()}
                </div>
                <div>
                  <h2 className="text-xl font-bold">{profile.full_name || "User"}</h2>
                  <p className="text-sm text-muted-foreground">{profile.email}</p>
                </div>
              </div>
              <div className="pt-4 border-t border-border space-y-2">
                <p className="text-sm text-muted-foreground">Phone</p>
                <p className="font-medium">{profile.phone || "Not provided"}</p>
              </div>
            </>
          ) : (
            <p className="text-muted-foreground">Failed to load profile</p>
          )}
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
