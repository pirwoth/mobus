import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate, useLocation } from 'react-router-dom';
import { ArrowLeft, CreditCard, Phone, Shield } from 'lucide-react';

const Payment: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { bus, from, to, date, selectedSeats, totalFare } = location.state || {};
  
  const [phoneNumber, setPhoneNumber] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('mobilemoney');
  const [loading, setLoading] = useState(false);

  const handlePayment = async () => {
    setLoading(true);
    // Simulate payment process
    setTimeout(() => {
      setLoading(false);
      navigate('/passenger/ticket', { 
        state: { 
          bus, 
          from, 
          to, 
          date, 
          selectedSeats,
          totalFare,
          bookingId: `MB${Date.now()}`,
          paymentMethod: 'Mobile Money'
        } 
      });
    }, 3000);
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
          <div className="flex items-center space-x-4">
            <button
              onClick={() => navigate(-1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ArrowLeft className="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h1 className="text-lg font-bold text-gray-800">Payment</h1>
              <p className="text-gray-600 text-sm">Complete your booking</p>
            </div>
          </div>
        </motion.div>

        {/* Booking Summary */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.2 }}
          className="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 mb-6 border border-blue-100"
        >
          <h2 className="text-lg font-bold text-gray-800 mb-4">Booking Summary</h2>
          
          <div className="space-y-3">
            <div className="flex justify-between">
              <span className="text-gray-600">Operator:</span>
              <span className="font-semibold text-gray-800">{bus?.operator}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-600">Route:</span>
              <span className="font-semibold text-gray-800">{from} → {to}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-600">Date:</span>
              <span className="font-semibold text-gray-800">{date}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-600">Seats:</span>
              <span className="font-semibold text-gray-800">{selectedSeats?.join(', ')}</span>
            </div>
            <div className="border-t border-gray-200 pt-3 flex justify-between">
              <span className="text-lg font-bold text-gray-800">Total:</span>
              <span className="text-2xl font-bold text-blue-600">
                UGX {totalFare?.toLocaleString()}
              </span>
            </div>
          </div>
        </motion.div>

        {/* Payment Method */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.3 }}
          className="bg-white rounded-2xl p-6 mb-6 shadow-lg"
        >
          <h2 className="text-lg font-bold text-gray-800 mb-4">Payment Method</h2>
          
          <div className="space-y-3 mb-6">
            <div
              className={`flex items-center space-x-3 p-4 rounded-xl border-2 cursor-pointer transition-colors ${
                paymentMethod === 'mobilemoney' 
                  ? 'border-blue-500 bg-blue-50' 
                  : 'border-gray-200 hover:border-gray-300'
              }`}
              onClick={() => setPaymentMethod('mobilemoney')}
            >
              <Phone className="w-6 h-6 text-green-600" />
              <div className="flex-1">
                <h3 className="font-semibold text-gray-800">Mobile Money</h3>
                <p className="text-gray-600 text-sm">MTN/Airtel Money</p>
              </div>
              <div className={`w-4 h-4 rounded-full border-2 ${
                paymentMethod === 'mobilemoney' 
                  ? 'border-blue-500 bg-blue-500' 
                  : 'border-gray-300'
              }`} />
            </div>

            <div
              className={`flex items-center space-x-3 p-4 rounded-xl border-2 cursor-pointer transition-colors ${
                paymentMethod === 'card' 
                  ? 'border-blue-500 bg-blue-50' 
                  : 'border-gray-200 hover:border-gray-300'
              }`}
              onClick={() => setPaymentMethod('card')}
            >
              <CreditCard className="w-6 h-6 text-blue-600" />
              <div className="flex-1">
                <h3 className="font-semibold text-gray-800">Credit/Debit Card</h3>
                <p className="text-gray-600 text-sm">Visa, MasterCard</p>
              </div>
              <div className={`w-4 h-4 rounded-full border-2 ${
                paymentMethod === 'card' 
                  ? 'border-blue-500 bg-blue-500' 
                  : 'border-gray-300'
              }`} />
            </div>
          </div>

          {/* Payment Details */}
          {paymentMethod === 'mobilemoney' && (
            <div className="space-y-4">
              <div>
                <label className="block text-gray-700 text-sm font-semibold mb-2">
                  Mobile Money Number
                </label>
                <input
                  type="tel"
                  placeholder="e.g., 0701234567"
                  value={phoneNumber}
                  onChange={(e) => setPhoneNumber(e.target.value)}
                  className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
          )}
        </motion.div>

        {/* Security Notice */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.4 }}
          className="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6"
        >
          <div className="flex items-center space-x-3">
            <Shield className="w-6 h-6 text-green-600" />
            <div>
              <h3 className="font-semibold text-green-800">Secure Payment</h3>
              <p className="text-green-600 text-sm">Your payment is protected with 256-bit SSL encryption</p>
            </div>
          </div>
        </motion.div>

        {/* Payment Button */}
        <motion.button
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.5 }}
          whileHover={{ scale: loading ? 1 : 1.02 }}
          whileTap={{ scale: loading ? 1 : 0.98 }}
          onClick={handlePayment}
          disabled={loading || !phoneNumber}
          className="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-4 rounded-2xl font-semibold text-lg shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {loading ? (
            <div className="flex items-center justify-center space-x-2">
              <motion.div
                animate={{ rotate: 360 }}
                transition={{ duration: 1, repeat: Infinity, ease: "linear" }}
                className="w-5 h-5 border-2 border-white border-t-transparent rounded-full"
              />
              <span>Processing Payment...</span>
            </div>
          ) : (
            'Confirm & Pay'
          )}
        </motion.button>
      </div>
    </motion.div>
  );
};

export default Payment;