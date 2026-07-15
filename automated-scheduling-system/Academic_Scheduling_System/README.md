# Academic Scheduling System

A web-based academic scheduling system built with Laravel 12, designed to automate course and exam timetable creation while enforcing institutional rules and avoiding resource conflicts.

## Key Features

- Automated class and exam scheduling
- Conflict detection for rooms, instructors, and student groups
- Role-based access control for admin, faculty, and students
- Department-aware data scoping
- CSV import support for schedules and official records
- Secure Laravel authentication and form protection
- Responsive UI built with Tailwind CSS and Vite

## Technology Stack

- PHP 8.2+ and Laravel 12
- MySQL / MariaDB compatible database
- Tailwind CSS, Vite, and Axios for front-end assets
- Composer for PHP dependencies
- NPM for frontend tooling

## Repository Structure

- `app/` - Application controllers, models, providers, and scheduling services
- `bootstrap/` - Laravel bootstrap files and cached configuration
- `config/` - Application configuration files
- `database/` - Migrations, seeders, and SQL backups
- `public/` - Public web entry point and assets
- `resources/` - CSS, JavaScript, and Blade views
- `routes/` - HTTP route definitions
- `storage/` - Logs, cache, sessions, and compiled files
- `tests/` - PHPUnit tests and application test cases

## Installation

1. Clone the repository:

   ```bash
   git clone <repository-url>
   cd Academic_Scheduling_System
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install frontend dependencies:

   ```bash
   npm install
   ```

4. Copy the environment file and configure database credentials:

   ```bash
   copy .env.example .env
   ```

   Then update `.env` with your database settings.

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Run database migrations:

   ```bash
   php artisan migrate
   ```

7. Build frontend assets:

   ```bash
   npm run build
   ```

## Running the Application

To start the application locally:

```bash
php artisan serve
npm run dev
```

Open the site in your browser at `http://127.0.0.1:8000`.

## Available Scripts

- `composer setup` - install PHP dependencies, generate app key, migrate database, install npm packages, and build assets
- `composer test` - run PHPUnit tests
- `npm run dev` - start Vite development server
- `npm run build` - build production frontend assets

## Testing

Run the project's test suite with:

```bash
composer test
```

## Documentation

Additional project documentation is available in `Academic_Scheduling_System_Documentation.md`.

## Notes

- Use the `database/restore_backup.sql` file if you need to restore a saved SQL backup.
- `composer.json` is configured for Laravel 12 and PHP 8.2.
- `package.json` includes Tailwind CSS and Vite for the frontend asset pipeline.

## License

This project is licensed under the MIT License.
