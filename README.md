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

> Products List
![picture alt](/ScreenShot/Products.png "Products List")

> Categories
![picture alt](/ScreenShot/Categories.png "Categories")

> POS Page
![picture alt](/ScreenShot/POS.png "")

> Setting
![picture alt](/ScreenShot/Setting-Store.png "")
![picture alt](/ScreenShot/Setting-Sales.png "")
![picture alt](/ScreenShot/Setting-Print.png "")
![picture alt](/ScreenShot/Setting-System.png "")
![picture alt](/ScreenShot/Setting-Backup.png "")
---

### How to install on the local system

> Requisites
* MySQL Server
* PHP
* Composer
* Laravel
* NPM


> install
* First, clone the project on your system
```bash
git clone https://github.com/alirezachali/Sales-Management-System.git
```
* Change the path to the project folder
```bash
cd Sales-Management-System
```
> Run
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