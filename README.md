#  PHP_Laravel12_Octane

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Octane-Enabled-orange" alt="Octane">
  <img src="https://img.shields.io/badge/Server-FrankenPHP-blue" alt="FrankenPHP">
  <img src="https://img.shields.io/badge/Database-MySQL-blueviolet" alt="MySQL">
  <img src="https://img.shields.io/badge/Environment-WSL-green" alt="WSL">
</p>


---

##  Overview

This project demonstrates how to set up a **high-performance Laravel 12 development environment** using modern tools designed for speed and scalability. Instead of the traditional PHP request lifecycle, this setup uses **Laravel Octane with FrankenPHP**, which keeps the application in memory for blazing-fast performance.

The environment runs entirely inside **WSL (Ubuntu)** on Windows, giving you a Linux-based development stack that closely mirrors production behavior.

---

##  Features

*  Laravel Octane for high-performance request handling
*  FrankenPHP application server
*  MySQL running inside WSL
*  OPcache enabled for faster PHP execution
*  Linux-based development using WSL (Ubuntu)
*  Production-like local development setup

---

##  Folder Structure

```
PHP_Laravel12_Octane/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
└── README.md
```

---

##  Installation Guide

This guide explains how to set up a high-performance Laravel 12 development environment using:
• WSL (Ubuntu)
• Laravel Octane
• FrankenPHP
• MySQL (inside WSL)
• OPcache

---

## Requirements

• Windows 10/11
• WSL enabled
• Ubuntu installed via WSL
• Internet connection

---

## STEP 1 — Install WSL & Ubuntu

Open PowerShell as Administrator:

```
wsl --install
```

Restart your PC if asked.
After restart, Ubuntu will open and ask:
• Create a Linux username
• Set a password

---

## STEP 2 — Update Ubuntu

Inside WSL terminal:

```
sudo apt update && sudo apt upgrade -y
```

---

## STEP 3 — Install PHP & Required Extensions

```
sudo apt install php php-cli php-mysql php-curl php-xml php-mbstring unzip git curl -y
```

Check PHP:

```
php -v
```

---

## STEP 4 — Install Composer

```
curl -sS https://getcomposer.org/installer | php

sudo mv composer.phar /usr/local/bin/composer

composer -V
```

---

## STEP 5 — Install MySQL (Inside WSL)

```
sudo apt install mysql-server -y

sudo service mysql start
```

Set root password:

```
sudo mysql
```

Inside MySQL:

```
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
EXIT;
```

Test login:

```
mysql -u root -p
```

---

## STEP 6 — Create Database

```
mysql -u root -p

CREATE DATABASE laravel;
EXIT;
```

---

## STEP 7 — Create Laravel Project

Inside WSL home directory:

```
composer create-project laravel/laravel laravel-octane
cd laravel-octane
```

---

## STEP 8 — Configure .env

Edit environment file:

```
nano .env
```

Update database settings:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

Use file sessions for Octane:

```
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

---

## STEP 9 — Install Laravel Octane

```
composer require laravel/octane

php artisan octane:install
```

When asked, choose:

```
frankenphp
```

---

## STEP 10 — Fix Permissions

```
chmod -R 775 storage bootstrap/cache
```

---

## STEP 11 — Start Octane Server

```
php artisan octane:start --server=frankenphp --watch
```

Open browser:

```
http://localhost:8000
```
<img width="1290" height="725" alt="Screenshot 2026-01-28 133327" src="https://github.com/user-attachments/assets/29f3b251-3208-4d3d-849c-e907a1cbb9c6" />


You should see the Laravel welcome page.

---

## STEP 12 — Enable OPcache for CLI (Performance Boost)

```
sudo nano /etc/php/8.3/cli/php.ini
```

Set:

```
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=1
```

Restart Octane after saving.

---

##  Useful Commands

| Action            | Command                                                |
| ----------------- | ------------------------------------------------------ |
| Start server      | `php artisan octane:start --server=frankenphp --watch` |
| Stop server       | `php artisan octane:stop`                              |
| Reload server     | `php artisan octane:reload`                            |
| Kill stuck server | `pkill -f frankenphp`                                  |

---

##  Important Notes

• Always run Laravel from WSL path, not C:\xampp
• Do not store request data in static variables (Octane memory stays alive)
• Use queues for heavy tasks
• File sessions are best for local Octane development

---

