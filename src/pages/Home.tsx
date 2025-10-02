import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Calendar } from "lucide-react";
import BottomNav from "@/components/BottomNav";
import { supabase } from "@/integrations/supabase/client";

const Home = () => {
  const navigate = useNavigate();
  const [searchData, setSearchData] = useState({
    origin: "",
    destination: "",
    date: "",
  });

  useEffect(() => {
    // Check if user is logged in
    supabase.auth.getSession().then(({ data: { session } }) => {
      if (!session) {
        navigate("/login");
      }
    });
  }, [navigate]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    navigate("/buses");
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchData({
      ...searchData,
      [e.target.id]: e.target.value,
    });
  };

  return (
    <div className="min-h-screen bg-background pb-24">
      <header className="px-6 py-6 border-b border-border">
        <h1 className="text-2xl font-bold">MoBus</h1>
      </header>

      <main className="px-6 py-8 flex items-center justify-center min-h-[calc(100vh-73px-96px)]">
        <div className="w-full max-w-md">
          <div className="mb-8 text-center">
            <h2 className="text-3xl font-bold mb-2">Book Your Journey</h2>
            <p className="text-muted-foreground">Find buses for your next trip</p>
          </div>

          <form onSubmit={handleSearch}>
            <div className="bg-card rounded-ios-lg shadow-ios-lg p-6 space-y-5">
              <div className="space-y-2">
                <Label htmlFor="origin">From</Label>
                <Input
                  id="origin"
                  type="text"
                  placeholder="Origin city"
                  value={searchData.origin}
                  onChange={handleChange}
                  required
                  className="rounded-ios"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="destination">To</Label>
                <Input
                  id="destination"
                  type="text"
                  placeholder="Destination city"
                  value={searchData.destination}
                  onChange={handleChange}
                  required
                  className="rounded-ios"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="date">Date</Label>
                <div className="relative">
                  <Input
                    id="date"
                    type="date"
                    value={searchData.date}
                    onChange={handleChange}
                    required
                    className="rounded-ios"
                  />
                  <Calendar className="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground pointer-events-none" />
                </div>
              </div>

              <Button type="submit" className="w-full rounded-ios h-14 text-base font-medium mt-6">
                Search Buses
              </Button>
            </div>
          </form>
        </div>
      </main>

      <BottomNav />
    </div>
  );
};

export default Home;
