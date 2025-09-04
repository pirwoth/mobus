import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { Search, MapPin, Calendar, User, Star } from 'lucide-react';

const PassengerHome: React.FC = () => {
  const navigate = useNavigate();
  const [from, setFrom] = useState('');
  const [to, setTo] = useState('');
  const [date, setDate] = useState('');

  const handleSearch = () => {
    if (from && to && date) {
      navigate('/passenger/search', { state: { from, to, date } });
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -20 }}
      className="min-h-screen pt-20 px-4"
    >
      <div className="max-w-md mx-auto">
        {/* Welcome Section */}
        <motion.div
          initial={{ scale: 0.9 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.2 }}
          className="bg-white/20 backdrop-blur-lg rounded-3xl p-6 mb-6 border border-white/20"
        >
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-white/80 text-sm font-medium">Welcome back,</h2>
              <h1 className="text-white text-xl font-bold">John Smith</h1>
            </div>
            <div className="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center">
              <User className="w-6 h-6 text-white" />
            </div>
          </div>
          
          <div className="flex items-center space-x-2 text-yellow-300">
            <Star className="w-4 h-4 fill-current" />
            <span className="text-white/80 text-sm">Premium Member</span>
          </div>
        </motion.div>

        {/* Search Section */}
        <motion.div
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.4 }}
          className="bg-white rounded-3xl p-6 shadow-2xl"
        >
          <h2 className="text-gray-800 text-lg font-bold mb-6">Book Your Journey</h2>
          
          <div className="space-y-4">
            <div className="relative">
              <MapPin className="absolute left-3 top-3 w-5 h-5 text-blue-500" />
              <input
                type="text"
                placeholder="From"
                value={from}
                onChange={(e) => setFrom(e.target.value)}
                className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <div className="relative">
              <MapPin className="absolute left-3 top-3 w-5 h-5 text-purple-500" />
              <input
                type="text"
                placeholder="To"
                value={to}
                onChange={(e) => setTo(e.target.value)}
                className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              />
            </div>
            
            <div className="relative">
              <Calendar className="absolute left-3 top-3 w-5 h-5 text-cyan-500" />
              <input
                type="date"
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
              />
            </div>
          </div>
          
          <motion.button
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            onClick={handleSearch}
            className="w-full mt-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-xl font-semibold flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transition-shadow"
          >
            <Search className="w-5 h-5" />
            <span>Search Buses</span>
          </motion.button>
        </motion.div>

        {/* Quick Actions */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.6 }}
          className="mt-6 flex space-x-4"
        >
          <button
            onClick={() => navigate('/passenger/bookings')}
            className="flex-1 bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center hover:bg-white/30 transition-colors"
          >
            My Bookings
          </button>
          <button className="flex-1 bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center hover:bg-white/30 transition-colors">
            Support
          </button>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default PassengerHome;