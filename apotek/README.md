# Apotek - Laravel 12 Breeze with Midtrans Integration

A modern pharmacy management system built with Laravel 12 and Breeze, featuring complete Midtrans payment gateway integration.

## 🎯 Features

### Authentication & Security
- User registration and login (Laravel Breeze)
- Password encryption
- Email verification ready
- CSRF protection
- Secure webhook handling

### Payment Management
- **Midtrans Snap API Integration** - Full payment gateway integration
- Multiple payment methods (Cards, Bank Transfer, E-Wallets, BNPL)
- **Real-time Payment Status** - Webhook-based updates
- **Transaction History** - Complete payment records
- **Secure Payments** - 3DS enabled, server-side verification

### User Experience
- Responsive Blade templates with Tailwind CSS
- Dark mode support
- Payment form with validation
- Transaction history with pagination
- Payment result confirmation page

## 📋 Prerequisites

- PHP 8.2+
- Composer
- SQLite (or MySQL/PostgreSQL)
- Node.js & npm

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Midtrans Credentials
Get credentials from https://www.midtrans.com:

```bash
# Edit .env
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Start Development Server
```bash
# Terminal 1: PHP server
php artisan serve

# Terminal 2: Build assets
npm run dev
```

Visit: http://localhost:8000

## 📁 Key Files & Directories

```
app/
├── Http/Controllers/PaymentController.php     # Payment operations
├── Models/
│   ├── User.php                               # User model
│   └── Transaction.php                        # Transaction model
└── Services/MidtransService.php               # Midtrans API wrapper

config/
└── midtrans.php                               # Midtrans configuration

database/migrations/
└── 2024_12_07_000000_create_transactions_table.php

resources/views/payment/
├── form.blade.php                             # Payment form
├── result.blade.php                           # Payment result
└── history.blade.php                          # Transaction history

routes/
└── web.php                                    # Payment routes

tests/Feature/
└── PaymentTest.php                            # Payment tests
```

## 🔗 Available Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/payment` | GET | Payment form |
| `/payment/create` | POST | Create payment token |
| `/payment/result` | GET | Payment result |
| `/payment/history` | GET | Transaction history |
| `/midtrans-webhook` | POST | Midtrans notifications |

## 💳 Payment Integration

### How It Works
1. User fills payment form at `/payment`
2. JavaScript sends AJAX request to `/payment/create`
3. Controller creates transaction and Midtrans Snap token
4. Snap modal opens for payment completion
5. Midtrans sends webhook notification
6. Transaction status updates in database
7. User sees result at `/payment/result`

### Test Cards (Sandbox)
- Visa: `4811111111111114`
- Mastercard: `5555555555554444`
- BCA Transfer: `9360000000000000`

Expiry: Any future date | CVV: Any 3 digits

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run payment tests
php artisan test tests/Feature/PaymentTest.php

# With coverage
php artisan test --coverage
```

## 📊 Database Schema

### Transactions Table
```sql
- id
- user_id (foreign key)
- order_id (unique)
- amount (decimal)
- payment_method
- status (pending, completed, failed, expired)
- midtrans_token
- description
- metadata (json)
- timestamps
```

## 🔐 Security

✓ CSRF protection ✓ Webhook verification ✓ 3D Secure ✓ Server-side validation ✓ Secure credentials

## 📚 Documentation

- **Setup Guide:** `SETUP_CHECKLIST.md`
- **Integration Details:** `MIDTRANS_INTEGRATION.md`
- **Midtrans Docs:** https://docs.midtrans.com
- **Laravel Docs:** https://laravel.com/docs

## 🚀 Production Deployment

1. Get production keys from Midtrans Dashboard
2. Update `.env` with production credentials
3. Set `MIDTRANS_IS_PRODUCTION=true`
4. Configure webhook URL in Midtrans Dashboard
5. Run `php artisan migrate --force`
6. Run `npm run build`

## 📄 License

MIT License - see LICENSE file for details

---

**Happy coding!** 🎉 See `SETUP_CHECKLIST.md` for detailed setup instructions.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
