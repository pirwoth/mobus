import React from 'react';
import { motion } from 'framer-motion';
import { useNavigate, useLocation } from 'react-router-dom';
import { Clock, MapPin, Users, Star } from 'lucide-react';

interface Bus {
  id: string;
  operator: string;
  departure: string;
  arrival: string;
  fare: number;
  availableSeats: number;
  rating: number;
  amenities: string[];
}

const SearchResults: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { from, to, date } = location.state || {};

  const buses: Bus[] = [
    {
      id: 'NLS001',
      operator: 'Nile Star Express',
      departure: '08:00',
      arrival: '14:30',
      fare: 25000,
      availableSeats: 18,
      rating: 4.8,
      amenities: ['AC', 'WiFi', 'Charging Port']
    },
    {
      id: 'GWY002',
      operator: 'Gateway Coach',
      departure: '10:15',
      arrival: '16:45',
      fare: 22000,
      availableSeats: 12,
      rating: 4.5,
      amenities: ['AC', 'Entertainment', 'Snacks']
    },
    {
      id: 'SMT003',
      operator: 'Smart Link',
      departure: '12:30',
      arrival: '19:00',
      fare: 28000,
      availableSeats: 8,
      rating: 4.9,
      amenities: ['AC', 'WiFi', 'Recliner Seats', 'Meals']
    }
  ];

  const handleSelectBus = (bus: Bus) => {
    navigate('/passenger/seats', { 
      state: { 
        bus, 
        from, 
        to, 
        date,
        totalFare: bus.fare
      } 
    });
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -20 }}
      className="min-h-screen pt-20 px-4"
    >
      <div className="max-w-md mx-auto">
        {/* Header */}
        <motion.div
          initial={{ y: -20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.1 }}
          className="bg-white/20 backdrop-blur-lg rounded-2xl p-4 mb-6 border border-white/20"
        >
          <div className="flex items-center space-x-4 text-white">
            <div className="flex-1">
              <div className="flex items-center space-x-2">
                <MapPin className="w-4 h-4 text-blue-300" />
                <span className="font-semibold">{from || 'Kampala'}</span>
              </div>
            </div>
            <div className="w-8 h-px bg-white/40" />
            <div className="flex-1 text-right">
              <div className="flex items-center justify-end space-x-2">
                <span className="font-semibold">{to || 'Mbarara'}</span>
                <MapPin className="w-4 h-4 text-purple-300" />
              </div>
            </div>
          </div>
          <p className="text-center text-white/70 text-sm mt-2">{date || 'Today'}</p>
        </motion.div>

        {/* Results */}
        <div className="space-y-4">
          {buses.map((bus, index) => (
            <motion.div
              key={bus.id}
              initial={{ y: 50, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.2 + index * 0.1 }}
              className="bg-white rounded-2xl p-5 shadow-lg border border-gray-100"
            >
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h3 className="font-bold text-gray-800 text-lg">{bus.operator}</h3>
                  <div className="flex items-center space-x-1 mt-1">
                    <Star className="w-4 h-4 text-yellow-400 fill-current" />
                    <span className="text-gray-600 text-sm">{bus.rating}</span>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold text-gray-800">
                    UGX {bus.fare.toLocaleString()}
                  </p>
                  <p className="text-green-600 text-sm font-medium">
                    {bus.availableSeats} seats left
                  </p>
                </div>
              </div>

              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center space-x-4">
                  <div className="text-center">
                    <p className="text-xl font-bold text-gray-800">{bus.departure}</p>
                    <p className="text-gray-500 text-xs">Departure</p>
                  </div>
                  <div className="flex-1 flex items-center space-x-2">
                    <div className="w-2 h-2 bg-blue-500 rounded-full" />
                    <div className="flex-1 h-px bg-gray-300" />
                    <Clock className="w-4 h-4 text-gray-400" />
                    <div className="flex-1 h-px bg-gray-300" />
                    <div className="w-2 h-2 bg-purple-500 rounded-full" />
                  </div>
                  <div className="text-center">
                    <p className="text-xl font-bold text-gray-800">{bus.arrival}</p>
                    <p className="text-gray-500 text-xs">Arrival</p>
                  </div>
                </div>
              </div>

              <div className="flex items-center justify-between">
                <div className="flex flex-wrap gap-2">
                  {bus.amenities.map((amenity, i) => (
                    <span
                      key={i}
                      className="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full"
                    >
                      {amenity}
                    </span>
                  ))}
                </div>
                <motion.button
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  onClick={() => handleSelectBus(bus)}
                  className="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-xl font-semibold shadow-lg"
                >
                  Select
                </motion.button>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </motion.div>
  );
};

export default SearchResults;