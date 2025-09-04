import React from 'react';
import { motion } from 'framer-motion';
import { useLocation, useNavigate } from 'react-router-dom';
import { Download, Share, Check, QrCode, MapPin, Clock, User } from 'lucide-react';

const DigitalTicket: React.FC = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const { bus, from, to, date, selectedSeats, totalFare, bookingId, paymentMethod } = location.state || {};

  const handleDownload = () => {
    // Simulate download
    alert('Ticket downloaded to your device');
  };

  const handleShare = () => {
    // Simulate share
    if (navigator.share) {
      navigator.share({
        title: 'MOBUS Bus Ticket',
        text: `Bus ticket for ${from} to ${to} on ${date}`,
        url: window.location.href
      });
    } else {
      alert('Ticket link copied to clipboard');
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
        {/* Success Message */}
        <motion.div
          initial={{ scale: 0 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
          className="text-center mb-6"
        >
          <div className="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <Check className="w-10 h-10 text-white" />
          </div>
          <h1 className="text-2xl font-bold text-white mb-2">Payment Successful!</h1>
          <p className="text-white/80">Your ticket has been generated</p>
        </motion.div>

        {/* Digital Ticket */}
        <motion.div
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.4 }}
          className="relative"
        >
          {/* Ticket Background with gradient */}
          <div className="bg-gradient-to-br from-blue-600 via-purple-600 to-cyan-500 rounded-3xl p-1 shadow-2xl">
            <div className="bg-white rounded-3xl overflow-hidden">
              {/* Header */}
              <div className="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <h2 className="text-2xl font-bold">MOBUS</h2>
                    <p className="text-blue-100">Digital Boarding Pass</p>
                  </div>
                  <QrCode className="w-8 h-8" />
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-blue-100 text-sm">From</p>
                    <p className="text-xl font-bold">{from || 'Kampala'}</p>
                  </div>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-white rounded-full" />
                    <div className="w-12 h-px bg-white/50" />
                    <MapPin className="w-4 h-4 text-white" />
                    <div className="w-12 h-px bg-white/50" />
                    <div className="w-2 h-2 bg-white rounded-full" />
                  </div>
                  <div className="text-right">
                    <p className="text-blue-100 text-sm">To</p>
                    <p className="text-xl font-bold">{to || 'Mbarara'}</p>
                  </div>
                </div>
              </div>

              {/* Ticket Details */}
              <div className="p-6">
                <div className="grid grid-cols-2 gap-4 mb-6">
                  <div>
                    <p className="text-gray-500 text-sm">Passenger</p>
                    <p className="font-semibold text-gray-800">John Smith</p>
                  </div>
                  <div>
                    <p className="text-gray-500 text-sm">Date</p>
                    <p className="font-semibold text-gray-800">{date}</p>
                  </div>
                  <div>
                    <p className="text-gray-500 text-sm">Booking ID</p>
                    <p className="font-semibold text-gray-800">{bookingId}</p>
                  </div>
                  <div>
                    <p className="text-gray-500 text-sm">Bus Operator</p>
                    <p className="font-semibold text-gray-800">{bus?.operator}</p>
                  </div>
                  <div>
                    <p className="text-gray-500 text-sm">Departure</p>
                    <p className="font-semibold text-gray-800">{bus?.departure}</p>
                  </div>
                  <div>
                    <p className="text-gray-500 text-sm">Seats</p>
                    <p className="font-semibold text-gray-800">{selectedSeats?.join(', ')}</p>
                  </div>
                </div>

                {/* QR Code */}
                <div className="text-center mb-6">
                  <div className="inline-block p-4 bg-gray-50 rounded-2xl">
                    <div className="w-32 h-32 bg-gradient-to-br from-gray-800 to-gray-600 rounded-lg flex items-center justify-center">
                      <QrCode className="w-20 h-20 text-white" />
                    </div>
                  </div>
                  <p className="text-gray-500 text-sm mt-2">Show this QR code when boarding</p>
                </div>

                {/* Total Amount */}
                <div className="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-4 text-center border border-blue-100">
                  <p className="text-gray-600 text-sm">Total Amount Paid</p>
                  <p className="text-3xl font-bold text-gray-800">
                    UGX {totalFare?.toLocaleString()}
                  </p>
                  <p className="text-green-600 text-sm font-medium">Paid via {paymentMethod}</p>
                </div>
              </div>

              {/* Perforated Edge Effect */}
              <div className="flex justify-center">
                <div className="flex space-x-2">
                  {Array.from({ length: 20 }).map((_, i) => (
                    <div key={i} className="w-1 h-1 bg-gray-300 rounded-full" />
                  ))}
                </div>
              </div>

              {/* Footer */}
              <div className="p-4 bg-gray-50 text-center">
                <p className="text-gray-600 text-xs">
                  Valid for travel on the specified date and route only
                </p>
              </div>
            </div>
          </div>
        </motion.div>

        {/* Action Buttons */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.6 }}
          className="flex space-x-4 mt-6"
        >
          <motion.button
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            onClick={handleDownload}
            className="flex-1 bg-white/20 backdrop-blur-lg border border-white/20 text-white py-3 rounded-2xl font-semibold flex items-center justify-center space-x-2"
          >
            <Download className="w-5 h-5" />
            <span>Download</span>
          </motion.button>
          
          <motion.button
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            onClick={handleShare}
            className="flex-1 bg-white/20 backdrop-blur-lg border border-white/20 text-white py-3 rounded-2xl font-semibold flex items-center justify-center space-x-2"
          >
            <Share className="w-5 h-5" />
            <span>Share</span>
          </motion.button>
        </motion.div>

        {/* Navigation Buttons */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.8 }}
          className="flex space-x-4 mt-4"
        >
          <button
            onClick={() => navigate('/passenger/bookings')}
            className="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-2xl font-semibold"
          >
            View All Bookings
          </button>
          <button
            onClick={() => navigate('/passenger/home')}
            className="flex-1 bg-white/20 backdrop-blur-lg border border-white/20 text-white py-3 rounded-2xl font-semibold"
          >
            Book Another
          </button>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default DigitalTicket;