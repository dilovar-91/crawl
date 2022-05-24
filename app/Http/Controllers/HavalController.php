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
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Link;
use GuzzleHttp\Client;

class HavalController extends Controller
{

    private $client;
    private $description;
    private $marks;

    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 100,
            'verify' => false

        ]);
        $this->description = '<p>Geely авто Краснодар<br />
        Гарантированная выгода при обмене вашего автомобиля!<br />
        Специальные предложения по кредитованию и страхованию!<br />
        Мы гарантируем юридическую чистоту автомобиля!<br />
        Есть возможность провести тест-драйв!<br />
        Автомобиль прошел комплексную предпродажную подготовку!<br />
        Мы уже более 10 лет занимается продажей новых автомобилей и с пробегом.&nbsp;<br />
        В наличии более 1500 автомобилей</p>';

    }








    public function index()

    {
        //$items = RolfProduct::where("id", '>', 0)->where('mark', '<>', 'Citroen')->where('mark', '<>', 'Peugeot')->where('mark', '<>', 'Jeep')->whereNotNull('price')->get();
        $items = RolfProduct::where('mark', 'geely')->get();

        //return count($items);


        $marks2 = array(
            'Audi' => '116',
            'BMW' => '117',
            'Chery' => '118',
            'Changan' => '119',
            'Ford' => '120',
            'Geely' => '121',
            'Honda' => '122',
            'Haval' => '123',
            'Hyundai' => '124',
            'Kia' => '125',
            'Lada' => '126',
            'Mazda' => '127',
            'Nissan' => '128',
            'Renault' => '129',
            'Skoda' => '159',
            'Volkswagen' => '131',
            'Uaz' => '174',
            'Suzuki' => '175',
            'Peugeot' => '176',
            'Mitsubishi' => '177',
            'Jeep' => '178',
            'Citroen' => '179'
        );
        foreach ($items as $row) {

            $id = null;
            //$files = Storage::disk('public')->files($row->link);
            $item = new EboardDb();
            $item->title = $row->name;
            $item->uid = $row->uid;
            $item->ip = mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);
            $item->cat = 18;
            $item->region = 37;
            $item->date_add = 1652791725;
            $item->uid = Str::uuid();
            $item->user_id = 10;
            $item->approved = 1;
            $item->active = 1;
            $item->status = 1;
            $item->save();
            $p = 0;
            $id = $item->id;


            if ($row->pictures !== null) {
                foreach ($row->pictures as $image) {
                    if ($image !== null) {
                        try {
                            $contents = file_get_contents( $image);
                            $image_name = Str::uuid() . '.png';
                            Storage::put("automir/" . $image_name, $contents);
                            //Storage::copy('public/' . $image, 'germes/' . $image_name);
                            DB::table('eboard_filter_val_files')->insert(['filter' => 6, 'msg' => $id, 'file' => $image_name, 'folder' => 10, 'sort' => $p]);
                            $p++;
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }
            //return ;

            DB::table('eboard_filter_val_int')->insert(['filter' => 62, 'msg' => $id, 'val' => $row->year]);
            $engine_volumes = array(
                "1.2" => 159,
                "1.3" => 160,
                "1.4" => 161,
                "1.5" => 162,
                "1.6" => 163,
                "1.8" => 164,
                "2.0" => 165,
                "2.2" => 166,
                "2.3" => 167,
                "2.4" => 168,
                "2.5" => 169,
                "2.7" => 170,
                "3.0" => 171,
                "3" => 171,
                "3.5" => 174,
                "3.6" => 172,
                "3.7" => 173
            );

            DB::table('eboard_filter_val_price')->insert(['filter' => 39, 'msg' => $id, 'val_user' => $row->price, 'val_default' => ($row->price - 300000), 'currency' => 1]);

           // DB::table('eboard_filter_val_set')->insert(['filter' => 76, 'msg' => $id, 'val' => $engine_volumes[$row->volume]]);
           // DB::table('eboard_filter_val_set')->insert(['filter' => 74, 'msg' => $id, 'val' => $row->korobka_id]);
           // DB::table('eboard_filter_val_set')->insert(['filter' => 73, 'msg' => $id, 'val' => $row->engine_id]);
          //  DB::table('eboard_filter_val_set')->insert(['filter' => 68, 'msg' => $id, 'val' => $row->privod_id]);
          /*  if ($row->body_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 67, 'msg' => $id, 'val' => $row->body_id]);
            }*/
            DB::table('eboard_filter_val_set')->insert(['filter' => 17, 'msg' => $id, 'val' => 42]);
            if ($row->mark_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 71, 'msg' => $id, 'val' => 121]);

            }
            if ($row->model_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 72, 'msg' => $id, 'val' => 23414]);
            }
            DB::table('eboard_filter_val_string')->insert(['filter' => 3, 'msg' => $id, 'val' => 'г. Краснодар', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 14, 'msg' => $id, 'val' => '+7 (861) 211-43-51', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 40, 'msg' => $id, 'val' => '45.04080382964087,38.97723485777147,9', 'ind_hash' => Str::uuid()]);

            DB::table('eboard_filter_val_string')->insert(['filter' => 75, 'msg' => $id, 'val' => 184, 'ind_hash' => Str::uuid()]);
           // DB::table('eboard_filter_val_string')->insert(['filter' => 77, 'msg' => $id, 'val' => "Белый", 'ind_hash' => Str::uuid()]);
           // if ($row->equipment !== null) {
            //    DB::table('eboard_filter_val_string')->insert(['filter' => 78, 'msg' => $id, 'val' => 'Elite', 'ind_hash' => Str::uuid()]);
            //}
            //DB::table('eboard_filter_val_string')->insert(['filter' => 16, 'msg' => $id, 'val' => "https://autocentr.su/", 'ind_hash' => Str::uuid()]);


            DB::table('eboard_filter_val_text')->insert(['filter' => 0, 'msg' => $id, 'val' => $this->description, 'ind' => $row->name, 'ind_hash' => Str::uuid()]);

            DB::table('eboard_ind_cat')->insert(['message' => $id, 'cat' => 1]);
            DB::table('eboard_ind_cat')->insert(['message' => $id, 'cat' => 18]);

            DB::table('eboard_ind_region')->insert(['message' => $id, 'cat' => 37]);
            //DB::table('eboard_orders')->insert(['message_id' =>  $id, 'time_end' => 1683197806, 'days' => 365, 'status' => 1, 'type' => 'r', 'user_id' => 4]);


            unset($item);
            //return;

        }

    }



    public function haval_products()
    {
        $links = Link::where('id', '>', 1477)->get();
        foreach ($links as $link) {
            $response = $this->client->get($link->link); // URL, where you want to fetch the content


            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;
            $name = $crawler->filter('h1')->text();
            $name = mb_substr($name, 0, -3);
            try {
                $description = $crawler->filter("div.table-responsive")->outerHtml();
            } catch (Exception $e) {
                $description = null;
            }
            try {
                $mark = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(0)->text();
            } catch (Exception $e) {
                $mark = null;
            }

            try {
                $model = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(1)->text();
            } catch (Exception $e) {
                $model = null;
            }
            try {
                $complectation = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(2)->text();
            } catch (Exception $e) {
                $complectation = null;
            }
            try {
                $year = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(3)->text();
            } catch (Exception $e) {
                $year = null;
            }
            try {
                $privod = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(10)->text();
            } catch (Exception $e) {
                $privod = null;
            }
            $privods = array(
                "Передний" => 91,
                "Задный" => 92,
                "Полный" => 93
            );
            try {
                $color = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(4)->text();
            } catch (Exception $e) {
                $color = null;
            }
            $bodies = array(
                "Седан" => 1,
                "Хэтчбек" => 2,
                "Универсал" => 87,
                "Кроссовер" => 143,
                "Внедорожник" => 88,
                "Кабриолет" => 89,
                "Минивэн" => 144,
                "Купе" => 144,
                "Фургон" => 144
            );
            $dvig = array(
                "Бензин" => 145,
                "Дизель" => 146,
                "Гибрид" => 147,
                "Электро" => 143,
            );

            try {
                $kuzov = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(5)->text();
            } catch (Exception $e) {
                $kuzov = null;
            }
            try {
                $power = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(13)->text();
            } catch (Exception $e) {
                $power = null;
            }
            try {
                $vol = round($crawler->filter("div.options")->filter('div.col-lg-7')->eq(11)->text() / 1000, 1);
            } catch (Exception $e) {
                $vol = null;
            }
            try {
                $engine_type = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(9)->text();
            } catch (Exception $e) {
                $engine_type = null;
            }
            try {
                $korobka = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(12)->text();
            } catch (Exception $e) {
                $korobka = null;
            }

            try {
                $price = (int)filter_var($crawler->filter("span.new-car-info__price b")->text(), FILTER_SANITIZE_NUMBER_INT);
            } catch (Exception $e) {
                $price = 0;
            }


            $img = $crawler->filter('div.sale-new-car')->filter('div.lazy-img')->attr('data-src');
            $images = array($img);
            foreach ($images as $image) {
                if ($image !== null) {
                    try {
                        $contents = file_get_contents('https://www.avtogermes.ru' . $image);
                        $filename = substr($image, strrpos($image, '/') + 1);
                        Storage::put('restotest/' . $filename, $contents);
                    } catch (\InvalidArgumentException $e) {
                        continue;
                    }
                }
            }
            $product = new RolfProduct();
            $product->name = $name;
            $product->pictures = $images;
            $product->mark = ucfirst($mark ?? $link->category);
            $product->model = $model;
            $product->link = $link->link;
            $product->owner = 0;
            $product->year = $year;
            $product->price = $price;
            $product->engine = $engine_type;
            $product->milage = 0;
            $product->korobka = $korobka;
            $product->color_name = $color;
            $product->description = $description;
            $product->privod = $privod;
            $product->privod_id = $privods[$privod] ?? 0;
            $product->engine_id = $dvig[$engine_type] ?? 0;
            $product->volume = $vol;
            $product->equipment = $complectation;
            $product->power = $power;
            $product->body = $kuzov;
            $product->body_id = $bodies[$kuzov] ?? 0;
            $product->uid = Str::uuid();
            $product->save();
            sleep(3);
        }
    }

    public function str_replace_last($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = mb_strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
}
