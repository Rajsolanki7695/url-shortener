# URL Shortener

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite

## Local Setup

1. **Clone the repository and navigate to the project directory:**

    ```bash
    git clone <repository-url>
    cd url-shortener
    ```

2. **Install dependencies:**

    ```bash
    composer install
    npm install
    ```

3. **Configure the environment file:**

    ```bash
    cp .env.example .env
    ```

    Configure your database credentials in `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=url-shortener
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Generate application key:**

    ```bash
    php artisan key:generate
    ```

5. **Run migrations and database seeder:**

    ```bash
    php artisan migrate --seed
    ```

    **Default SuperAdmin Account:**
    - **Email:** `superadmin@gmail.com`
    - **Password:** `12345678`

6. **Compile frontend assets:**

    ```bash
    npm run build
    ```

7. **Start local development server:**
    ```bash
    php artisan serve
    ```
    Access the application at `http://127.0.0.1:8000`.

## Testing

Execute the automated test suites using Artisan:

```bash
# Run the full test suite
php artisan test

# Run short URL constraint tests
php artisan test --filter=ShortUrlConstraintTest

# Run invitation constraint tests
php artisan test --filter=InvitationConstraintTest
```

## AI Tools Used: ChatGPT

I used ChatGPT only to assist with creating the HTML code and Laravel migration file code. The AI assistance was limited to generating the initial code structure and syntax. The overall workflow, implementation decisions, and reasoning were my own.
