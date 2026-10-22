# Movie Ticket Booking REST API

A production-ready, high-concurrency Movie Ticket Booking REST API built with **Laravel 11 (PHP 8.3)**, **PostgreSQL** (local dev via Docker; production on **NeonDB**), and **Laravel Sanctum** token-based authentication.

---

## Tech Stack & Architecture

- **Framework**: Laravel 11 (API-only architecture, PHP 8.3)
- **Database**: PostgreSQL 16 (Local via Docker Compose; Cloud production on **NeonDB Serverless**)
- **Authentication**: Laravel Sanctum (Stateless Bearer Tokens)
- **Containerization**: Multi-stage Dockerfile (PHP 8.3 Alpine + Nginx + PHP-FPM)
- **Deployment**: Render Web Service (`render.yaml` blueprint with auto-scaling & health checks)
- **Concurrency & Booking Safety**: Row-level locking (`SELECT ... FOR UPDATE`) inside database transactions paired with unique database constraints on `(showtime_id, seat_id)` to guarantee zero double bookings.

---

## Core Domain Models & Relationships

- **User**: Authentication, role enum (`user`, `admin`), soft deletes.
- **Movie**: Catalog of movies, slug, genre array, duration, rating, poster, trailer, status (`now_showing`, `coming_soon`, `archived`).
- **Cinema**: Physical cinema locations and addresses.
- **Screen**: Auditoriums belonging to a cinema, total rows and seats per row, auditorium type (`standard`, `imax`, `atmos`).
- **Seat**: Specific seat locations (`row_label`, `seat_number`, type `standard` / `vip`).
- **Showtime**: Movie screenings per screen with datetime (`starts_at`), base price, and status.
- **Booking**: User booking transaction with unique `reference_code` (`RTXXXXXX`), total amount, status (`pending`, `confirmed`, `cancelled`), and 10-minute expiration for pending orders.
- **BookingSeat**: Pivot linking booking, seat, and showtime with a **strict UNIQUE composite constraint on `(showtime_id, seat_id)`** to reject concurrent race conditions.

---

## Default Seeded Accounts

The database seeder provisions demo accounts, 3 cinemas, screens with full seat layouts, 10 movies, and showtimes:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` |
| **User** | `user@example.com` | `password` |

---

## API Endpoints (Prefix `/api/v1`)

### Health Check
- `GET /api/health` - Production health check for Render / Uptime monitors (checks database connectivity).

### Authentication (`/api/v1/auth`)
- `POST /auth/register` - Register a new customer (`name`, `email`, `password`, `phone`).
- `POST /auth/login` - Login with credentials and receive Bearer token.
- `POST /auth/logout` - Revoke current access token (*Auth required*).
- `GET /auth/me` - Get profile of authenticated user (*Auth required*).

### Public Catalog (`/api/v1`)
- `GET /movies` - Paginated movies list (supports `?search=`, `?genre=`, `?status=now_showing|coming_soon`, `?page=`).
- `GET /movies/{slug}` - Get movie details by slug or ID.
- `GET /movies/{slug}/showtimes` - Get upcoming active showtimes for a movie.
- `GET /showtimes/{id}/seats` - Full seat map of the screen showing taken vs available seats.

### Customer Bookings (`/api/v1/bookings`) - *Auth required*
- `POST /bookings` - Reserve 1 to 8 seats:
  ```json
  {
    "showtime_id": 1,
    "seats": ["A1", "A2"]
  }
  ```
- `GET /bookings` - List authenticated user's bookings.
- `GET /bookings/{id}` - Get booking details and reference code.
- `PATCH /bookings/{id}/cancel` (or `POST /bookings/{id}/cancel`) - Cancel booking up to 1 hour before showtime.

### Admin Operations (`/api/v1/admin`) - *Auth required, Admin role only*
- `GET /admin/stats` - Platform revenue, total tickets sold, and top-performing movies.
- `GET /admin/bookings` - View all customer bookings platform-wide.
- `apiResource /admin/movies` - Full CRUD for movies (`title`, `slug`, `genre`, `duration`, `rating`, `status`, etc.).
- `apiResource /admin/cinemas` - Full CRUD for cinema locations.
- `apiResource /admin/screens` - Full CRUD for screens (auto-provisions seats on creation).
- `apiResource /admin/showtimes` - Full CRUD for showtimes.

---

## Local Development with Docker

### Prerequisites
- Docker & Docker Compose installed.

### 1. Clone & Setup Environment
```bash
cd Backend
cp .env.example .env
```

### 2. Start Services
```bash
docker compose up -d --build
```
This spins up:
- **`app`**: PHP 8.3-FPM with PostgreSQL, Opcache, and Composer
- **`nginx`**: Web server on `http://localhost:8000`
- **`postgres`**: PostgreSQL 16 on `localhost:5432`
- **`pgadmin`**: GUI on `http://localhost:5050` (Email: `admin@example.com`, Password: `adminpassword`)

The entrypoint automatically waits for PostgreSQL, installs Composer packages, generates the `APP_KEY`, runs migrations, and seeds demo data.

### 3. Verify Local API
```bash
curl http://localhost:8000/api/health
```
Response:
```json
{
  "status": "healthy",
  "timestamp": "2026-10-22T05:45:00.000000Z",
  "services": {
    "api": "up",
    "database": "ok"
  }
}
```

---

## Running Automated Tests

Run the full PHPUnit feature test suite inside the container:
```bash
docker compose exec app php artisan test
```

Test coverage includes:
1. `AuthTest`: Registration, login, validation, profile retrieval, logout.
2. `MovieTest`: Public catalog, filters, pagination, showtimes, seat map generation.
3. `BookingTest`: Reservation workflow, max 8 seats validation, past showtime blocking, 1-hour cancellation rule.
4. `DoubleBookingTest`: High-concurrency race condition testing verifying that row-level locking and DB unique constraints reject duplicate seat claims.
5. `AdminTest`: Admin route protection, CRUD operations, dashboard stats.

---

## Production Deployment to Render & NeonDB

### 1. NeonDB PostgreSQL Setup
1. Create a PostgreSQL project at [Neon.tech](https://neon.tech).
2. Neon provides a serverless connection URL formatted like:
   ```text
   postgresql://username:password@ep-cool-dawn-123456.us-east-2.aws.neon.tech/neondb?sslmode=require
   ```
   > **Note on NeonDB**:
   > - Neon requires SSL mode set to `require` (`DB_SSLMODE=require`).
   > - When using connection pooling (recommended for high traffic), use the `-pooler` endpoint provided by Neon.
   > - Laravel automatically parses the `DATABASE_URL` environment variable.

### 2. Deploy to Render via Blueprint (`render.yaml`)
1. Push your repository to GitHub or GitLab.
2. Log into [Render Dashboard](https://dashboard.render.com).
3. Select **New +** -> **Blueprint**.
4. Connect your repository and select the `render.yaml` file located in `Backend/render.yaml`.
5. Provide the following environment variables in the Render settings:
   - `DATABASE_URL`: Your full NeonDB connection string.
   - `FRONTEND_URL`: Your Vercel frontend URL (e.g. `https://movieticket-frontend.vercel.app`).
   - `APP_URL`: Your Render service URL (e.g. `https://movie-ticket-api.onrender.com`).
   - `RUN_SEEDERS`: `true` on first deploy to seed initial movies and admin.
6. Render will automatically build the `Dockerfile` with the `render` target, wire up dynamic `$PORT` routing via Nginx, execute migrations on NeonDB, and begin health checks at `/api/health`.

---

## Connecting the Frontend

In your React / Vite frontend (`Frontend/.env`):
```env
VITE_API_URL=http://localhost:8000/api/v1
```
For production:
```env
VITE_API_URL=https://movie-ticket-api.onrender.com/api/v1
```

