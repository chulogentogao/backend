<?php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Public routes
Route::middleware('throttle:api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/logout', [AuthController::class, 'logout']); // Logout doesn't require auth
});

// Protected routes
Route::middleware(['auth:web', 'throttle:api'])->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);

    // Normal user routes
    Route::prefix('user')->group(function () {
        // User can view items
        Route::get('/items', [ItemController::class, 'index']);
        Route::get('/items/{id}', [ItemController::class, 'show']);
    });
    
    // Admin routes - full CRUD on items and user management
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Item management
        Route::apiResource('items', ItemController::class);
        
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}/toggle-restriction', [UserController::class, 'toggleRestriction']);
    });
});