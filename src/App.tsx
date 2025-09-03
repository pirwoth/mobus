import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';

// Passenger Components
import PassengerSplash from './components/passenger/PassengerSplash';
import PassengerHome from './components/passenger/PassengerHome';
import SearchResults from './components/passenger/SearchResults';
import SeatSelection from './components/passenger/SeatSelection';
import Payment from './components/passenger/Payment';
import DigitalTicket from './components/passenger/DigitalTicket';
import MyBookings from './components/passenger/MyBookings';

// Agent Components
import AgentSplash from './components/agent/AgentSplash';
import AgentLogin from './components/agent/AgentLogin';
import ConductorDashboard from './components/agent/ConductorDashboard';
import TripManagement from './components/agent/TripManagement';

// Admin Components
import AdminSplash from './components/admin/AdminSplash';
import AdminLogin from './components/admin/AdminLogin';
import AdminDashboard from './components/admin/AdminDashboard';

// Shared Components
import Navigation from './components/shared/Navigation';

function App() {
  return (
    <Router>
      <div className="min-h-screen bg-gradient-to-br from-blue-400 via-purple-500 to-cyan-400">
        <Navigation />
        <AnimatePresence mode="wait">
          <Routes>
            {/* Passenger Routes */}
            <Route path="/" element={<PassengerSplash />} />
            <Route path="/passenger/home" element={<PassengerHome />} />
            <Route path="/passenger/search" element={<SearchResults />} />
            <Route path="/passenger/seats" element={<SeatSelection />} />
            <Route path="/passenger/payment" element={<Payment />} />
            <Route path="/passenger/ticket" element={<DigitalTicket />} />
            <Route path="/passenger/bookings" element={<MyBookings />} />
            
            {/* Agent Routes */}
            <Route path="/agent" element={<AgentSplash />} />
            <Route path="/agent/login" element={<AgentLogin />} />
            <Route path="/agent/dashboard" element={<ConductorDashboard />} />
            <Route path="/agent/trip/:tripId" element={<TripManagement />} />
            
            {/* Admin Routes */}
            <Route path="/admin" element={<AdminSplash />} />
            <Route path="/admin/login" element={<AdminLogin />} />
            <Route path="/admin/dashboard" element={<AdminDashboard />} />
          </Routes>
        </AnimatePresence>
      </div>
    </Router>
  );
}

export default App;