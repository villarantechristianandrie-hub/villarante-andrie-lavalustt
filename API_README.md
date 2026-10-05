# Product Management API (LavaLust)

## 1. Local setup
1. `cp .env.example .env` and fill in your Aiven credentials.
2. Generate secrets and paste them into `.env`:
   `php -r "echo bin2hex(random_bytes(32));"`  (run twice: `JWT_SECRET`, `REFRESH_TOKEN_KEY`)
3. Run the migrations (creates `migrations`, `users`, `refresh_tokens`, `products`):
   `php lava migration run`
4. Start the server: `php lava serve` (default http://localhost:8080)

## 2. Migration commands (Database Migration activity)
| Command | What it does |
|---|---|
| `php lava migration run` | run pending migrations |
| `php lava migration status` | show migration status |
| `php lava migration create-migration NAME` | create a migration file |
| `php lava migration rollback` | roll back latest migration |
| `php lava migration rollback-all` / `refresh` | dev database only! |

Browser routes (`/migrate`, `/status`, ...) also work locally, but are blocked when `APP_ENV=production`.

## 3. Endpoints
| Method | URL | Auth | Body |
|---|---|---|---|
| POST | /api/register | - | username, email, password |
| POST | /api/login | - | username, password |
| POST | /api/refresh | - | refresh_token |
| POST | /api/logout | - | refresh_token |
| GET | /api/me | JWT | |
| GET | /api/products | JWT | |
| GET | /api/products/{id} | JWT | |
| POST | /api/products | JWT | product_name, description, price, quantity |
| PUT / PATCH | /api/products/{id} | JWT | same (PATCH = partial) |
| DELETE | /api/products/{id} | JWT | |

Send `Authorization: Bearer <access_token>` on protected routes. Test with https://api-tester.marasigan.dev/

## 4. Deploy to Render
1. Push this folder to GitHub (`.env` is git-ignored).
2. Render > New > Web Service > connect repo > **Runtime: Docker** (uses the included `Dockerfile`).
3. Environment variables: `APP_ENV=production`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`,
   `JWT_SECRET`, `REFRESH_TOKEN_KEY`, `ALLOWED_ORIGIN` (your frontend URL), `PORT=80`.
4. Create the tables once, from your own computer, with your `.env` pointing at Aiven: `php lava migration run`
   (or from the Render Shell tab of the service).
5. In Aiven, allow Render's IPs (or 0.0.0.0/0 for the lab) under *Allowed IP addresses*.
