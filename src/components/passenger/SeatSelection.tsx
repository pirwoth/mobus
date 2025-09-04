import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate, useLocation } from 'react-router-dom';
import { ArrowLeft, User } from 'lucide-react';

const SeatSelection: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { bus, from, to, date } = location.state || {};
  
  const [selectedSeats, setSelectedSeats] = useState<string[]>([]);

  // Generate seat layout (40 seats in 2-2 configuration)
  const generateSeats = () => {
    const seats = [];
    const occupiedSeats = ['1A', '3B', '5A', '8B', '12A', '15B', '18A', '20B'];
    
    for (let row = 1; row <= 10; row++) {
      seats.push([
        { id: `${row}A`, occupied: occupiedSeats.includes(`${row}A`) },
        { id: `${row}B`, occupied: occupiedSeats.includes(`${row}B`) },
        null, // aisle
        { id: `${row}C`, occupied: occupiedSeats.includes(`${row}C`) },
        { id: `${row}D`, occupied: occupiedSeats.includes(`${row}D`) }
      ]);
    }
    return seats;
  };

  const seats = generateSeats();

  const toggleSeat = (seatId: string) => {
    setSelectedSeats(prev => 
      prev.includes(seatId) 
        ? prev.filter(id => id !== seatId)
        : [...prev, seatId]
    );
  };

  const getSeatStatus = (seat: any) => {
    if (!seat) return '';
    if (seat.occupied) return 'occupied';
    if (selectedSeats.includes(seat.id)) return 'selected';
    return 'available';
  };

  const getSeatClassName = (status: string) => {
    const base = 'w-10 h-10 rounded-lg border-2 flex items-center justify-center text-xs font-semibold transition-all duration-200';
    
    switch (status) {
      case 'occupied':
        return `${base} bg-gray-300 border-gray-400 text-gray-500 cursor-not-allowed`;
      case 'selected':
        return `${base} bg-gradient-to-r from-blue-500 to-purple-600 border-blue-500 text-white shadow-lg scale-110`;
      case 'available':
        return `${base} bg-white border-gray-300 text-gray-700 hover:border-blue-400 hover:shadow-md cursor-pointer`;
      default:
        return base;
    }
  };

  const handleProceedToPayment = () => {
    if (selectedSeats.length > 0) {
      const totalFare = (bus?.fare || 0) * selectedSeats.length;
      navigate('/passenger/payment', { 
        state: { 
          bus, 
          from, 
          to, 
          date, 
          selectedSeats,
          totalFare
        } 
      });
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
        {/* Header */}
        <motion.div
          initial={{ y: -20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.1 }}
          className="bg-white rounded-2xl p-4 mb-6 shadow-lg"
        >
          <div className="flex items-center space-x-4 mb-3">
            <button
              onClick={() => navigate(-1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ArrowLeft className="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h1 className="text-lg font-bold text-gray-800">{bus?.operator}</h1>
              <p className="text-gray-600 text-sm">{from} → {to}</p>
            </div>
          </div>
        </motion.div>

        {/* Bus Layout */}
        <motion.div
          initial={{ scale: 0.9, opacity: 0 }}
          animate={{ scale: 1, opacity: 1 }}
          transition={{ delay: 0.2 }}
          className="bg-white rounded-2xl p-6 mb-6 shadow-lg"
        >
          <h2 className="text-center text-lg font-bold text-gray-800 mb-6">Select Your Seats</h2>
          
          {/* Driver Area */}
          <div className="flex justify-end mb-4">
            <div className="w-16 h-8 bg-gray-800 rounded-t-lg flex items-center justify-center">
              <User className="w-4 h-4 text-white" />
            </div>
          </div>

          {/* Seat Grid */}
          <div className="space-y-2">
            {seats.map((row, rowIndex) => (
              <div key={rowIndex} className="flex items-center justify-center space-x-2">
                {row.map((seat, seatIndex) => (
                  <div key={seatIndex} className="flex-shrink-0">
                    {seat ? (
                      <button
                        onClick={() => !seat.occupied && toggleSeat(seat.id)}
                        disabled={seat.occupied}
                        className={getSeatClassName(getSeatStatus(seat))}
                      >
                        {seat.id}
                      </button>
                    ) : (
                      <div className="w-10 h-10" /> // Aisle space
                    )}
                  </div>
                ))}
              </div>
            ))}
          </div>

          {/* Legend */}
          <div className="flex items-center justify-center space-x-6 mt-6 text-sm">
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 bg-white border-2 border-gray-300 rounded" />
              <span className="text-gray-600">Available</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded" />
              <span className="text-gray-600">Selected</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className="w-4 h-4 bg-gray-300 rounded" />
              <span className="text-gray-600">Occupied</span>
            </div>
          </div>
        </motion.div>

        {/* Booking Summary */}
        {selectedSeats.length > 0 && (
          <motion.div
            initial={{ y: 50, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            className="bg-white rounded-2xl p-4 mb-6 shadow-lg"
          >
            <div className="flex items-center justify-between mb-4">
              <div>
                <p className="text-gray-600 text-sm">Selected Seats</p>
                <p className="font-bold text-gray-800">{selectedSeats.join(', ')}</p>
              </div>
              <div className="text-right">
                <p className="text-gray-600 text-sm">Total Fare</p>
                <p className="text-2xl font-bold text-gray-800">
                  UGX {((bus?.fare || 0) * selectedSeats.length).toLocaleString()}
                </p>
              </div>
            </div>
          </motion.div>
        )}

        {/* Proceed Button */}
        {selectedSeats.length > 0 && (
          <motion.button
            initial={{ y: 50, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            onClick={handleProceedToPayment}
            className="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-2xl font-semibold text-lg shadow-xl"
          >
            Proceed to Payment
          </motion.button>
        )}
      </div>
    </motion.div>
  );
};

export default SeatSelection;