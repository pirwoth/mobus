import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import pool from './db.js'; // Import the pool to test connection
import authRoutes from './routes/auth.js';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

// Test DB connection
pool.query('SELECT NOW()')
  .then(() => console.log('PostgreSQL connected'))
  .catch(err => console.error('PostgreSQL connection error:', err));

// Routes
app.use('/api/auth', authRoutes);

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
