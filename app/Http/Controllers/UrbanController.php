<?php

namespace App\Http\Controllers;

use App\Models\CarModel;
use App\Models\RolfProduct;
use App\Models\EboardDb;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Link;
use GuzzleHttp\Client;

class UrbanController extends Controller
{

    private $client;

    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 100,
            'verify' => false
        ]);
    }

    public function index()
    {
        $items = RolfProduct::where("id", '>', 5)->get();
        foreach ($items as $row) {
            // = preg_split('/[\s,]+/', $row->name, 3);
            $id = null;
            $files = Storage::disk('public')->files($row->link);
            //return $row->name;

            //$car_name = $arr[0] . ' ' . $arr[1] . ', ' . $row->year;
            //return  $car_name;
            $item = new EboardDb();
            $item->title = $row->name;
            $item->uid = $row->uid;
            $item->ip =  mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);
            $item->cat = 18;
            $item->region = 3;
            $item->date_add = 1651752904;
            $item->uid = Str::uuid();
            $item->user_id = 2;
            $item->approved = 1;
            $item->status = 1;
            $item->save();
            $p = 0;
            $id =  $item->id;



            foreach ($files as $image) {
                //if ($p > 6) {
                //    break;
               // }
                if ($image !== null) {
                    try {
                        //$contents = file_get_contents($image);
                        $image_name = Str::uuid() . '.jpg';
                        //Storage::put("rest/" . $image_name, $contents);
                        Storage::copy('public/' . $image, 'rest_test/' . $image_name);
                        DB::table('eboard_filter_val_files')->insert(['filter' => 6, 'msg' =>  $id, 'file' => $image_name, 'sort' => $p]);
                        $p++;
                    } catch (\InvalidArgumentException $e) {
                        continue;
                    }
                }
            }
            DB::table('eboard_filter_val_int')->insert(['filter' => 62, 'msg' =>  $id, 'val' => $row->year]);
            //DB::table('eboard_filter_val_int')->insert(['filter' => 52, 'msg' => $item->id, 'val' => $row->milage]);

            DB::table('eboard_filter_val_price')->insert(['filter' => 39, 'msg' =>  $id, 'val_user' => $row->price, 'val_default' => $row->price, 'currency' => 1]);

            DB::table('eboard_filter_val_set')->insert(['filter' => 17, 'msg' =>  $id, 'val' => 42]);
            //DB::table('eboard_filter_val_set')->insert(['filter' => 67, 'msg' => $item->id, 'val' => 85]);

            DB::table('eboard_filter_val_string')->insert(['filter' => 3, 'msg' =>  $id, 'val' => 'Москва, Алтуфьевское шоссе, 31, стр.8', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 14, 'msg' =>  $id, 'val' => '+7 (495) 161-34-70', 'ind_hash' => Str::uuid()]);


            DB::table('eboard_filter_val_text')->insert(['filter' => 0, 'msg' =>  $id, 'val' => $row->description ?? $row->engine, 'ind' => $row->name, 'ind_hash' => Str::uuid()]);

            DB::table('eboard_ind_cat')->insert(['message' =>  $id, 'cat' => 1]);
            DB::table('eboard_ind_cat')->insert(['message' =>  $id, 'cat' => 18]);
            DB::table('eboard_ind_cat')->insert(['message' =>  $id, 'cat' => 151]);

            DB::table('eboard_ind_region')->insert(['message' =>  $id, 'cat' => 3]);

            DB::table('eboard_orders')->insert(['message_id' =>  $id, 'time_end' => 1683197806, 'days' => 365, 'status' => 1, 'type' => 'r', 'user_id' => 3]);
            //return;

            unset($item);

        }

    }


    public function automir()
    {

        $url = 'https://avtomir.ru/new-cars/';
        $pages = array(
            "kia",
            "hyundai",
            "nissan",
            "renault",
            "chery",
            "volkswagen",
            "skoda",
            "haval",
            "geely",

        );
        for ($l = 1; $l < count($pages); $l++) {


            $response = $this->client->get($url . $pages[$l] . '/'); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $_this = $this;


            $data = $crawler->filter('div.card')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node->filter('a.card__name')->attr('href');
                }
                );
            //return $data;

            foreach ($data as $row) {

                $link = new Link();
                $link->link = "https://avtomir.ru" . $row;
                $link->category = $pages[$l];
                $link->save();
            }


        }
    }

    public function models()
    {

        $models = Link::get();
        foreach ($models as $model) {
            $response = $this->client->get($model->link, ['headers' => ['User-Agent' => 'YandexBot']]); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $data = $crawler->filter('div.card')->each(function (Crawler $node, $i) {
                return $node;
            }
            );
            foreach ($data as $row) {
                $year = (int)filter_var($row->filter('div.card__date')->text(), FILTER_SANITIZE_NUMBER_INT);
                $price = (int)filter_var($row->filter('span.card__price-num')->text(), FILTER_SANITIZE_NUMBER_INT);
                $engine = $row->filter('div.card__text')->eq(0)->text();
                //return $row->attr('data-href');
                $link = new CarModel();
                $link->link = $row->attr('data-href');
                $link->group_id = $year;
                $link->engine = $engine;
                $link->price = $price ?? 0;
                $link->category = $model->category;
                $link->save();
            }

        }

    }


    public function automir_products()
    {
        $links = CarModel::where('id', '>', 855)->get();
        //return $links;
        foreach ($links as $link) {

            $response = $this->client->get('https://avtomir.ru' . $link->link); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;

            $name = $crawler->filter('h1')->text();

            try {
                $specifications = $crawler->filter("div.product-page__specifications")->outerHtml();
            } catch(Exception $e) {

                $specifications = null;
            }

           // $price = (int)filter_var($crawler->filter("span.card__price-num")->text(), FILTER_SANITIZE_NUMBER_INT);

            $korobka = $crawler->filter('div.product-page__params-group')->eq(0);
            $korobka = mb_substr($korobka->filter('li.product-page__params-item')->eq(2)->text(), 8);



            $images = $crawler->filter('a.product-page__slider-item')->each(function (Crawler $node, $i) {
                return $node->attr('href');
            }
            );

            foreach ($images as $image) {

                if ($image !== null) {
                    try {
                        $contents = file_get_contents($image);
                        $filename = substr($image, strrpos($image, '/') + 1);
                        Storage::put($link->link . $filename, $contents);
                    } catch (\InvalidArgumentException $e) {
                        continue;
                    }
                }
            }

            $product = new RolfProduct();
            $product->name =  $name;
            $product->pictures = $images;
            $product->mark = $link->category;
            $product->link = $link->link;
            $product->owner = 0;
            $product->year = $link->group_id;
            $product->price = $link->price;
            $product->engine = $link->engine;
            $product->milage = 0;
            $product->korobka = $korobka;
            $product->description = $specifications;
            $product->uid = Str::uuid();
            $product->save();
        }
    }
}
