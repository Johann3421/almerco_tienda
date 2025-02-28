<?php

use App\Http\Controllers\GetImageController;
use App\Http\Controllers\Panel\FilterItemsController;
use App\Http\Controllers\Panel\GroupController;
use App\Http\Controllers\Panel\SubGroupController;
use App\Http\Controllers\ProfileController;
use App\Mail\SendDemoMail;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\BannerController;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\ClientController;
use App\Http\Controllers\Panel\OrderController;
use App\Http\Controllers\Web\OrderControllerWeb;
use App\Http\Controllers\Panel\PanelController;
use App\Http\Controllers\Panel\ProductController;
use App\Http\Controllers\Panel\FilterController;
use App\Http\Controllers\Web\ProductControllerWeb;
use App\Http\Controllers\Panel\SettingController;
use App\Http\Controllers\Web\SettingControllerWeb;
use App\Http\Controllers\Web\CategoryWebController;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOficialMail;

Route::prefix('/')->group(function () {

    // Route::get('/send-email', function () {

    //     $client_email = 'jalop772000@gmail.com';

    //     Mail::to($client_email)->send(new SendDemoMail([
    //         'title' => 'Test Mail',
    //         'url' => 'https://www.google.com',
    //         'client_name' => 'Alexander Carlos',
    //         'client_document' => '74044376',
    //         'order_code' => 'ORD-2024000001',
    //         'order_date' => '2024-09-22',
    //         'order_subtotal' => '150.00',
    //         'order_igv' => '0.00',
    //         'order_total' => '150.00',
    //         'order_status' => 'Pendiente',
    //         'order_items' => [
    //             [
    //                 'product_name' => 'Producto 1',
    //                 'product_quantity' => 2,
    //                 'product_price' => 50.00,
    //                 'product_total' => 100.00
    //             ],
    //             [
    //                 'product_name' => 'Producto 2',
    //                 'product_quantity' => 1,
    //                 'product_price' => 50.00,
    //                 'product_total' => 50.00
    //             ]
    //         ]

    //     ]));

    //     return "Email has been sent to: {$client_email}";
    // });

    Route::post('/send-email', [SendOficialMail::class, 'enviarCorreo']);

    Route::get('getImage', [GetImageController::class, 'getImage'])->name('getImage');

    Route::get('/', [ProductControllerWeb::class, 'index'])->name('web');
    Route::get('/product/{slug}', [ProductControllerWeb::class, 'showProduct'])->name('productid');
    Route::get('/subgroup/{slug}', [ProductControllerWeb::class, 'showForSubgroup'])->name('subgroupid');
    Route::get('/category/{slug}', [ProductControllerWeb::class, 'showForCategory'])->name('categoryid');
    Route::get('/group/{slug}', [ProductControllerWeb::class, 'showForGroup'])->name('groupid');
    Route::get('/bays', [ProductControllerWeb::class, 'showBays'])->name('baysid');
    Route::post('/filter-products', [ProductControllerWeb::class, 'filterProducts']);
    Route::get('/search/{s}', [ProductControllerWeb::class, 'searchProducts'])->name('search-products');
    Route::get('/filter-products-search', [ProductControllerWeb::class, 'filterProductsSearch'])->name('filter-products-search');
    Route::get('/settingdolar', [SettingControllerWeb::class, 'index'])->name('settingdolar');
    Route::post('/pedidos', [OrderControllerWeb::class, 'store'])->name('pedidos.store');
    Route::post('/orders/{orderId}/send-confirmation', [OrderControllerWeb::class, 'sendConfirmation']);
    Route::get('/orders/{id}', [OrderControllerWeb::class, 'show']);
});

Route::middleware(['auth', 'verified'])->prefix('panel')->group(function () {

    Route::get('/', [PanelController::class, 'index'])->name('dashboard');

    Route::resource('pedidos', OrderController::class)->names('orders');
    Route::prefix('pedidos')->group(function () {
        Route::get('/load/orders', [OrderController::class, 'load'])->name('orders.load');
        Route::patch('/update/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('/update/order', [OrderController::class, 'updateOrder'])->name('orders.updateOrder');
    });

    Route::resource('productos', ProductController::class)->names('products');
    Route::prefix('productos')->group(function () {
        Route::get('/load/products', [ProductController::class, 'load'])->name('products.load');
        Route::post('/{id}/uploadImageChunk', [ProductController::class, 'uploadImageChunk'])->name('products.uploadImageChunk');
        Route::patch('/update/visibility', [ProductController::class, 'updateVisibilityOnStore'])->name('products.updateVisibility');
    });

    Route::resource('categorias', CategoryController::class)->names('categories');
    Route::prefix('categorias')->group(function () {
        Route::get('/buscar/{search}', [CategoryController::class, 'find'])->name('categories.find');

        Route::resource('{categoria}/grupos', GroupController::class)->names('categories.groups');

        Route::resource('{categoria}/grupos/{grupo}/subgrupos', SubGroupController::class)->names('categories.groups.subgroups');
    });

    Route::resource('filtros', FilterController::class)->names('filters');
    Route::prefix('filtros')->group(function () {
        Route::get('/buscar/{search}', [FilterController::class, 'find'])->name('filters.find');

        Route::resource('{filtro}/items', FilterItemsController::class)->names('filters.items');
    });

    Route::resource('banners', BannerController::class)->names('banners');
    Route::prefix('banners')->group(function () {
        Route::get('/buscar/{search}', [BannerController::class, 'find'])->name('banners.find');
    });

    Route::resource('configuraciones', SettingController::class)->names('settings');
    Route::prefix('configuraciones')->group(function () {
        Route::post('/storeTipoDeCambio', [SettingController::class, 'storeTipoDeCambio'])->name('settings.storeTipoDeCambio');
        Route::post('/storeImgSuperior', [SettingController::class, 'storeImgSuperior'])->name('settings.storeImgSuperior');
        Route::post('/storeImgSuperiorMobile', [SettingController::class, 'storeImgSuperiorMobile'])->name('settings.storeImgSuperiorMobile');
        Route::post('/storeImgMedio', [SettingController::class, 'storeImgMedio'])->name('settings.storeImgMedio');
        Route::post('/storeImgMedioMobile', [SettingController::class, 'storeImgMedioMobile'])->name('settings.storeImgMedioMobile');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
