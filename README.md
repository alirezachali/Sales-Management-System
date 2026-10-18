### Language

[<img src="https://img.shields.io/badge/ReadMe(English)-018EF5.svg?style=for-the-badge&logo=ReadMe&logoColor=white">](README.md)
[<img src="https://img.shields.io/badge/ReadMe(فارسی)-018EF5.svg?style=for-the-badge&logo=ReadMe&logoColor=white" title="Click for Persian">](README.fa.md)

### Technologies

![Laravel](https://img.shields.io/badge/Laravel-FF2D20.svg?style=for-the-badge&logo=Laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4E56A6.svg?style=for-the-badge&logo=Livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4.svg?style=for-the-badge&logo=PHP&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3.svg?style=for-the-badge&logo=Bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=for-the-badge&logo=JavaScript&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=MySQL&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-003B57.svg?style=for-the-badge&logo=SQLite&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-FF4438.svg?style=for-the-badge&logo=Redis&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF.svg?style=for-the-badge&logo=Vite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

# Store Management System (POS & Inventory)

A comprehensive web-based store management system built with Laravel 12 and Livewire 4. It provides complete solutions for sales, inventory management, purchases, customers, suppliers, warehouses, cashboxes, expenses, employee management, reporting, and more.

## ✨ Features

- **Point of Sale (POS)** - Fast and intuitive sales interface with cart management
- **Product Management** - Add, edit, track products with categories, brands, images, and barcodes
- **Inventory Management** - Multi-warehouse stock tracking, stock movements, transfers, and adjustments
- **Purchase Management** - Purchase invoices, supplier management, and purchase tracking
- **Sales Management** - Sales records, invoices, and detailed reporting
- **Customer Management** - Customer profiles, roles, accounts, points, and transaction history
- **Supplier Management** - Supplier information and purchase history
- **Warehouse Management** - Manage multiple warehouses with stock levels per location
- **Cashbox Management** - Track cash transactions and balances
- **Expense Management** - Record and categorize business expenses
- **Employee Management** - Attendance, payroll, and employee records
- **Reporting & Analytics** - Sales reports, financial reports, and business insights
- **User & Role Management** - Role-based access control with permissions
- **Online Orders** - Integration with online shop API
- **Messaging System** - Internal messaging between users
- **Todos & Alerts** - Task management and system alerts
- **Multi-language Support** - Persian (fa) and English (en) translations
- **Barcode Generation** - Product barcode and QR code generation

## 📋 Requirements

Before you begin, ensure you have the following installed on your system:

- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/) >= 2.x
- [Node.js](https://nodejs.org/) >= 18.x or 20.x
- [NPM](https://www.npmjs.com/) or [Yarn](https://yarnpkg.com/)
- [MySQL](https://www.mysql.com/) 8.0+ or [SQLite](https://www.sqlite.org/)
- [Redis](https://redis.io/) (optional but recommended for queues/cache)

## 🚀 Installation

You can install this project using the automated setup scripts or manually. The Laravel application is located in the `laravel-app/` directory.

### Option 1: Automated Setup (Recommended)

#### For Linux/macOS

```bash
chmod +x setup-linux.sh
./setup-linux.sh
```

#### For Windows

Double-click `setup-windows.bat` or run it from Command Prompt.

The setup script will:
1. Check for Composer and Node.js
2. Install PHP dependencies (`composer install`)
3. Copy `.env.example` to `.env`
4. Generate application key
5. Install Node.js dependencies (`npm install`)

### Option 2: Manual Installation

```bash
# Navigate to Laravel app directory
cd laravel-app

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Install Node.js dependencies
npm install

# Build assets
npm run build
```

## ⚙️ Configuration

1. Open `laravel-app/.env` and configure your database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. (Optional) Configure Redis for queue/cache:

```env
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

3. (Optional) Configure online shop integration if needed:

```env
ONLINE_SHOP_URL=
ONLINE_SHOP_TOKEN=
ONLINE_SHOP_CITY=
```

## 🗄️ Database Setup

After configuring your database, run migrations. You can choose to start with an empty database or seed it with sample data.

### Option A: Empty Database

```bash
cd laravel-app
php artisan migrate
```

### Option B: With Sample Data (Includes Admin User)

```bash
cd laravel-app
php artisan migrate:fresh --seed
```

If you seed the database, you'll get a default admin user to log in.

## 🏃 Running the Application

### Development Mode

For development, you can run the server and Vite dev server concurrently:

```bash
cd laravel-app
composer run dev
```

This will start:
- Laravel development server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log viewer (`php artisan pail`)
- Vite dev server (`npm run dev`)

Or run them separately:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev

# Terminal 3: Queue worker (if needed)
php artisan queue:work
```

### Production Mode

```bash
cd laravel-app
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Then serve using your web server (Apache, Nginx) pointing to `laravel-app/public/`.

## 📁 Project Structure

```
.
├── laravel-app/           # Main Laravel application
│   ├── app/
│   │   ├── Http/          # Controllers, Middleware
│   │   ├── Livewire/      # Livewire components (UI)
│   │   ├── Models/        # Eloquent models
│   │   ├── Services/      # Business logic
│   │   └── Helpers/       # Helper functions
│   ├── database/
│   │   ├── migrations/    # Database migrations
│   │   ├── seeders/       # Database seeders
│   │   └── factories/     # Model factories
│   ├── resources/
│   │   ├── views/         # Blade templates
│   │   ├── js/            # JavaScript files
│   │   └── css/           # CSS files
│   ├── routes/            # Application routes
│   └── lang/              # Translation files (fa, en)
├── setup-linux.sh         # Linux/macOS setup script
├── setup-windows.bat      # Windows setup script
├── README.md             # English documentation
└── README.fa.md          # Persian documentation
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgements

- [Laravel](https://laravel.com/) - The PHP Framework
- [Livewire](https://livewire.laravel.com/) - Full-stack framework for Laravel
- [Tabler](https://tabler.io/) - Premium dashboard template
- [Bootstrap](https://getbootstrap.com/) - Frontend framework