import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useLocation, useNavigate } from 'react-router-dom';
import { ArrowLeft, QrCode, Users, MapPin, Clock, CheckCircle, AlertCircle } from 'lucide-react';

interface Passenger {
  id: string;
  name: string;
  seat: string;
  bookingId: string;
  boarded: boolean;
  boardingStop: string;
}

const TripManagement: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { trip } = location.state || {};
  
  const [passengers] = useState<Passenger[]>([
    {
      id: '1',
      name: 'John Smith',
      seat: '12A',
      bookingId: 'MB1703847291',
      boarded: true,
      boardingStop: 'Kampala'
    },
    {
      id: '2',
      name: 'Mary Nakato',
      seat: '8B',
      bookingId: 'MB1703847292',
      boarded: true,
      boardingStop: 'Kampala'
    },
    {
      id: '3',
      name: 'David Musoke',
      seat: '15C',
      bookingId: 'MB1703847293',
      boarded: false,
      boardingStop: 'Masaka'
    },
    {
      id: '4',
      name: 'Grace Namusoke',
      seat: '20A',
      bookingId: 'MB1703847294',
      boarded: false,
      boardingStop: 'Masaka'
    }
  ]);

  const [showScanner, setShowScanner] = useState(false);
  const [scanMessage, setScanMessage] = useState('');

  const nextStop = 'Masaka';
  const passengersToBoard = passengers.filter(p => p.boardingStop === nextStop && !p.boarded);
  const boardedPassengers = passengers.filter(p => p.boarded);

  const handleScanQR = () => {
    setShowScanner(true);
    setTimeout(() => {
      setShowScanner(false);
      setScanMessage('Passenger verified successfully!');
      setTimeout(() => setScanMessage(''), 3000);
    }, 2000);
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
          <div className="flex items-center space-x-4 mb-4">
            <button
              onClick={() => navigate('/agent/dashboard')}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ArrowLeft className="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h1 className="text-lg font-bold text-gray-800">Trip Management</h1>
              <p className="text-gray-600 text-sm">{trip?.route}</p>
            </div>
          </div>
          
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="text-gray-600 text-sm">Bus</p>
              <p className="font-semibold text-gray-800">{trip?.busNumber}</p>
            </div>
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="text-gray-600 text-sm">Departure</p>
              <p className="font-semibold text-gray-800">{trip?.departureTime}</p>
            </div>
          </div>
        </motion.div>

        {/* Next Stop Info */}
        <motion.div
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.2 }}
          className="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-2xl p-5 mb-6"
        >
          <div className="flex items-center space-x-3 mb-3">
            <MapPin className="w-6 h-6 text-blue-600" />
            <div>
              <h2 className="font-bold text-gray-800">Next Stop: {nextStop}</h2>
              <p className="text-gray-600 text-sm">Passengers to board: {passengersToBoard.length}</p>
            </div>
          </div>
          
          {passengersToBoard.length > 0 && (
            <div className="space-y-2">
              <h3 className="font-semibold text-gray-800 text-sm">Boarding at {nextStop}:</h3>
              {passengersToBoard.map(passenger => (
                <div key={passenger.id} className="bg-white rounded-lg p-3 flex items-center justify-between">
                  <div>
                    <p className="font-medium text-gray-800">{passenger.name}</p>
                    <p className="text-gray-600 text-sm">Seat {passenger.seat} • {passenger.bookingId}</p>
                  </div>
                  <AlertCircle className="w-5 h-5 text-orange-500" />
                </div>
              ))}
            </div>
          )}
        </motion.div>

        {/* QR Scanner */}
        <motion.div
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.3 }}
          className="bg-white rounded-2xl p-6 mb-6 shadow-lg"
        >
          <h2 className="text-lg font-bold text-gray-800 mb-4 text-center">QR Code Scanner</h2>
          
          {showScanner ? (
            <motion.div
              initial={{ scale: 0.8 }}
              animate={{ scale: 1 }}
              className="text-center"
            >
              <div className="w-40 h-40 bg-gray-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                <motion.div
                  animate={{ rotate: 360 }}
                  transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
                  className="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full"
                />
              </div>
              <p className="text-gray-600">Scanning QR Code...</p>
            </motion.div>
          ) : (
            <div className="text-center">
              <div className="w-40 h-40 bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                <QrCode className="w-20 h-20 text-blue-600" />
              </div>
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                onClick={handleScanQR}
                className="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg"
              >
                Scan QR Code
              </motion.button>
            </div>
          )}
          
          {scanMessage && (
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className="mt-4 p-3 bg-green-100 border border-green-200 rounded-lg text-green-800 text-center"
            >
              {scanMessage}
            </motion.div>
          )}
        </motion.div>

        {/* Boarded Passengers */}
        <motion.div
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.4 }}
          className="bg-white rounded-2xl p-5 shadow-lg"
        >
          <div className="flex items-center space-x-2 mb-4">
            <Users className="w-5 h-5 text-green-600" />
            <h2 className="text-lg font-bold text-gray-800">
              Boarded Passengers ({boardedPassengers.length})
            </h2>
          </div>
          
          <div className="space-y-3">
            {boardedPassengers.map(passenger => (
              <div key={passenger.id} className="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                <div>
                  <p className="font-medium text-gray-800">{passenger.name}</p>
                  <p className="text-gray-600 text-sm">Seat {passenger.seat} • {passenger.bookingId}</p>
                </div>
                <CheckCircle className="w-5 h-5 text-green-600" />
              </div>
            ))}
          </div>
        </motion.div>

        {/* Trip Stats */}
        <motion.div
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.5 }}
          className="mt-6 grid grid-cols-2 gap-4"
        >
          <div className="bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center">
            <Clock className="w-6 h-6 mx-auto mb-2" />
            <p className="text-sm">On Time</p>
          </div>
          <div className="bg-white/20 backdrop-blur-lg border border-white/20 rounded-xl p-4 text-white text-center">
            <Users className="w-6 h-6 mx-auto mb-2" />
            <p className="text-sm">{boardedPassengers.length}/{trip?.maxPassengers} Boarded</p>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default TripManagement;