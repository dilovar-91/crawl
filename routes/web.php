<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\UnoTechnoController;
use App\Http\Controllers\UrbanController;
use App\Http\Controllers\HavalController;
use Intervention\Image\Facades\Image;


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
Route::get('/planeta-links', [CrawlerController::class, 'planeta_links']);
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

Route::get('/orekhvill-links', [CrawlerController::class, 'orekhvill']);
Route::get('/orekhvill-products', [CrawlerController::class, 'orekhvill_products']);
Route::get('/rolf-products', [CrawlerController::class, 'rolf_products']);
Route::get('/rolf', [CrawlerController::class, 'rolf2']);

//uno
Route::get('/uno-links', [UnoTechnoController::class, 'parse_links']);
Route::get('/uno-products', [UnoTechnoController::class, 'uno_products']);
Route::get('/uno-list', [UnoTechnoController::class, 'list']);//uno


Route::get('/quke-links', [UnoTechnoController::class, 'quke_links']);
Route::get('/quke-products', [UnoTechnoController::class, 'quke_products']);
Route::get('/quke-list', [UnoTechnoController::class, 'quke_list']);

Route::get('/autogermes-links', [UrbanController::class, 'autogermes']);

Route::get('/haval-index', [HavalController::class, 'index']);

Route::get('/autogermes-products', [UrbanController::class, 'autogermes_products']);
Route::get('/autogermes-iterate', [UrbanController::class, 'iterate']);



Route::get('/urban', [UrbanController::class, 'index']);
Route::get('/automir-links', [UrbanController::class, 'automir']);
Route::get('/automir-models', [UrbanController::class, 'models']);
Route::get('/automir-products', [UrbanController::class, 'automir_products']);


Route::get('addWatermark', function()
{

    $img_bg = Image::make(public_path('logo-orekhvill.jpg'));
    $img_mini = Image::make(public_path('middle.jpg'));
    //$img_bg->colorize(0, 30, 0);

    /* insert watermark at bottom-right corner with 10px offset */

    ///$files = Storage::disk('public')->files($directory);

   //$img->save(public_path('03/11b0.jpg'));
   //$img->save(storage_path('app/02/11b0.jpg'));

   $dir = public_path('02');
   $result = [];

     $files = File::files($dir);
     foreach($files as $f){
        $img = Image::make(public_path('02/'.$f->getRelativePathname()));
        $height = $img->height();
        if ($height>=300 && $height<=430){
            $img->insert($img_mini, 'bottom-right', 0, 0);
            $img->save(public_path('03/'. $f->getRelativePathname()));
        }
        /*
        else {
            $img->insert($img_bg, 'bottom-right', 0, 0);
        }
        */


     }
});

