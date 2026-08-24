# Micro Travel — Laravel + Filament

This is the Laravel edition of the Micro Travel Maldives website. It keeps the premium public design and adds a practical administration system.

## Included

- Responsive Micro Travel homepage
- Resort, guesthouse and liveaboard listings
- Property detail pages and filters
- Multiple property images and amenities
- Starting prices, islands, atolls and publishing controls
- Travel journal/blog with rich-text editing
- Customer inquiry database
- WhatsApp actions connected to `9996210`
- Filament admin panel at `/admin`
- SEO title and description fields
- Sample content seeder

## Requirements

- PHP 8.2 or newer
- Composer 2
- MySQL 8+ or MariaDB 10.6+
- PHP extensions commonly required by Laravel: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer and XML

## Installation

```bash
unzip micro-travel-laravel.zip
cd micro-travel-laravel
composer install
cp .env.example .env
php artisan key:generate
```

Create a database named `micro_travel`, then update these values in `.env`:

```env
DB_DATABASE=micro_travel
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
APP_URL=http://micro-travel.test
MICRO_TRAVEL_WHATSAPP=9609996210
```

Finish the setup:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

Open the website at `http://127.0.0.1:8000` and the backend at `http://127.0.0.1:8000/admin`.

## Initial administrator

- Email: `admin@microtravel.mv`
- Password: `ChangeMe123!`

**Change this password immediately after the first login.** For production, it is better to remove the seeded password and create the administrator interactively:

```bash
php artisan make:filament-user
```

## Admin workflow

### Accommodation

Go to **Travel Products → Accommodations**. Select Resort, Guesthouse or Liveaboard; upload images; enter the location, price, amenities and description; then enable **Published**. Enable **Featured** to show it on the homepage.

### Blog

Go to **Content → Posts**. Add the title, category, cover image, excerpt and article. Set a publish date and enable **Published**.

### Customer enquiries

Go to **Customers → Travel inquiries**. Update each lead from New to Contacted, Quoted, Confirmed or Closed.

## Production notes

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Point the domain document root to the project's `/public` directory.
- Configure HTTPS and automatic database backups.
- Run `php artisan storage:link` after deployment.
- Use a queue worker if email notifications are added later.
- The included demo photographs use remote Unsplash URLs. Replace them through the admin panel before launch.
- For DigitalOcean managed MySQL, use the database hostname, add the Droplet or VPC as a trusted source, and configure SSL options in `.env`.
- A deployment checklist for this project is available in `DEPLOY_DIGITALOCEAN.md`.

## Recommended next additions

1. Room/villa types and seasonal rate tables
2. Package builder with transfers and meal plans
3. Email notifications for new inquiries
4. Quotations and PDF itineraries
5. Availability/API connections to resort suppliers
6. User roles for administrators, sales staff and content editors

The current system is an enquiry-based travel website, not an instant-booking engine. Real-time room availability, payment capture and supplier confirmation require separate integrations.
