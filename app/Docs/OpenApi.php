<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Product Management API",
 *     description="RESTful API for products, categories, authentication, image uploads, and soft deletes"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use Bearer Token from login endpoint"
 * )
 */

namespace App\Docs;

class OpenApi {}
