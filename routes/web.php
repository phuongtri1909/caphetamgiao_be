<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return 'Cache cleared';
})->name('clear.cache');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return view('admin.pages.dashboard');
        })->name('dashboard');

        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('categories', CategoryController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);

        Route::resource('reviews', ReviewController::class)->except(['show']);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::resource('news', NewsController::class)->except(['show']);

        Route::post('/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload.image');

        
    });

    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', function () {
            return view('admin.pages.auth.login');
        })->name('login');

        Route::post('login', [AuthController::class, 'login'])->name('login');
    });
});
