FROM node:20-alpine as builder

# Create app directory
WORKDIR /usr/src/app

# Install deps (including dev for build)
COPY package*.json ./
RUN npm ci

# Copy source and build
COPY . .
RUN npm run build

FROM node:20-alpine as runner
WORKDIR /usr/src/app
COPY --from=builder /usr/src/app/package*.json ./
COPY --from=builder /usr/src/app/dist ./dist
RUN npm ci --only=production

EXPOSE 4000
CMD ["node", "dist/index.js"]
