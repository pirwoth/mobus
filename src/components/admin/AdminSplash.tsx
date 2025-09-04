import React, { useEffect } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { Bus, Settings } from 'lucide-react';

const AdminSplash: React.FC = () => {
  const navigate = useNavigate();

  useEffect(() => {
    const timer = setTimeout(() => {
      navigate('/admin/login');
    }, 3000);

    return () => clearTimeout(timer);
  }, [navigate]);

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="min-h-screen flex items-center justify-center relative overflow-hidden"
    >
      <div className="absolute inset-0 bg-gradient-to-br from-gray-800 via-purple-800 to-blue-900" />
      
      <div className="relative z-10 text-center">
        <motion.div
          initial={{ scale: 0 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.5, type: "spring", stiffness: 200 }}
          className="mb-8"
        >
          <div className="relative">
            <Bus className="w-24 h-24 text-white mx-auto mb-4" />
            <motion.div
              animate={{ scale: [1, 1.2, 1] }}
              transition={{ duration: 2, repeat: Infinity }}
              className="absolute -top-2 -right-2"
            >
              <Settings className="w-6 h-6 text-yellow-300 fill-current" />
            </motion.div>
          </div>
        </motion.div>
        
        <motion.h1
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 1 }}
          className="text-6xl font-bold text-white mb-4"
        >
          MOBUS
        </motion.h1>
        
        <motion.p
          initial={{ y: 30, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 1.5 }}
          className="text-white/80 text-xl mb-2"
        >
          Admin Portal
        </motion.p>
        
        <motion.p
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 1.7 }}
          className="text-white/60 text-sm"
        >
          Management Dashboard
        </motion.p>
      </div>
    </motion.div>
  );
};

export default AdminSplash;