import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { MapPin, Calendar, Clock, Users, Bus, ArrowRight } from "lucide-react";
import { useLocation } from "wouter";
import { format } from "date-fns";

interface SearchParams {
  from: string;
  to: string;
  date: string;
}

export default function Home() {
  const [, setLocation] = useLocation();
  const [searchParams, setSearchParams] = useState<SearchParams>({
    from: "",
    to: "",
    date: format(new Date(), "yyyy-MM-dd"),
  });
  const [hasSearched, setHasSearched] = useState(false);

  const { data, isLoading, error } = useQuery({
    queryKey: ["/api/routes/search", searchParams.from, searchParams.to, searchParams.date],
    enabled: hasSearched && !!searchParams.from && !!searchParams.to && !!searchParams.date,
  });

  // Ensure routes is always an array for rendering
  const routes: any[] = Array.isArray(data) ? data : [];

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchParams.from && searchParams.to && searchParams.date) {
      setHasSearched(true);
    }
  };

  const handleSelectSeat = (routeId: string) => {
    const searchQuery = new URLSearchParams({
      routeId,
      date: searchParams.date,
    }).toString();
    setLocation(`/seat-selection?${searchQuery}`);
  };

  const cities = [
    "Kampala", "Gulu", "Arua", "Mbarara", "Jinja", "Mbale", "Fort Portal", "Masaka", "Entebbe", "Kasese", "Soroti", "Tororo", "Mukono", "Kabale"
  ];

  return (
  <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="py-8 md:py-12" data-testid="section-hero">
        <div className="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
          <div className="text-center mb-8 md:mb-12">
            <h2 className="text-4xl font-bold text-gray-900 mb-4" data-testid="text-hero-title">
              Book Your Bus Ticket
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto" data-testid="text-hero-description">
              Fast, reliable, and comfortable bus travel across the country. Find your perfect route and book in minutes.
            </p>
          </div>

          {/* Search Form */}
          <Card className="p-4 md:p-8 mb-8 md:mb-12">
            <form onSubmit={handleSearch} className="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-6">
              <div>
                <Label htmlFor="from" className="text-sm font-medium text-gray-700 mb-2 block">
                  From
                </Label>
                <div className="relative">
                  <Input
                    id="from"
                    list="cities-from"
                    value={searchParams.from}
                    onChange={(e) => setSearchParams(prev => ({ ...prev, from: e.target.value }))}
                    placeholder="Select departure city"
                    className="pl-10"
                    data-testid="input-from"
                  />
                  <MapPin className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <datalist id="cities-from">
                    {cities.map(city => (
                      <option key={city} value={city} />
                    ))}
                  </datalist>
                </div>
              </div>
              
              <div>
                <Label htmlFor="to" className="text-sm font-medium text-gray-700 mb-2 block">
                  To
                </Label>
                <div className="relative">
                  <Input
                    id="to"
                    list="cities-to"
                    value={searchParams.to}
                    onChange={(e) => setSearchParams(prev => ({ ...prev, to: e.target.value }))}
                    placeholder="Select destination city"
                    className="pl-10"
                    data-testid="input-to"
                  />
                  <MapPin className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <datalist id="cities-to">
                    {cities.map(city => (
                      <option key={city} value={city} />
                    ))}
                  </datalist>
                </div>
              </div>
              
              <div>
                <Label htmlFor="date" className="text-sm font-medium text-gray-700 mb-2 block">
                  Departure Date
                </Label>
                <div className="relative">
                  <Input
                    id="date"
                    type="date"
                    value={searchParams.date}
                    onChange={(e) => setSearchParams(prev => ({ ...prev, date: e.target.value }))}
                    min={format(new Date(), "yyyy-MM-dd")}
                    className="pl-10"
                    data-testid="input-date"
                  />
                  <Calendar className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                </div>
              </div>
              
              <div className="flex items-end">
                <Button type="submit" className="w-full" data-testid="button-search">
                  <Bus className="mr-2 h-4 w-4" />
                  Search Buses
                </Button>
              </div>
            </form>
          </Card>

          {/* Search Results */}
          {hasSearched && (
            <div className="space-y-4 md:space-y-6">
              <h3 className="text-2xl font-bold text-gray-900" data-testid="text-results-title">
                Available Buses
              </h3>
              
              {isLoading && (
                <div className="text-center py-8" data-testid="loading-results">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
                  <p className="text-muted-foreground">Searching for buses...</p>
                </div>
              )}
              
              {error && (
                <Card className="p-6 text-center" data-testid="error-results">
                  <p className="text-red-600">Failed to search buses. Please try again.</p>
                </Card>
              )}
              
              {routes && Array.isArray(routes) && routes.length === 0 && (
                <Card className="p-6 text-center" data-testid="no-results">
                  <p className="text-gray-600">No buses found for the selected route and date.</p>
                </Card>
              )}
              
              {routes && Array.isArray(routes) && routes.map((route: any) => (
                <Card key={route.id} className="hover:shadow-lg transition-shadow" data-testid={`route-card-${route.id}`}>
                  <CardContent className="p-6">
                    <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                      <div className="flex-1">
                        <div className="flex items-center mb-4">
                          <div className="bg-blue-100 p-3 rounded-lg mr-4">
                            <Bus className="text-primary text-xl" />
                          </div>
                          <div>
                            <h4 className="text-lg font-semibold text-gray-900" data-testid={`text-operator-${route.id}`}>
                              {route.bus.operator.companyName}
                            </h4>
                            <p className="text-gray-600" data-testid={`text-bus-type-${route.id}`}>
                              {route.bus.busType}
                            </p>
                          </div>
                        </div>
                        
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 md:gap-4 text-sm">
                          <div>
                            <p className="text-gray-500 flex items-center">
                              <Clock className="mr-1 h-4 w-4" />
                              Departure
                            </p>
                            <p className="font-medium" data-testid={`text-departure-${route.id}`}>
                              {route.departureTime}
                            </p>
                          </div>
                          <div>
                            <p className="text-gray-500 flex items-center">
                              <Clock className="mr-1 h-4 w-4" />
                              Arrival
                            </p>
                            <p className="font-medium" data-testid={`text-arrival-${route.id}`}>
                              {route.arrivalTime}
                            </p>
                          </div>
                          <div>
                            <p className="text-gray-500 flex items-center">
                              <ArrowRight className="mr-1 h-4 w-4" />
                              Duration
                            </p>
                            <p className="font-medium" data-testid={`text-duration-${route.id}`}>
                              {route.duration}
                            </p>
                          </div>
                          <div>
                            <p className="text-gray-500 flex items-center">
                              <Users className="mr-1 h-4 w-4" />
                              Available Seats
                            </p>
                            <Badge variant="secondary" className="text-secondary" data-testid={`text-seats-${route.id}`}>
                              {route.availableSeats} seats
                            </Badge>
                          </div>
                        </div>
                        
                        {route.bus.amenities && (
                          <div className="mt-4">
                            <p className="text-gray-500 text-sm mb-2">Amenities:</p>
                            <div className="flex flex-wrap gap-2">
                              {route.bus.amenities.map((amenity: string) => (
                                <Badge key={amenity} variant="outline" className="text-xs">
                                  {amenity}
                                </Badge>
                              ))}
                            </div>
                          </div>
                        )}
                      </div>
                      
                      <div className="mt-4 md:mt-6 lg:mt-0 lg:ml-6 text-right">
                        <div className="text-2xl font-bold text-gray-900 mb-2" data-testid={`text-price-${route.id}`}>
                          ${route.price}
                        </div>
                        <Button
                          onClick={() => handleSelectSeat(route.id)}
                          disabled={route.availableSeats === 0}
                          data-testid={`button-select-seat-${route.id}`}
                        >
                          {route.availableSeats === 0 ? "Sold Out" : "Select Seats"}
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          )}
        </div>
      </section>
    </div>
  );
}
