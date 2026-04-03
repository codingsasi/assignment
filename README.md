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

1. Clone this repo.
```
git clone https://github.com/codingsasi/assignment.git
```
2. Create the database and tables

Either using docker:
```bash
docker compose up -d
```

Or import `sql/database.sql` in phpMyAdmin.

Admin default password is "password"

3. Run the app from this directory:

```bash
php -S localhost:8080
```

Open `http://localhost:8080/`.

4. **MySQL in Docker (if not using xamp/wamp):**
  

Then run PHP on your machine with the defaults above (`DB_HOST=127.0.0.1`, port `3306`).
