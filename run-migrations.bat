@echo off
echo Running migrations and seeders...
php artisan migrate:fresh --seed
pause
