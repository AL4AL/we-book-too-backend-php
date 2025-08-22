# We-Book-Too Backend PHP

## Prerequisites

To run this project, you need the following PHP 8.4 packages:

```bash
sudo apt install zip php8.4 php8.4-cli php8.4-fpm php8.4-common php8.4-mbstring php8.4-mysql php8.4-opcache php8.4-readline php8.4-sqlite3 php8.4-xml
```

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and configure your environment variables
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run database migrations:
   ```bash
   php artisan migrate
   ```
6. Seed the database (optional):
   ```bash
   php artisan db:seed
   ```
