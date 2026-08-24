# Deploying Atolliva Maldives on a DigitalOcean Droplet

This guide is tailored to this Laravel 12 + Filament project and a DigitalOcean Managed MySQL database.

## 1. Before you start

- Create a Droplet in the same region as your managed database.
- Put the Droplet in the same VPC as the database if possible.
- In the managed database firewall, add the Droplet or the VPC CIDR as a trusted source.
- Point your domain to the Droplet IP.

Managed database connection details for this project:

- Host: `your-managed-db-host`
- Port: `25060`
- Database: `your_database_name`
- Username: `your_database_user`
- SSL mode: `require`

Do not commit your live database password into Git. Set it only on the server in `.env`.

## 2. Install the server stack

```bash
ssh root@your_droplet_ip

apt update && apt upgrade -y
apt install -y nginx git unzip curl mysql-client certbot python3-certbot-nginx \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-mbstring php8.3-xml \
  php8.3-bcmath php8.3-zip php8.3-intl php8.3-gd

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

## 3. Download the app

```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/Rannamaari/atollivamaldives.git atolliva
cd /var/www/atolliva
composer install --no-dev --optimize-autoloader
cp .env.production.example .env
php artisan key:generate
```

## 4. Configure `.env`

Edit `.env` and set at least:

```env
APP_URL=https://your-domain.com
DB_PASSWORD=your_real_managed_db_password
MICRO_TRAVEL_WHATSAPP=960xxxxxxxx
```

For stronger TLS verification with the managed database, download the CA certificate from the DigitalOcean database control panel and place it here:

```bash
mkdir -p /var/www/atolliva/storage/certs
```

Then set:

```env
DB_SSL_CA=/var/www/atolliva/storage/certs/digitalocean-managed-ca.pem
DB_SSL_VERIFY_SERVER_CERT=true
```

## 5. Prepare Laravel

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

This project seeds an initial admin user:

- Email: `admin@microtravel.mv`
- Password: `ChangeMe123!`

Change that password immediately after first login, or create a replacement:

```bash
php artisan make:filament-user
```

## 6. Set permissions

```bash
chown -R www-data:www-data /var/www/atolliva
find /var/www/atolliva -type f -exec chmod 644 {} \;
find /var/www/atolliva -type d -exec chmod 755 {} \;
chmod -R ug+rwx /var/www/atolliva/storage /var/www/atolliva/bootstrap/cache
```

## 7. Configure Nginx

Create `/etc/nginx/sites-available/atolliva`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;

    root /var/www/atolliva/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt { access_log off; log_not_found off; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
ln -s /etc/nginx/sites-available/atolliva /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

## 8. Enable HTTPS

```bash
certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 9. Updating the site later

```bash
cd /var/www/atolliva
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
systemctl reload php8.3-fpm
systemctl reload nginx
```

## 10. Recommended post-launch checks

- Visit `/`
- Visit `/blog`
- Visit `/faq`
- Visit `/liveaboards`
- Visit `/admin`
- Submit a test inquiry
- Confirm uploaded images load from `/storage`
- Confirm the managed DB is reachable after adding the trusted source

Because the live database password has been shared in chat, rotate it in DigitalOcean after deployment and update the Droplet `.env`.
