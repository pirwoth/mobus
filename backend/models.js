const mongoose = require('mongoose');

// Passenger Schema
const passengerSchema = new mongoose.Schema({
    name: { type: String, required: true },
    email: { type: String, required: true, unique: true },
    phone: { type: String, required: true },
});

// Agent Schema
const agentSchema = new mongoose.Schema({
    name: { type: String, required: true },
    agencyName: { type: String, required: true },
    phone: { type: String, required: true },
});

// Admin Schema
const adminSchema = new mongoose.Schema({
    username: { type: String, required: true, unique: true },
    password: { type: String, required: true },
});

// Bus Schema
const busSchema = new mongoose.Schema({
    busNumber: { type: String, required: true },
    capacity: { type: Number, required: true },
    route: { type: String, required: true },
});

// Trip Schema
const tripSchema = new mongoose.Schema({
    bus: { type: mongoose.Schema.Types.ObjectId, ref: 'Bus', required: true },
    startLocation: { type: String, required: true },
    endLocation: { type: String, required: true },
    departureTime: { type: Date, required: true },
});

// Booking Schema
const bookingSchema = new mongoose.Schema({
    passenger: { type: mongoose.Schema.Types.ObjectId, ref: 'Passenger', required: true },
    trip: { type: mongoose.Schema.Types.ObjectId, ref: 'Trip', required: true },
    bookingDate: { type: Date, default: Date.now },
});

// Models
const Passenger = mongoose.model('Passenger', passengerSchema);
const Agent = mongoose.model('Agent', agentSchema);
const Admin = mongoose.model('Admin', adminSchema);
const Bus = mongoose.model('Bus', busSchema);
const Trip = mongoose.model('Trip', tripSchema);
const Booking = mongoose.model('Booking', bookingSchema);

module.exports = { Passenger, Agent, Admin, Bus, Trip, Booking };