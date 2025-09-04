import React from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { Calendar, MapPin, Clock, QrCode, ArrowRight } from 'lucide-react';

interface Booking {
  id: string;
  operator: string;
  from: string;
  to: string;
  date: string;
  time: string;
  seats: string[];
  amount: number;
  status: 'upcoming' | 'completed' | 'cancelled';
}

const MyBookings: React.FC = () => {
  const navigate = useNavigate();

  const bookings: Booking[] = [
    {
      id: 'MB1703847291',
      operator: 'Nile Star Express',
      from: 'Kampala',
      to: 'Mbarara',
      date: '2025-01-15',
      time: '08:00',
      seats: ['12A', '12B'],
      amount: 50000,
      status: 'upcoming'
    },
    {
      id: 'MB1703234567',
      operator: 'Gateway Coach',
      from: 'Mbarara',
      to: 'Kampala',
      date: '2025-01-10',
      time: '14:30',
      seats: ['8C'],
      amount: 22000,
      status: 'completed'
    },
    {
      id: 'MB1702987654',
      operator: 'Smart Link',
      from: 'Kampala',
      to: 'Jinja',
      date: '2025-01-05',
      time: '10:15',
      seats: ['15A', '15B'],
      amount: 35000,
      status: 'completed'
    }
  ];

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'upcoming':
        return 'bg-green-100 text-green-800 border-green-200';
      case 'completed':
        return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'cancelled':
        return 'bg-red-100 text-red-800 border-red-200';
      default:
        return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  const handleBookingClick = (booking: Booking) => {
    navigate('/passenger/ticket', {
      state: {
        bus: { operator: booking.operator, departure: booking.time },
        from: booking.from,
        to: booking.to,
        date: booking.date,
        selectedSeats: booking.seats,
        totalFare: booking.amount,
        bookingId: booking.id,
        paymentMethod: 'Mobile Money'
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
          className="bg-white/20 backdrop-blur-lg rounded-2xl p-6 mb-6 border border-white/20"
        >
          <h1 className="text-2xl font-bold text-white mb-2">My Bookings</h1>
          <p className="text-white/80">Tap a booking to view your digital ticket</p>
        </motion.div>

        {/* Bookings List */}
        <div className="space-y-4">
          {bookings.map((booking, index) => (
            <motion.div
              key={booking.id}
              initial={{ y: 50, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.2 + index * 0.1 }}
              whileHover={{ y: -2 }}
              className="bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer"
              onClick={() => handleBookingClick(booking)}
            >
              {/* Status Badge */}
              <div className="relative">
                <div className="absolute top-4 right-4 z-10">
                  <span className={`px-3 py-1 rounded-full text-xs font-semibold border ${getStatusColor(booking.status)}`}>
                    {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                  </span>
                </div>
                
                {/* Card Header */}
                <div className="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white">
                  <div className="flex items-center justify-between mb-3">
                    <h3 className="font-bold text-lg">{booking.operator}</h3>
                    <QrCode className="w-6 h-6" />
                  </div>
                  
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-blue-100 text-sm">From</p>
                      <p className="font-bold">{booking.from}</p>
                    </div>
                    <div className="flex items-center space-x-2">
                      <div className="w-2 h-2 bg-white rounded-full" />
                      <div className="w-8 h-px bg-white/50" />
                      <ArrowRight className="w-4 h-4" />
                      <div className="w-8 h-px bg-white/50" />
                      <div className="w-2 h-2 bg-white rounded-full" />
                    </div>
                    <div className="text-right">
                      <p className="text-blue-100 text-sm">To</p>
                      <p className="font-bold">{booking.to}</p>
                    </div>
                  </div>
                </div>

                {/* Card Body */}
                <div className="p-4">
                  <div className="grid grid-cols-2 gap-4 mb-4">
                    <div className="flex items-center space-x-2">
                      <Calendar className="w-4 h-4 text-gray-500" />
                      <div>
                        <p className="text-gray-500 text-xs">Date</p>
                        <p className="font-semibold text-gray-800">{booking.date}</p>
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      <Clock className="w-4 h-4 text-gray-500" />
                      <div>
                        <p className="text-gray-500 text-xs">Time</p>
                        <p className="font-semibold text-gray-800">{booking.time}</p>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-gray-500 text-sm">Seats: {booking.seats.join(', ')}</p>
                      <p className="text-gray-500 text-sm">ID: {booking.id}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-2xl font-bold text-gray-800">
                        UGX {booking.amount.toLocaleString()}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Book New Trip */}
        <motion.button
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.6 }}
          onClick={() => navigate('/passenger/home')}
          className="w-full mt-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-2xl font-semibold text-lg shadow-xl"
        >
          Book New Trip
        </motion.button>
      </div>
    </motion.div>
  );
};

export default MyBookings;