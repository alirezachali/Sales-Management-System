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

* run migration command
```bash
php artisan migrate
```
* run server
```bash
php artisan serve
```
* open the new terminal and run command
```bash
npm run dev
```

* Now open `127.0.0.1:8000/dashboard` in the browser