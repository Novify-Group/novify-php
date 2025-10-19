<?php

namespace App\Http\Controllers\API;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Novify API",
 *     version="1.0.0",
 *     description="Digital Marketplace Platform API Documentation",
 *     @OA\Contact(
 *         email="support@novify.com",
 *         name="Novify Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://novify.solvertech.co",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="jwt",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Bearer Token Authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints for merchants and staff"
 * )
 * 
 * @OA\Tag(
 *     name="Merchants",
 *     description="Merchant management and operations"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Product catalog and inventory management"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Order processing and sales management"
 * )
 * 
 * @OA\Tag(
 *     name="Wallets",
 *     description="Digital wallet operations and transactions"
 * )
 * 
 * @OA\Tag(
 *     name="Bills",
 *     description="Bill payment services and utilities"
 * )
 * 
 * @OA\Tag(
 *     name="Lookup",
 *     description="Reference data and lookup services"
 * )
 */
class SwaggerController
{
    // This controller is used only for Swagger documentation
}
