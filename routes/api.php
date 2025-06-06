<?php

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\Store\SizeController;
use App\Http\Controllers\Store\OrderController;
use App\Http\Controllers\Features\BrandController;
use App\Http\Controllers\Features\TrendController;
use App\Http\Controllers\Features\SearchController;
use App\Http\Controllers\Features\ContentController;
use App\Http\Controllers\Features\KeywordController;
use App\Http\Controllers\Features\ProductController;
use App\Http\Controllers\Features\CategoryController;
use App\Http\Controllers\Store\StoreProductController;
use App\Http\Controllers\Store\StoreLocationController;
use App\Http\Controllers\Features\ContentContractController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Add your protected API routes here
});

Route::get('/generate-token', function () {
    $privateKey = file_get_contents(storage_path('keys/private.key'));

    $payload = [];

    $jwt = JWT::encode($payload, $privateKey, 'RS256');

    return response()->json(['token' => $jwt]);
});

Route::middleware(['token'])->group(function () {
    //store
    Route::resource('/orders', OrderController::class);

    Route::get('/store-products', [StoreProductController::class, 'indexApi']);

    Route::get('/locations', [StoreLocationController::class, 'index']);

    Route::post('/locations', [StoreLocationController::class, 'store']);
});


Route::post('/scanner', [ScannerController::class, 'store'])->name('scanner.store');
Route::post('/qr-order', [ScannerController::class, 'order'])->name('scanner.order');



