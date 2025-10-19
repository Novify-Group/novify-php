# 🏪 Novify - Digital Marketplace Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  <img src="https://img.shields.io/badge/Status-Active-brightgreen.svg" alt="Status">
</p>

## 🎯 Overview

**Novify** is a comprehensive **Digital Marketplace Platform** that combines **Point of Sale (POS)**, **Online Ordering**, **Digital Wallet System**, and **Bill Payment Services** into a unified ecosystem. It enables merchants to create their own digital stores, manage products, process orders, and offer financial services to customers.

## 🌟 Key Features

### 🏪 **Digital Marketplace**
- **Multi-Merchant Platform**: Multiple merchants with their own stores
- **Product Catalog**: Comprehensive product management with variants, images, and inventory
- **Online Ordering**: Customers can browse and order from merchant stores
- **Point of Sale (POS)**: In-store transaction processing
- **Multi-Location Support**: Merchants can manage multiple branches

### 💰 **Digital Wallet System**
- **Multi-Wallet Support**: Merchants can have multiple wallets (Main, Savings, Expenses, etc.)
- **Money Transfers**: Wallet-to-wallet transfers between users
- **Payment Processing**: Support for cash, mobile money, card payments, and wallet payments
- **Transaction History**: Complete audit trail of all financial operations
- **Cross-Merchant Payments**: B2B transactions between merchants

### 🧾 **Bill Payment Services**
- **Utility Bills**: Electricity, water, internet, and other utility payments
- **Service Payments**: Various service provider payments
- **Bill Validation**: Customer account verification before payment
- **Payment Processing**: Secure bill payment transactions
- **Service Provider Integration**: Connect with multiple billers and service providers

### 👥 **User Management**
- **Merchant Onboarding**: Complete KYC process with document verification
- **Staff Management**: Role-based access for attendants and distributors
- **Customer Management**: Customer database with purchase history
- **Multi-Authentication**: Support for different user types and permissions

## 🏗️ **System Architecture**

### **Core Components**

#### **1. Merchant Management**
```
Merchants
├── Personal Information & KYC
├── Business Registration
├── Store Management
├── Branch Management
├── Staff Management
└── Financial Services
```

#### **2. Product & Inventory System**
```
Products
├── Product Catalog
├── Categories & Subcategories
├── Product Variants (size, color, etc.)
├── Inventory Tracking
├── Pricing Management
├── Image Management
└── Stock Alerts
```

#### **3. Order & Sales Management**
```
Orders
├── Online Ordering
├── Point of Sale (POS)
├── Customer Management
├── Payment Processing
├── Order Tracking
└── Sales Analytics
```

#### **4. Digital Wallet System**
```
Wallets
├── Multi-Wallet Support
├── Balance Management
├── Money Transfers
├── Payment Methods
├── Transaction History
└── Cross-Merchant Payments
```

#### **5. Bill Payment Services**
```
Bill Payments
├── Utility Bill Payments
├── Service Provider Payments
├── Bill Validation
├── Payment Processing
└── Transaction Records
```

## 🚀 **Technology Stack**

### **Backend**
- **Framework**: Laravel 11.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: JWT (JSON Web Tokens)
- **API**: RESTful API with comprehensive endpoints
- **Payments**: Mobile Money, Card Payments, Wallet System

### **Frontend**
- **Web Interface**: Laravel Blade Templates
- **UI Framework**: Bootstrap 4 (SB Admin 2)
- **Mobile-First**: Responsive design for all devices
- **Charts**: Chart.js for analytics and reporting

### **Key Integrations**
- **SMS Services**: Africa's Talking, EgoSMS
- **Payment Gateways**: Mobile Money, Card Payments
- **Bill Payment Providers**: Various utility and service providers

## 📱 **API Endpoints**

### **Authentication**
```
POST /api/v1/auth/register          # Merchant registration
POST /api/v1/auth/login             # Merchant login
POST /api/v1/auth/attendant-login   # Staff login
POST /api/v1/auth/verify-otp        # OTP verification
POST /api/v1/auth/logout            # Logout
```

### **Merchant Management**
```
GET  /api/v1/merchant/              # Get merchants
GET  /api/v1/merchant/wallet/{number} # Get merchant by wallet
POST /api/v1/merchant/branches       # Create branch
GET  /api/v1/merchant/branches       # List branches
POST /api/v1/merchant/users          # Create staff
GET  /api/v1/merchant/users          # List staff
```

### **Product Management**
```
GET  /api/v1/products/               # List products
POST /api/v1/products/               # Create product
GET  /api/v1/products/{id}           # Get product
PUT  /api/v1/products/{id}           # Update product
DELETE /api/v1/products/{id}         # Delete product
POST /api/v1/products/{id}/variants  # Add product variant
POST /api/v1/products/{id}/images    # Add product image
```

### **Order Management**
```
GET  /api/v1/merchant/orders/        # List orders
POST /api/v1/merchant/orders/        # Create order
GET  /api/v1/merchant/orders/{id}    # Get order
```

### **Wallet System**
```
POST /api/v1/wallet/topup            # Top up wallet
POST /api/v1/wallet/transfer         # Transfer money
POST /api/v1/wallet/pay              # Pay for order
GET  /api/v1/wallet/transactions      # Get transactions
GET  /api/v1/wallet/                  # List wallets
```

### **Bill Payment Services**
```
GET  /api/v1/bills/categories        # Get bill categories
GET  /api/v1/bills/billers           # Get billers
POST /api/v1/bills/validate          # Validate bill
POST /api/v1/bills/pay               # Process bill payment
GET  /api/v1/bills/payments/{ref}    # Get payment status
```

## 🗄️ **Database Schema**

### **Core Tables**
- **merchants**: Merchant information and business details
- **branches**: Multi-location support for merchants
- **merchant_users**: Staff members (attendants, distributors)
- **customers**: Customer database
- **products**: Product catalog with variants and images
- **orders**: Sales transactions
- **order_items**: Order line items
- **wallets**: Digital wallet accounts
- **wallet_transactions**: Financial transaction records
- **bill_payments**: Bill payment transactions

### **Key Relationships**
- Merchants have multiple branches and staff
- Merchants have multiple wallets and products
- Orders link merchants, customers, and products
- Wallet transactions track all financial operations
- Bill payments connect to external service providers

## 🎯 **Target Market**

### **Primary Users**
- **Small-Medium Retailers**: Digital transformation of traditional stores
- **Multi-Location Businesses**: Centralized management across branches
- **Service Providers**: Offering bill payment and financial services
- **Emerging Markets**: Mobile-first approach for developing regions

### **Use Cases**
- **Retail Stores**: Complete POS and inventory management
- **Restaurants**: Online ordering and payment processing
- **Service Centers**: Bill payment and financial services
- **Marketplaces**: Multi-vendor platform for various merchants

## 🚀 **Getting Started**

### **Prerequisites**
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Laravel 11.x

### **Installation**
```bash
# Clone the repository
git clone https://github.com/your-org/novify-api.git
cd novify-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Generate Swagger documentation
php artisan l5-swagger:generate

# Start the development server
php artisan serve
```

### **API Documentation**

#### **Local Development Server (localhost:8000)**
- **Swagger UI**: `http://localhost:8000/api/documentation`
- **JSON API Docs**: `http://localhost:8000/api/docs`
- **YAML API Docs**: `http://localhost:8000/api/docs.yaml`

#### **Production Server (novify.solvertech.co)**
- **Swagger UI**: `https://novify.solvertech.co/api/documentation`
- **JSON API Docs**: `https://novify.solvertech.co/api/docs`
- **YAML API Docs**: `https://novify.solvertech.co/api/docs.yaml`

#### **Interactive Testing**
- Available in Swagger UI with JWT authentication
- Server selection dropdown allows switching between local and production
- Complete request/response examples for all endpoints

### **Configuration**
1. Configure database connection in `.env`
2. Set up SMS service credentials
3. Configure payment gateway settings
4. Set up file storage for images
5. Configure CORS settings for API access

## 📊 **Business Model**

### **Revenue Streams**
- **Transaction Fees**: On wallet transactions and payments
- **Bill Payment Commissions**: From utility and service payments
- **Subscription Fees**: For premium features or usage limits
- **Merchant Services**: Additional business management tools

### **Competitive Advantages**
- **All-in-One Platform**: Complete marketplace solution
- **Mobile Money Integration**: Critical for African markets
- **Multi-Location Support**: Enterprise features for small businesses
- **Bill Payment Services**: Additional revenue stream for merchants
- **International Support**: Multi-currency and geographic support

## 🔧 **Development**

### **Code Structure**
```
app/
├── Http/Controllers/
│   ├── API/           # API controllers
│   ├── UI/            # Web interface controllers
│   └── Auth/          # Authentication controllers
├── Models/            # Eloquent models
├── Services/          # Business logic services
├── Contracts/         # Service contracts
└── Helpers/           # Utility helpers

routes/
├── api.php            # API routes
├── ui.php             # Web interface routes
├── auth.php           # Authentication routes
└── bills.php          # Bill payment routes
```

### **Key Features**
- **Service Layer Pattern**: Business logic separated from controllers
- **Repository Pattern**: Data access abstraction
- **Contract-Based Services**: Dependency injection with interfaces
- **Comprehensive Validation**: Request validation and business rules
- **Transaction Management**: ACID compliance for financial operations

## 📈 **Roadmap**

### **Phase 1** ✅
- [x] Core marketplace functionality
- [x] Merchant onboarding and management
- [x] Product catalog and inventory
- [x] Order processing and POS
- [x] Digital wallet system
- [x] Bill payment services

### **Phase 2** 🚧
- [ ] Mobile applications (iOS/Android)
- [ ] Advanced analytics and reporting
- [ ] Multi-language support
- [ ] Advanced payment integrations
- [ ] Marketplace search and discovery

### **Phase 3** 📋
- [ ] AI-powered recommendations
- [ ] Advanced inventory management
- [ ] Supply chain integration
- [ ] Advanced financial services
- [ ] International expansion

## 🤝 **Contributing**

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### **Development Setup**
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 **Support**

- **Documentation**: [Wiki](https://github.com/your-org/novify-api/wiki)
- **Issues**: [GitHub Issues](https://github.com/your-org/novify-api/issues)
- **Discussions**: [GitHub Discussions](https://github.com/your-org/novify-api/discussions)

## 🙏 **Acknowledgments**

- Laravel Framework
- Bootstrap SB Admin 2 Template
- Chart.js for analytics
- All contributors and supporters

---

**Novify** - *Empowering Digital Commerce in Emerging Markets* 🌍