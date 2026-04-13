<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VariantController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminVariantController;
use App\Http\Controllers\Api\Admin\AdminSupplierController;
use App\Http\Controllers\Api\Admin\AdminTagController;
use App\Http\Controllers\Api\Admin\StockController;
use App\Http\Controllers\Api\Admin\VariantSupplierController;
use App\Http\Controllers\Api\Admin\ImageUploadController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\StripeWebhookController;

// Auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/products/{product}/variants', [VariantController::class, 'index']);

// Stripe webhook (sans middleware auth — Stripe appelle directement)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

// Checkout — création PaymentIntent
Route::post('/checkout/payment-intent', [CheckoutController::class, 'createPaymentIntent']);

// Orders (public for guest checkout)
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/webhook', [OrderController::class, 'webhook']);
Route::get('/orders/by-email', [OrderController::class, 'byEmail']);
Route::get('/orders/{id}', [OrderController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail']);

    // User stats
    Route::get('/user/stats', [UserController::class, 'stats']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Factures
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Products CRUD
    Route::apiResource('products', AdminProductController::class);

    // Variants (nested under products)
    Route::apiResource('products.variants', AdminVariantController::class);

    // Categories CRUD
    Route::apiResource('categories', AdminCategoryController::class);

    // Suppliers CRUD
    Route::apiResource('suppliers', AdminSupplierController::class);

    // Tags CRUD
    Route::apiResource('tags', AdminTagController::class);

    // Variant-Supplier links
    Route::get('/variants/{variantId}/suppliers', [VariantSupplierController::class, 'index']);
    Route::post('/variants/{variantId}/suppliers', [VariantSupplierController::class, 'store']);
    Route::delete('/variants/{variantId}/suppliers/{supplierId}', [VariantSupplierController::class, 'destroy']);

    // Stock management
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/{variantId}', [StockController::class, 'show']);
    Route::post('/stocks/{variantId}/adjust', [StockController::class, 'adjust']);
    Route::get('/stocks/{variantId}/movements', [StockController::class, 'movements']);
    Route::get('/stock-movements', [StockController::class, 'allMovements']);

    // Orders management
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    // Image upload
    Route::post('/upload', [ImageUploadController::class, 'upload']);
    Route::delete('/upload', [ImageUploadController::class, 'delete']);
});
