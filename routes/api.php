<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CatProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FranquiciaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//TEST API
Route::get('/test', function () {
    return ["mensaje" => "estoy funcionando"];
});

//USER API
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::get('role',[AuthController::class, 'getUserRole']);
    Route::post('logout', [AuthController::class, 'logout']);

});

//LIST PRICE API

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::get('listaprecios', [AuthController::class, 'listaprecios']);

});

//CAROUSEL/MODAL API

Route::get('banner/carouselmovil', [BannerController::class, 'getBannersCarouselMovil']);
Route::get('banner/carousel', [BannerController::class, 'getBannersCarousel']);
Route::get('banner/{name}', [BannerController::class, 'getBannerCarouselByName']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('banner', [BannerController::class, 'setBanner']);
    Route::delete('banner/{id}', [BannerController::class, 'deleteBannerCarouselById']);

});

//POST API

Route::get('posts', [PostController::class, 'getPosts']);

Route::get('posts/{cant}', [PostController::class, 'getCantPosts']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('posts', [PostController::class, 'setPost']);
    Route::put('posts', [PostController::class, 'updatePost']);
    Route::delete('posts/{id}', [PostController::class, 'deletePostById']);

});

// Route::get('banner/modal', [BannerController::class, 'getBannerModal']);

// Route::get('banner/carousel', [BannerController::class, 'getBannersCarousel']);

// Route::get('banner/{name}', [BannerController::class, 'getBannerCarouselByName']);



/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */

// Route::group(['middleware' => ['auth:sanctum']], function() {

//     Route::get('role',[AuthController::class, 'getUserRole']);

//     Route::post('logout', [AuthController::class, 'logout']);

//     Route::get('listaprecios', [AuthController::class, 'listaprecios']);

//     Route::post('banner', [BannerController::class, 'setBanner']);

    // Route::post('/banner/{id}', [BannerController::class, 'updateBannerById']);

    // Route::put('/banner', [BannerController::class, 'updateBanner']);

    // Route::put('banner/carousel/{id}', [BannerController::class, 'updateBannerById']);

//     Route::delete('banner/carousel', [BannerController::class, 'deleteBannerCarouselById']);

// });

// Categoria Producto

Route::get('categoriaproductos', [CatProductoController::class, 'getCatProductos']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('categoriaproducto', [CatProductoController::class, 'setCatProducto']);
    Route::put('categoriaproducto/{id}', [CatProductoController::class, 'updateCatProducto']);
    Route::delete('categoriaproducto/{id}', [CatProductoController::class, 'deleteCatProductoByCat']);

});

Route::get('productos', [ProductoController::class, 'getProductos']);

Route::get('producto/{id}', [ProductoController::class, 'getProductoById']);

Route::get('imagenproducto/{name}',[ProductoController::class, 'getImagenProducto']);

Route::get('imagennutricional/{name}',[ProductoController::class, 'getImagenInfoNutricionalProducto']);

Route::get('tablanutricional/{name}',[ProductoController::class, 'getInfoNutricionalProducto']);

Route::get('productoporcategoria/{cat}', [ProductoController::class, 'getProductosByCat']);


Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('producto', [ProductoController::class, 'setProducto']);
    Route::put('producto/{id}', [ProductoController::class, 'updateProducto']);
    Route::delete('producto/{id}', [ProductoController::class, 'deleteProductoById']);

});


//Sucursales


Route::get('sucursales', [FranquiciaController::class, 'getSucursales']);

Route::get('imagensucursal/{id}', [FranquiciaController::class, 'getImagenSucursal']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('sucursales', [FranquiciaController::class, 'setSucursales']);
    Route::put('sucursales/{id}', [FranquiciaController::class, 'updateSucursales']);
    Route::delete('sucursales/{id}', [FranquiciaController::class, 'deleteSucursalesById']);

});
