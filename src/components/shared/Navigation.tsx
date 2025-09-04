import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Bus, User, Settings } from 'lucide-react';

const Navigation: React.FC = () => {
  const location = useLocation();
  const isPassenger = location.pathname.includes('/passenger');
  const isAgent = location.pathname.includes('/agent');
  const isAdmin = location.pathname.includes('/admin');

  if (location.pathname === '/' || location.pathname.includes('/splash') || location.pathname.includes('/login')) {
    return null;
  }

  return (
    <motion.nav 
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      className="fixed top-0 left-0 right-0 z-50 bg-white/10 backdrop-blur-lg border-b border-white/20"
    >
      <div className="container mx-auto px-4 py-3 flex items-center justify-between">
        <Link to="/" className="flex items-center space-x-2">
          <Bus className="w-8 h-8 text-white" />
          <span className="text-white font-bold text-xl">MOBUS</span>
        </Link>
        
        <div className="flex items-center space-x-4">
          <Link
            to="/passenger/home"
            className={`flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors ${
              isPassenger ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'
            }`}
          >
            <User className="w-4 h-4" />
            <span>Passenger</span>
          </Link>
          
          <Link
            to="/agent"
            className={`flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors ${
              isAgent ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'
            }`}
          >
            <Bus className="w-4 h-4" />
            <span>Agent</span>
          </Link>
          
          <Link
            to="/admin"
            className={`flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors ${
              isAdmin ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'
            }`}
          >
            <Settings className="w-4 h-4" />
            <span>Admin</span>
          </Link>
        </div>
      </div>
    </motion.nav>
  );
};

export default Navigation;