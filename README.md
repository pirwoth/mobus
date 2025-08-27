# Mobus - Dockerized setup

This repo contains a minimal Docker setup for a full-stack app: backend (Node/Express + PostgreSQL) and frontend (Vite + React served by nginx).

Quick start

1. Copy environment variables:

   cp .env.example .env
   cp backend/.env.example backend/.env

2. Build and start containers:

   docker-compose up --build

Backend
- Runs on port 4000 inside container and exposed to host 4000.
- Connects to Postgres via connection string from `DATABASE_URL`.

Frontend
- Built via Vite and served by nginx on port 3000.

Notes
- This is a starting scaffold. Install actual app code and DB migrations as needed.
