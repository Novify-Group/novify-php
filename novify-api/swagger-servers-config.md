# Swagger Multi-Server Configuration

## Environment Variables

Add these to your `.env` file to configure Swagger for both servers:

```env
# Swagger Configuration
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_CONST_HOST=http://localhost:8000
L5_SWAGGER_UI_DARK_MODE=false
L5_SWAGGER_UI_DOC_EXPANSION=none
L5_SWAGGER_UI_FILTERS=true
L5_SWAGGER_UI_DISPLAY_REQUEST_DURATION=true
L5_SWAGGER_UI_TRY_IT_OUT_ENABLED=true

# For Production Server
# L5_SWAGGER_CONST_HOST=https://novify.solvertech.co
```

## Server Configuration

### Local Development (localhost:8000)
- **Base URL**: `http://localhost:8000`
- **API Documentation**: `http://localhost:8000/api/documentation`
- **JSON Docs**: `http://localhost:8000/api/docs`
- **YAML Docs**: `http://localhost:8000/api/docs.yaml`

### Production Server (novify.solvertech.co)
- **Base URL**: `https://novify.solvertech.co`
- **API Documentation**: `https://novify.solvertech.co/api/documentation`
- **JSON Docs**: `https://novify.solvertech.co/api/docs`
- **YAML Docs**: `https://novify.solvertech.co/api/docs.yaml`

## Features

### Multi-Server Support
- Swagger UI includes a server selection dropdown
- Users can switch between local development and production
- All endpoints work with both servers
- JWT authentication works on both environments

### Interactive Testing
- Complete request/response examples
- JWT Bearer token authentication
- Parameter validation
- Error response documentation
- Real-time API testing

### Documentation Coverage
- **60+ API Endpoints** fully documented
- **15 Controllers** with complete annotations
- **All HTTP Methods** (GET, POST, PUT, DELETE)
- **Complete Schemas** for requests and responses
- **Security Definitions** for JWT authentication
