# wdt-api-assessment-marketplace-docker-app

## Usage

Installing dependencies:
- `docker run --rm -v $(pwd):/app composer install`

Start docker image:
- `docker-compose up -d`

Run artisan commands:
- `docker-compose exec app php artisan key:generate`
- `docker-compose exec app php artisan migrate`

(Optional) Set permissions on the project directory so that it is owned by your non-root user:
- `sudo chown -R $USER:$USER ./`
