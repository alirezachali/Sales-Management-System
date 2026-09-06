#!/bin/bash

# تنظیم کدگذاری UTF-8
export LANG=en_US.UTF-8

# تابع برای نمایش بنر
show_banner() {
    clear
    cat << "EOF"
    
╔═══════════════════════════════════════════════════════╗
║                                                       ║
║                 🚀 Setup Script 🚀                   ║
║                                                       ║
║               Sales Management System                 ║
║                                                       ║
╚═══════════════════════════════════════════════════════╝

EOF
}

# تابع برای پرسش تایید
confirm() {
    local prompt="$1"
    local response
    
    while true; do
        read -p "$(echo -e '\033[36m'"$prompt"'\033[0m (y/n): ')" response
        case "$response" in
            [yY][eE][sS]|[yY])
                return 0
                ;;
            [nN][oO]|[nN])
                return 1
                ;;
            *)
                echo "لطفاً y یا n وارد کن"
                ;;
        esac
    done
}

# تابع برای نمایش پیام موفقیت
success() {
    echo -e "\033[32m✓ $1\033[0m"
}

# تابع برای نمایش پیام خطا
error() {
    echo -e "\033[31m❌ $1\033[0m"
}

# تابع برای نمایش پیام هشدار
warning() {
    echo -e "\033[33m⚠ $1\033[0m"
}

# تابع برای نمایش پیام اطلاعاتی
info() {
    echo -e "\033[36mℹ $1\033[0m"
}

# نمایش بنر
show_banner

# ============ مرحله 1: بررسی Composer ============
echo ""
echo -e "\033[35m[1/6] Checking the Composer installation...\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if ! command -v composer &> /dev/null; then
    error "Composer is not installed!"
    info "Please install the Composer: https://getcomposer.org"
    exit 1
fi
success "Composer found"

# ============ مرحله 2: بررسی Node.js ============
echo ""
echo -e "\033[35m[2/6] Checking the Node.js installation...\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if ! command -v node &> /dev/null; then
    warning "Node.js is not installed!"
    info "Please install the Node.js: https://nodejs.org"
else
    success "Node.js found (v$(node -v))"
fi

# ============ مرحله 3: نصب وابستگی‌های PHP ============
echo ""
echo -e "\033[35m[3/6] Installing PHP dependencies with Composer...\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if confirm "Do you want to run the (Composer install) ?"; then
    composer install
    if [ $? -ne 0 ]; then
        error "Error in composer install"
        exit 1
    fi
    success "PHP dependencies installed."
else
    warning " composer install rejected"
fi

# ============ مرحله 4: کپی فایل .env ============
echo ""
echo -e "\033[35m[4/6] تنظیم فایل .env\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        if confirm "آیا می‌خوای فایل .env از .env.example کپی شود؟"; then
            cp .env.example .env
            success "فایل .env ایجاد شد"
        else
            warning ".env کپی نشد"
        fi
    else
        warning "فایل .env.example یافت نشد"
    fi
else
    info "فایل .env قبلاً موجود است"
fi

# ============ مرحله 5: تولید APP_KEY ============
echo ""
echo -e "\033[35m[5/6] تولید کلید برنامه (APP_KEY)\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if confirm "آیا می‌خوای php artisan key:generate اجرا شود؟"; then
    php artisan key:generate
    if [ $? -ne 0 ]; then
        warning "خطا در تولید کلید (شاید قبلاً تولید شده)"
    else
        success "کلید برنامه تولید شد"
    fi
else
    warning "key:generate رد شد"
fi

# ============ مرحله 6: نصب وابستگی‌های Node ============
echo ""
echo -e "\033[35m[6/6] نصب وابستگی‌های Node.js\033[0m"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if ! command -v npm &> /dev/null; then
    warning "npm نصب نیست - نصب وابستگی‌های Node.js رد شد"
else
    if confirm "آیا می‌خوای npm install اجرا شود؟"; then
        npm install
        if [ $? -ne 0 ]; then
            warning "خطا در npm install"
        else
            success "وابستگی‌های Node.js نصب شدند"
        fi
    else
        warning "npm install رد شد"
    fi
fi

# ============ نتیجه نهایی ============
echo ""
echo "╔═══════════════════════════════════════════════════════╗"
echo "║      ✓ راه‌اندازی پروژه تمام شد!                      ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo ""

echo -e "\033[36m📝 مراحل بعدی:\033[0m"
echo ""
echo "1️⃣  تنظیمات .env رو چک کن:"
echo "   ${PWD}/.env"
echo ""
echo "2️⃣  فایل پایگاه‌داده رو تنظیم کن و سپس اجرا کن:"
echo "   php artisan migrate"
echo ""
echo "3️⃣  (اختیاری) برای ساخت داده نمونه:"
echo "   php artisan migrate:fresh --seed"
echo ""
echo "4️⃣  (اختیاری) برای کامپایل assets:"
echo "   npm run build"
echo ""
echo "5️⃣  سرور رو شروع کن:"
echo "   php artisan serve"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

