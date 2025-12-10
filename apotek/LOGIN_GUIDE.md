# 🔐 Login Credentials & Getting Started

## 📝 Test Account Credentials

Three test accounts have been created for you to test the application:

### Account 1: Test User
```
Email:    test@example.com
Password: password
```

### Account 2: Pharmacy User
```
Email:    user@apotek.com
Password: password123
```

### Account 3: Administrator
```
Email:    admin@apotek.com
Password: admin123
```

---

## 🌐 How to Login

1. **Start the Application**
   ```bash
   php artisan serve
   ```
   Application will run at: `http://localhost:8000`

2. **Navigate to Login**
   - Open: http://localhost:8000/login
   - Or click "Login" link from home page

3. **Enter Credentials**
   - Email: (choose one from above)
   - Password: (use corresponding password)

4. **Click "Log in"**
   - You'll be redirected to the dashboard

---

## ✨ What You Can Do After Login

### 1. View Dashboard
- Welcome page with quick links
- Payment gateway card
- Quick access to payment and history

### 2. Make Payments
- Navigate to `/payment` or click "Make Payment"
- Enter payment amount
- Optionally add description
- Click "Proceed to Payment"
- Select payment method in Snap modal
- Complete payment

### 3. View Payment History
- Navigate to `/payment/history` or click "View History"
- See all your transactions
- View transaction status
- Click "View" to see details

### 4. Manage Profile
- Navigate to `/profile`
- Update name and email
- Change password
- Delete account

---

## 💳 Test Payment with Midtrans

After logging in, you can test payments:

### Test Card (Success)
```
Card Number: 4811111111111114
Expiry:      Any future date (e.g., 08/27)
CVV:         Any 3 digits (e.g., 123)
OTP:         Any 6 digits
Result:      ✓ Payment Successful
```

### Test Card (Alternative)
```
Card Number: 5555555555554444
Expiry:      Any future date
CVV:         Any 3 digits
Result:      ✓ Payment Successful
```

### Bank Transfer (Test)
```
Card Number: 9360000000000000
Select:      Bank Transfer BCA
Result:      ⏳ Payment Pending
```

---

## 🚀 Quick Start Steps

1. **Open Terminal**
   ```bash
   cd d:\Tugas\Dagang\Apps\apotek
   ```

2. **Start Development Server**
   ```bash
   php artisan serve
   ```

3. **Open Browser**
   - Go to: http://localhost:8000

4. **Click "Login"**
   - Use any credential from above

5. **Test Payment**
   - Go to Payment page
   - Enter amount: 100000
   - Use test card provided above

---

## 📚 Application Routes

### Public Routes
| Route | Purpose |
|-------|---------|
| `/` | Home/Welcome page |
| `/login` | Login page |
| `/register` | User registration |

### Protected Routes (Login Required)
| Route | Purpose |
|-------|---------|
| `/dashboard` | User dashboard |
| `/profile` | User profile |
| `/payment` | Payment form |
| `/payment/history` | Transaction history |
| `/payment/result` | Payment result page |

---

## 🎯 Features Available

✅ **Authentication**
- User registration
- Secure login
- Email verification ready
- Password reset option

✅ **Payments**
- Midtrans Snap integration
- Multiple payment methods
- Real-time status updates
- Webhook notifications

✅ **User Management**
- View/edit profile
- Change password
- View payment history
- Transaction details

✅ **Dashboard**
- Welcome message
- Quick payment links
- Payment history
- User information

---

## 🔧 Database Access

### Via phpMyAdmin
```
URL:      http://localhost/phpmyadmin
Server:   localhost
Username: root
Password: (leave empty)
Database: apotek
```

### View Users in phpMyAdmin
1. Select database: `apotek`
2. Click table: `users`
3. Click "Browse" tab
4. See all registered users

### View Transactions
1. Click table: `transactions`
2. Click "Browse" tab
3. See all payment records

---

## 💾 Backup Credentials

Save these credentials somewhere safe:

**Email Accounts:**
- test@example.com → password
- user@apotek.com → password123
- admin@apotek.com → admin123

**Database:**
- Host: 127.0.0.1
- Database: apotek
- Username: root
- Password: (empty)

## ❓ Troubleshooting

### "Access Denied" Error
- Ensure you're using correct email and password
- Credentials are case-sensitive
- Try clearing browser cache

### "Page not found"
- Ensure server is running: `php artisan serve`
- Check URL is correct
- Wait a few seconds for page to load

### "Payment not processing"
- Check internet connection
- Ensure Midtrans keys are set in .env
- Verify you're in sandbox mode
- Try refresh page

### "Can't login - redirect loop"
- Clear browser cookies/cache
- Try incognito/private window
- Restart server: `php artisan serve`

---

## 🔑 Creating Additional Users

### Via Application
1. Click "Register" on login page
2. Enter name, email, password
3. Click "Register"
4. Login with new credentials

### Via Terminal
```bash
php artisan tinker
User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => Hash::make('password')]);
```

---

## 📞 Support

- Check documentation: `MIDTRANS_INTEGRATION.md`
- View quick reference: `QUICK_REFERENCE.md`
- Database help: `DATABASE_GUIDE.md`
- View logs: `storage/logs/laravel.log`

---

**Happy testing!** 🎉

Remember: These are test accounts for development. For production, use strong passwords and proper user management.
