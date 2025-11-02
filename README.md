# Web-trafficker
Website traffic tracker that logs and displays unique visits to a web page

## Quick start
1) cp .env.example .env && edit DB/hostnames
2) composer install && npm i
3) php artisan key:generate
4) php artisan migrate --seed
5) php artisan serve (or Herd) + npm run dev
6) Open /admin
