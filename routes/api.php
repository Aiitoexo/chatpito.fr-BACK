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
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\StockAlertController;
use App\Http\Controllers\Api\ShippingController;
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
Route::get('/products/{slug}/reviews', [ReviewController::class, 'index']);
Route::post('/products/stock-alert', [StockAlertController::class, 'subscribe']);

// Stripe webhook (sans middleware auth — Stripe appelle directement)
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

// Coupons
Route::post('/coupons/apply', [CouponController::class, 'apply']);

// Shipping — calcul frais de port
Route::post('/shipping/calculate', [ShippingController::class, 'calculate']);

// Returns — demande client
Route::post('/returns', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'email' => 'required|email',
        'reason' => 'required|string|max:1000',
    ]);
    $return = \App\Models\ReturnRequest::create([
        'order_id' => $validated['order_id'],
        'customer_email' => $validated['email'],
        'reason' => $validated['reason'],
    ]);
    return response()->json(['data' => $return], 201);
});

// Checkout
Route::post('/checkout/save-cart', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'items' => 'required|array',
        'total' => 'required|numeric',
    ]);
    \App\Models\AbandonedCart::updateOrCreate(
        ['email' => $validated['email']],
        ['items' => $validated['items'], 'total' => $validated['total'], 'user_id' => $request->user()?->id]
    );
    return response()->json(['message' => 'ok']);
});
Route::post('/checkout/payment-intent', [CheckoutController::class, 'createPaymentIntent']);

// Orders (public for guest checkout)
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/webhook', [OrderController::class, 'webhook']);
Route::get('/orders/by-email', [OrderController::class, 'byEmail']);
Route::get('/orders/by-payment-intent/{paymentIntent}', [OrderController::class, 'byPaymentIntent']);
Route::get('/orders/{id}', [OrderController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail']);

    // User
    Route::get('/user/stats', [UserController::class, 'stats']);
    Route::patch('/user/profile', [UserController::class, 'updateProfile']);

    // Addresses
    Route::get('/user/addresses', [AddressController::class, 'index']);
    Route::post('/user/addresses', [AddressController::class, 'store']);
    Route::put('/user/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/user/addresses/{id}', [AddressController::class, 'destroy']);
    Route::patch('/user/addresses/{id}/default', [AddressController::class, 'setDefault']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Factures
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download']);

    // Reviews
    Route::get('/user/reviews', [ReviewController::class, 'myReviews']);
    Route::post('/products/{slug}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Wishlist
    Route::get('/user/wishlist', [WishlistController::class, 'index']);
    Route::post('/user/wishlist/{productId}', [WishlistController::class, 'toggle']);
    Route::delete('/user/wishlist/{productId}', [WishlistController::class, 'destroy']);
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
    Route::get('/orders/export', [AdminOrderController::class, 'export']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    // Image upload
    Route::post('/upload', [ImageUploadController::class, 'upload']);
    Route::delete('/upload', [ImageUploadController::class, 'delete']);

    // Reviews moderation
    Route::get('/reviews', [ReviewController::class, 'adminIndex']);
    Route::patch('/reviews/{id}/approve', [ReviewController::class, 'approve']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'adminDestroy']);

    // Activity logs
    Route::get('/logs', [Admin\ActivityLogController::class, 'index']);

    // SAV / Returns
    Route::get('/returns', [Admin\AdminReturnController::class, 'index']);
    Route::get('/returns/{id}', [Admin\AdminReturnController::class, 'show']);
    Route::patch('/returns/{id}/approve', [Admin\AdminReturnController::class, 'approve']);
    Route::patch('/returns/{id}/reject', [Admin\AdminReturnController::class, 'reject']);
    Route::post('/returns/{id}/refund', [Admin\AdminReturnController::class, 'refund']);

    // Reports
    Route::get('/reports/sales', [Admin\ReportController::class, 'sales']);
    Route::get('/reports/products', [Admin\ReportController::class, 'products']);
    Route::get('/reports/orders', [Admin\ReportController::class, 'orders']);

    // Coupons CRUD
    Route::get('/coupons', [Admin\AdminCouponController::class, 'index']);
    Route::post('/coupons', [Admin\AdminCouponController::class, 'store']);
    Route::put('/coupons/{id}', [Admin\AdminCouponController::class, 'update']);
    Route::delete('/coupons/{id}', [Admin\AdminCouponController::class, 'destroy']);
    Route::patch('/coupons/{id}/toggle', [Admin\AdminCouponController::class, 'toggle']);
});
