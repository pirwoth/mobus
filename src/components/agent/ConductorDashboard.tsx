import React from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { Bus, Clock, MapPin, Users, ArrowRight } from 'lucide-react';

interface Trip {
  id: string;
  busOperator: string;
  busNumber: string;
  route: string;
  departureTime: string;
  status: 'upcoming' | 'active' | 'completed';
  passengers: number;
  maxPassengers: number;
}

const ConductorDashboard: React.FC = () => {
  const navigate = useNavigate();

  const trips: Trip[] = [
    {
      id: 'TRP001',
      busOperator: 'Nile Star Express',
      busNumber: 'NSE-4501',
      route: 'Kampala → Mbarara',
      departureTime: '08:00',
      status: 'active',
      passengers: 28,
      maxPassengers: 40
    },
    {
      id: 'TRP002',
      busOperator: 'Nile Star Express',
      busNumber: 'NSE-4501',
      route: 'Mbarara → Kampala',
      departureTime: '14:30',
      status: 'upcoming',
      passengers: 15,
      maxPassengers: 40
    },
    {
      id: 'TRP003',
      busOperator: 'Nile Star Express',
      busNumber: 'NSE-4501',
      route: 'Kampala → Jinja',
      departureTime: '06:00',
      status: 'completed',
      passengers: 35,
      maxPassengers: 40
    }
  ];

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return 'bg-green-100 text-green-800 border-green-200';
      case 'upcoming':
        return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'completed':
        return 'bg-gray-100 text-gray-800 border-gray-200';
      default:
        return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  const handleTripClick = (trip: Trip) => {
    navigate(`/agent/trip/${trip.id}`, { state: { trip } });
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
          className="bg-white/20 backdrop-blur-lg rounded-2xl p-6 mb-6 border border-white/20"
        >
          <div className="flex items-center space-x-4 mb-4">
            <div className="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
              <Bus className="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 className="text-xl font-bold text-white">Conductor Dashboard</h1>
              <p className="text-white/80 text-sm">Welcome back, James Mukasa</p>
            </div>
          </div>
          
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-white/20 rounded-xl p-3 text-center">
              <p className="text-white/80 text-sm">Today's Trips</p>
              <p className="text-white text-xl font-bold">3</p>
            </div>
            <div className="bg-white/20 rounded-xl p-3 text-center">
              <p className="text-white/80 text-sm">Active Trip</p>
              <p className="text-white text-xl font-bold">1</p>
            </div>
          </div>
        </motion.div>

        {/* My Trips Section */}
        <motion.div
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.2 }}
          className="mb-6"
        >
          <h2 className="text-white text-lg font-semibold mb-4">My Trips</h2>
          
          <div className="space-y-4">
            {trips.map((trip, index) => (
              <motion.div
                key={trip.id}
                initial={{ y: 30, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ delay: 0.3 + index * 0.1 }}
                whileHover={{ y: -2 }}
                className="bg-white rounded-2xl p-5 shadow-lg cursor-pointer"
                onClick={() => handleTripClick(trip)}
              >
                <div className="flex items-start justify-between mb-4">
                  <div className="flex-1">
                    <div className="flex items-center space-x-2 mb-2">
                      <h3 className="font-bold text-gray-800">{trip.busOperator}</h3>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium border ${getStatusColor(trip.status)}`}>
                        {trip.status.charAt(0).toUpperCase() + trip.status.slice(1)}
                      </span>
                    </div>
                    <p className="text-gray-600 text-sm">Bus: {trip.busNumber}</p>
                    <p className="text-gray-600 text-sm">Trip ID: {trip.id}</p>
                  </div>
                  <ArrowRight className="w-5 h-5 text-gray-400" />
                </div>

                <div className="flex items-center space-x-4 mb-4">
                  <div className="flex items-center space-x-2">
                    <MapPin className="w-4 h-4 text-purple-500" />
                    <span className="text-gray-700 font-medium">{trip.route}</span>
                  </div>
                </div>

                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <Clock className="w-4 h-4 text-blue-500" />
                    <span className="text-gray-700 font-semibold">{trip.departureTime}</span>
                  </div>
                  <div className="flex items-center space-x-2">
                    <Users className="w-4 h-4 text-green-500" />
                    <span className="text-gray-700 font-medium">
                      {trip.passengers}/{trip.maxPassengers}
                    </span>
                  </div>
                </div>

                {trip.status === 'active' && (
                  <div className="mt-3 bg-green-50 border border-green-200 rounded-lg p-2">
                    <p className="text-green-800 text-sm font-medium text-center">
                      🟢 Currently Active
                    </p>
                  </div>
                )}
              </motion.div>
            ))}
          </div>
        </motion.div>

        {/* Quick Actions */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.6 }}
          className="grid grid-cols-2 gap-4"
        >
          <button className="bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center hover:bg-white/30 transition-colors">
            <Bus className="w-6 h-6 mx-auto mb-2" />
            <span className="text-sm font-medium">Bus Status</span>
          </button>
          <button className="bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center hover:bg-white/30 transition-colors">
            <Users className="w-6 h-6 mx-auto mb-2" />
            <span className="text-sm font-medium">Passenger List</span>
          </button>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default ConductorDashboard;