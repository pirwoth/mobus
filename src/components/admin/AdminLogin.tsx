import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { Settings, Lock, LogIn } from 'lucide-react';

const AdminLogin: React.FC = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (email && password) {
      navigate('/admin/dashboard');
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -20 }}
      className="min-h-screen pt-20 px-4 flex items-center justify-center"
    >
      <div className="max-w-md mx-auto w-full">
        <motion.div
          initial={{ scale: 0.9 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.2 }}
          className="bg-white rounded-3xl p-8 shadow-2xl"
        >
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-gradient-to-r from-gray-700 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
              <Settings className="w-8 h-8 text-white" />
            </div>
            <h1 className="text-2xl font-bold text-gray-800 mb-2">Admin Login</h1>
            <p className="text-gray-600">Access the management dashboard</p>
          </div>

          <form onSubmit={handleLogin} className="space-y-6">
            <div>
              <label className="block text-gray-700 text-sm font-semibold mb-2">
                Username/Email
              </label>
              <div className="relative">
                <Settings className="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                <input
                  type="text"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Enter your username or email"
                  required
                />
              </div>
            </div>

            <div>
              <label className="block text-gray-700 text-sm font-semibold mb-2">
                Password
              </label>
              <div className="relative">
                <Lock className="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                <input
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Enter your password"
                  required
                />
              </div>
            </div>

            <motion.button
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              type="submit"
              className="w-full bg-gradient-to-r from-gray-700 to-purple-700 text-white py-3 rounded-xl font-semibold flex items-center justify-center space-x-2 shadow-lg"
            >
              <LogIn className="w-5 h-5" />
              <span>Login</span>
            </motion.button>
          </form>

          <div className="mt-6 text-center">
            <a href="#" className="text-purple-600 hover:text-purple-800 text-sm">
              Forgot your password?
            </a>
          </div>
        </motion.div>
      </div>
    </motion.div>
  );
};

export default AdminLogin;