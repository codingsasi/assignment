# Online Second-Hand Marketplace Platform

Full-stack marketplace: user registration, listings by category, shopping cart, checkout, order history, and an admin area for listings, orders.

**Student:** Abhai Sasidharan  
**Course:** COSC 2956 W02  
**Student ID:** 5147016
## Tech Stack

- HTML5, Bootstrap 5, JavaScript
- PHP 8.x, MySQL 8
- Prepared statements (`PDO`), `password_hash` / `password_verify`

## Setup

1. Create the database and tables:

```bash
mysql -u root -p < sql/schema.sql
```

Or import `sql/database.sql` in phpMyAdmin.

2. Configure the database connection via environment variables (optional). Defaults:

- `DB_HOST` `127.0.0.1`
- `DB_PORT` `3306`
- `DB_NAME` `marketplace`
- `DB_USER` `root`
- `DB_PASS` (empty)

3. Run the app from this directory:

```bash
php -S localhost:8080
```

Open `http://localhost:8080/`.

4. **MySQL in Docker (if not using xamp/wamp):**
  
```bash
docker compose up -d
```

Then run PHP on your machine with the defaults above (`DB_HOST=127.0.0.1`, port `3306`).
