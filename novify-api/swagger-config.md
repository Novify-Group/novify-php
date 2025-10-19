# Swagger Configuration for Novify API

## Environment Variables

Add these to your `.env` file:

```env
# Swagger Configuration
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost:8000
L5_SWAGGER_UI_DARK_MODE=false
L5_SWAGGER_UI_DOC_EXPANSION=none
L5_SWAGGER_UI_FILTERS=true
L5_SWAGGER_UI_PERSIST_AUTHORIZATION=false
```

## Access Points

- **Swagger UI**: `http://localhost:8000/api/documentation`
- **JSON API Docs**: `http://localhost:8000/api/docs`
- **YAML API Docs**: `http://localhost:8000/api/docs.yaml`

## Features

✅ **Complete API Documentation**
- Authentication endpoints
- Merchant management
- Product catalog
- Order processing
- Wallet operations
- Bill payment services

✅ **Interactive Testing**
- Try out API endpoints directly
- JWT authentication support
- Request/response examples

✅ **Clean Documentation**
- Organized by tags
- Detailed request/response schemas
- Example data for all endpoints

## Usage

1. **Generate Documentation**:
   ```bash
   php artisan l5-swagger:generate
   ```

2. **Access Documentation**:
   - Visit `http://localhost:8000/api/documentation`
   - Use the "Authorize" button to set JWT token
   - Test endpoints directly from the interface

3. **Development Mode**:
   - Set `L5_SWAGGER_GENERATE_ALWAYS=true` for auto-regeneration
   - Set `L5_SWAGGER_GENERATE_ALWAYS=false` for production

## API Endpoints Documented

### Authentication
- `POST /api/v1/auth/register` - Merchant registration
- `POST /api/v1/auth/login` - Merchant login
- `POST /api/v1/auth/verify-otp` - OTP verification

### Merchants
- `GET /api/v1/merchant/` - List merchants
- `POST /api/v1/merchant/branches` - Create branch
- `GET /api/v1/merchant/branches` - List branches

### Products
- `GET /api/v1/products/` - List products
- `POST /api/v1/products/` - Create product
- `GET /api/v1/products/{id}` - Get product
- `PUT /api/v1/products/{id}` - Update product

### Orders
- `GET /api/v1/merchant/orders/` - List orders
- `POST /api/v1/merchant/orders/` - Create order
- `GET /api/v1/merchant/orders/{id}` - Get order

### Wallets
- `POST /api/v1/wallet/topup` - Top up wallet
- `POST /api/v1/wallet/transfer` - Transfer money
- `POST /api/v1/wallet/pay` - Pay for order
- `GET /api/v1/wallet/transactions` - Get transactions

### Bills
- `GET /api/v1/bills/categories` - Get bill categories
- `POST /api/v1/bills/validate` - Validate bill
- `POST /api/v1/bills/pay` - Process bill payment

## Security

All protected endpoints require JWT authentication:
1. Register/Login to get a token
2. Use "Authorize" button in Swagger UI
3. Enter: `Bearer your_jwt_token_here`
4. Test protected endpoints

## Customization

The Swagger documentation is generated from PHP annotations in the controllers. To add more documentation:

1. Add `@OA\*` annotations to controller methods
2. Run `php artisan l5-swagger:generate`
3. Refresh the documentation page

## Production Notes

- Set `L5_SWAGGER_GENERATE_ALWAYS=false` in production
- Consider restricting access to documentation in production
- Use HTTPS for the documentation URL
