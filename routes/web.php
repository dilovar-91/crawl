<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/crawler', [CrawlerController::class, 'index']);
Route::get('/tradein', [CrawlerController::class, 'parseTradein']);
Route::get('/parse-links', [CrawlerController::class, 'parse_links']);
Route::get('/parse', [CrawlerController::class, 'parse']);
Route::get('/milano', [CrawlerController::class, 'milano']);
Route::get('/milano-products', [CrawlerController::class, 'milano_products']);
Route::get('/milano-download', [CrawlerController::class, 'download_images']);
Route::get('/refresh_images', [CrawlerController::class, 'refresh_images']);
//Route::get('/set-color', [CrawlerController::class, 'setColor']);
Route::get('/export-csv', [CrawlerController::class, 'exportCsv']);
Route::get('/rename-turkish', [CrawlerController::class, 'renameTurkish']);
Route::get('/products', [CrawlerController::class, 'getProducts']);
Route::get('/set-color', [CrawlerController::class, 'setColor2']);
Route::get('/move-pic', [CrawlerController::class, 'movePic']);
