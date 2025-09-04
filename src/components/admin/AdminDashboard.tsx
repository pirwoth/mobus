import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Users, Bus, BarChart3, Settings, UserCheck, Calendar, AlertTriangle, TrendingUp } from 'lucide-react';

const AdminDashboard: React.FC = () => {
  const [activeTab, setActiveTab] = useState('overview');

  const stats = {
    totalUsers: 2547,
    totalTrips: 148,
    totalRevenue: 15420000,
    activeUsers: 342
  };

  const sidebarItems = [
    { id: 'overview', label: 'Overview', icon: BarChart3 },
    { id: 'users', label: 'User Management', icon: Users },
    { id: 'trips', label: 'Trip Management', icon: Bus },
    { id: 'operators', label: 'Bus Operators', icon: UserCheck },
    { id: 'reports', label: 'Reports', icon: TrendingUp },
    { id: 'settings', label: 'Settings', icon: Settings }
  ];

  const recentBookings = [
    { id: 'MB1703847291', passenger: 'John Smith', route: 'Kampala → Mbarara', amount: 25000, status: 'confirmed' },
    { id: 'MB1703847292', passenger: 'Mary Nakato', route: 'Jinja → Kampala', amount: 18000, status: 'confirmed' },
    { id: 'MB1703847293', passenger: 'David Musoke', route: 'Mbarara → Kampala', amount: 25000, status: 'pending' }
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'overview':
        return (
          <div className="space-y-6">
            {/* Stats Grid */}
            <div className="grid grid-cols-2 gap-4">
              <div className="bg-white rounded-2xl p-5 shadow-lg">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-gray-600 text-sm">Total Users</h3>
                  <Users className="w-5 h-5 text-blue-500" />
                </div>
                <p className="text-2xl font-bold text-gray-800">{stats.totalUsers.toLocaleString()}</p>
                <p className="text-green-600 text-sm">↗ +12% this month</p>
              </div>

              <div className="bg-white rounded-2xl p-5 shadow-lg">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-gray-600 text-sm">Active Trips</h3>
                  <Bus className="w-5 h-5 text-purple-500" />
                </div>
                <p className="text-2xl font-bold text-gray-800">{stats.totalTrips}</p>
                <p className="text-green-600 text-sm">↗ +8% this week</p>
              </div>

              <div className="bg-white rounded-2xl p-5 shadow-lg">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-gray-600 text-sm">Revenue</h3>
                  <TrendingUp className="w-5 h-5 text-green-500" />
                </div>
                <p className="text-2xl font-bold text-gray-800">UGX {(stats.totalRevenue / 1000000).toFixed(1)}M</p>
                <p className="text-green-600 text-sm">↗ +15% this month</p>
              </div>

              <div className="bg-white rounded-2xl p-5 shadow-lg">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-gray-600 text-sm">Active Now</h3>
                  <UserCheck className="w-5 h-5 text-cyan-500" />
                </div>
                <p className="text-2xl font-bold text-gray-800">{stats.activeUsers}</p>
                <p className="text-blue-600 text-sm">Users online</p>
              </div>
            </div>

            {/* Recent Bookings */}
            <div className="bg-white rounded-2xl p-5 shadow-lg">
              <h3 className="text-lg font-bold text-gray-800 mb-4">Recent Bookings</h3>
              <div className="space-y-3">
                {recentBookings.map(booking => (
                  <div key={booking.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                      <p className="font-medium text-gray-800">{booking.passenger}</p>
                      <p className="text-gray-600 text-sm">{booking.route}</p>
                    </div>
                    <div className="text-right">
                      <p className="font-bold text-gray-800">UGX {booking.amount.toLocaleString()}</p>
                      <span className={`px-2 py-1 rounded-full text-xs ${
                        booking.status === 'confirmed' 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {booking.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        );

      case 'users':
        return (
          <div className="bg-white rounded-2xl p-5 shadow-lg">
            <h2 className="text-xl font-bold text-gray-800 mb-4">User Management</h2>
            <div className="grid grid-cols-1 gap-4">
              <div className="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h3 className="font-semibold text-blue-800 mb-2">Passenger Accounts</h3>
                <p className="text-blue-600 text-sm">Manage passenger registrations and profiles</p>
                <button className="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                  View Users
                </button>
              </div>
              <div className="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                <h3 className="font-semibold text-purple-800 mb-2">Agent Accounts</h3>
                <p className="text-purple-600 text-sm">Manage conductor and agent accounts</p>
                <button className="mt-2 bg-purple-600 text-white px-4 py-2 rounded-lg text-sm">
                  View Agents
                </button>
              </div>
            </div>
          </div>
        );

      case 'trips':
        return (
          <div className="bg-white rounded-2xl p-5 shadow-lg">
            <h2 className="text-xl font-bold text-gray-800 mb-4">Trip Management</h2>
            <div className="space-y-4">
              <div className="p-4 bg-green-50 border border-green-200 rounded-xl">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-semibold text-green-800">Active Trips</h3>
                    <p className="text-green-600 text-sm">12 trips currently running</p>
                  </div>
                  <Calendar className="w-8 h-8 text-green-600" />
                </div>
              </div>
              <div className="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-semibold text-orange-800">Scheduled Trips</h3>
                    <p className="text-orange-600 text-sm">48 trips scheduled for today</p>
                  </div>
                  <AlertTriangle className="w-8 h-8 text-orange-600" />
                </div>
              </div>
            </div>
          </div>
        );

      default:
        return (
          <div className="bg-white rounded-2xl p-5 shadow-lg">
            <h2 className="text-xl font-bold text-gray-800 mb-4">
              {sidebarItems.find(item => item.id === activeTab)?.label}
            </h2>
            <p className="text-gray-600">This section is under development.</p>
          </div>
        );
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -20 }}
      className="min-h-screen pt-20 px-4"
    >
      <div className="max-w-6xl mx-auto flex gap-6">
        {/* Sidebar */}
        <motion.div
          initial={{ x: -50, opacity: 0 }}
          animate={{ x: 0, opacity: 1 }}
          transition={{ delay: 0.1 }}
          className="w-64 bg-white/20 backdrop-blur-lg border border-white/20 rounded-2xl p-4 h-fit"
        >
          <div className="mb-6">
            <h1 className="text-white text-xl font-bold mb-2">Admin Dashboard</h1>
            <p className="text-white/70 text-sm">Welcome, Administrator</p>
          </div>

          <nav className="space-y-2">
            {sidebarItems.map(item => {
              const Icon = item.icon;
              return (
                <button
                  key={item.id}
                  onClick={() => setActiveTab(item.id)}
                  className={`w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-left transition-colors ${
                    activeTab === item.id 
                      ? 'bg-white/20 text-white' 
                      : 'text-white/70 hover:bg-white/10 hover:text-white'
                  }`}
                >
                  <Icon className="w-5 h-5" />
                  <span className="text-sm font-medium">{item.label}</span>
                </button>
              );
            })}
          </nav>
        </motion.div>

        {/* Main Content */}
        <motion.div
          key={activeTab}
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.2 }}
          className="flex-1"
        >
          {renderContent()}
        </motion.div>
      </div>
    </motion.div>
  );
};

export default AdminDashboard;