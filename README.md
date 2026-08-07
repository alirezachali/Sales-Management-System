# Sales and warehousing management system #

### Features: ###

* The possibility of deploying and running on a private or public network
* Role assignment for users
* Sale by barcode scanner
* Saving customer details and assigning subscription codes to them for offline orders.

---
### ScreenShot

> Dashboard
![picture alt](/ScreenShot/Dashboard.png "Dashboard Page")

> Database
![picture alt](/ScreenShot/database.png "Database")

---

### How to install on the local system

> Requisites
* MySQL Server
* PHP
* Composer
* Laravel
* NPM


> Run in Localhost
* First, clone the project on your system
```bash
git clone https://github.com/alirezachali/Sales-Management-System.git
cd Sales-Management-System/app
```
---
* install Laravel Requisites Package
```bash
composer install
```
---

* Copy .env.example file and Paste and rename to`.env`

> Linux
> ```bash
> cp .env.example .env

> Windows
> ```batch
> copy .env.example .env

* Config MySql in `.env` File
```
DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=laravel
 DB_USERNAME=root
 DB_PASSWORD=
```

```bash
php artisan key:generate
```
---


* run migration command 
```bash
php artisan migrate --seed
```

```bash
php artisan storage:link
```
---

* install and Build NPM
```bash
npm install

npm run build
```
---

* Make This Folders If Not Exist
```bash
mkdir storage\framework\cache
mkdir storage\framework\cache\data
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\framework\testing
mkdir storage\logs
```
---

* Run Project
```bash
npm run dev

php artisan serve
```

* Open `127.0.0.1:8000/` in browser

* Login With 
```
Username => admin
Password => 123456
```