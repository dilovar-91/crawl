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

class UrbanController extends Controller
{

    private $client;
    private $description;

    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 100,
            'verify' => false

        ]);
        $this->description = '<p><span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">ABТОМОБИЛИ ПPOДАЮТСЯ ТOЛЬКO В КРEДИТ!!!</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">CTОИMOCTЬ ABТОМОБИЛЯ УKAЗАНA ПО ПPOГPAМME KPЕДИTOBAНИЯ!!!</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">Уникaльныe условия покупки aвтoмобиля в АЦ АВАНГАРД</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">Для пoлучение пpeдвaритeльногo рeшeния по кредиту:</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">1. Запoлнитe онлайн-заявку на сайте автоцентра &mdash; Вам не нужно посещать автосалон;</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">2. Узнайте решения банков и одобренные программы кредитования;</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">3. При положительном решении &mdash; посетите автосалон.</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Первоначальный взнос от 0%;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Кредит по двум документам;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Возможность досрочного погашения с 1 месяца;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Одобрение кредита за 1 час и выдача автомобиля день в день;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Льготные программы автокредита, более 30 банков партнеров;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Лучшие тарифы КАСКО, ОСАГО;</span><br />
<br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Покупка автомобиля за 1 час;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Каждый автомобиль проходит комплексную диагностику более чем по 140 пунктам;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Гарантия юридической чистоты на каждый автомобиль с пробегом;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Специальные условия при обмене своего автомобиля и покупке автомобиля в кредит;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">+ Скидка до 100 000 рублей при покупке с Тrаdе-In;</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">Мы всегда рады видеть Вас в нашем автоцентре.</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">Работаем для Вас ежедневно с 8:00 до 20:00.</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">Ждем Вас по адресу: г. Краснодар, Ростовское шоссе, 34/10</span><br />
<br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Тонированные стекла</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Стальные диски</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Защита картера</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Электрообогрев лобового стекла</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Крепление для детского кресла (Isofix/LATCH)</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Третий задний подголовник</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Передний центральный подлокотник</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Сигнализация с обратной связью</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Блокировка замков задних дверей</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Штатная аудиоподготовка</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Темный салон</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Сиденье водителя: ручная регулировка</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Сиденье пассажира: ручная регулировка</span><br />
<span style="background-color:rgb(255, 255, 255); font-family:arial,helvetica neue,helvetica,sans-serif; font-size:16px">- Регулировка руля</span></p>
';
    }

    public function iterate()
    {
        $marks = array(
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
            'Skoda' => '130',
            'Volkswagen' => '131',
            'Uaz' => '174',
            'Suzuki' => '175',
            'Peugeot' => '176',
            'Mitsubishi' => '177',
            'Jeep' => '178',
            'Citroen' => '179'
        );
        $cars = RolfProduct::where('mark', '<>', 'Citroen')->where('mark', '<>', 'Peugeot')->where('mark', '<>', 'Jeep')->get();
        foreach ($cars as $row) {
            $item = RolfProduct::where('id', $row->id)->first();
            if (!empty($item)){
                $item->model_id  =  $this->getModelId($row->model, $row->mark_id);
                $item->save();
            }
            else {
                continue;
            }

            //$item->mark_id = $marks[$row->mark];

        }

    }

    public function getModelId($model, $mark)
    {

        if($mark !== null){
        $car = CarModel::where('name','like',  '%'.$model. '%')->first();
        if (empty($car)) {
            return null;
        }
        return $car->id;
        }

    }


    public function index()

    {
        $items = RolfProduct::where("id", '>', 0)->where('mark', '<>', 'Citroen')->where('mark', '<>', 'Peugeot')->where('mark', '<>', 'Jeep')->whereNotNull('price')->get();


        $marks = array(
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
            'Skoda' => '130',
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
            $item->user_id = 6;
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
                            $contents = file_get_contents('https://www.avtogermes.ru' . $image);
                            $image_name = Str::uuid() . '.png';
                            Storage::put("germes/" . $image_name, $contents);
                            //Storage::copy('public/' . $image, 'germes/' . $image_name);
                            DB::table('eboard_filter_val_files')->insert(['filter' => 6, 'msg' => $id, 'file' => $image_name, 'sort' => $p]);
                            $p++;
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }

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

            DB::table('eboard_filter_val_set')->insert(['filter' => 76, 'msg' => $id, 'val' => $engine_volumes[$row->volume]]);
            DB::table('eboard_filter_val_set')->insert(['filter' => 74, 'msg' => $id, 'val' => $row->korobka_id]);
            DB::table('eboard_filter_val_set')->insert(['filter' => 73, 'msg' => $id, 'val' => $row->engine_id]);
            DB::table('eboard_filter_val_set')->insert(['filter' => 68, 'msg' => $id, 'val' => $row->privod_id]);
            if ($row->body_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 67, 'msg' => $id, 'val' => $row->body_id]);
            }
            DB::table('eboard_filter_val_set')->insert(['filter' => 17, 'msg' => $id, 'val' => 42]);
            if ($row->mark_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 71, 'msg' => $id, 'val' => $row->mark_id]);

            }
            if ($row->model_id > 0) {
                DB::table('eboard_filter_val_set')->insert(['filter' => 72, 'msg' => $id, 'val' => $row->model_id]);
            }
            DB::table('eboard_filter_val_string')->insert(['filter' => 3, 'msg' => $id, 'val' => 'г. Краснодар, Ростовское шоссе, 34/10', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 14, 'msg' => $id, 'val' => '8 (861) 211-49-46', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 40, 'msg' => $id, 'val' => '45.11354364152209,38.996480601316904,17', 'ind_hash' => Str::uuid()]);

            DB::table('eboard_filter_val_string')->insert(['filter' => 75, 'msg' => $id, 'val' => $row->power, 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 77, 'msg' => $id, 'val' => $row->color_name, 'ind_hash' => Str::uuid()]);
            if ($row->equipment !== null) {
                DB::table('eboard_filter_val_string')->insert(['filter' => 78, 'msg' => $id, 'val' => $row->equipment, 'ind_hash' => Str::uuid()]);
            }
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

    public function autogermes()
    {

        $url = 'https://www.avtogermes.ru/sale/new/';
        $pages = array(
            array("kia", 48),
            array("lada", 54),
            array("hyundai", 12),
            array("chery", 5),
            array("suzuki", 4),
            array("jeep", 3),
            array("mitsubishi", 6),
            array("renault", 20),
            array("peugeot", 4),
            array("citroen", 4),
            array("uaz", 6)
        );
        for ($l = 0; $l < count($pages); $l++) {


            for ($p = 1; $p <= $pages[$l][1]; $p++) {

                $uri = $url . $pages[$l][0] . '?page=' . $p;
                //echo $uri;


                $response = $this->client->get($uri); // URL, where you want to fetch the content


                // get content and pass to the crawler
                $content = $response->getBody()->getContents();
                $crawler = new Crawler($content);


                $data = $crawler->filter('div.pb-col')
                    ->each(function (Crawler $node) {
                        return $node->filter('a')->attr('href');
                    }
                    );
                //return $data;

                foreach ($data as $row) {

                    $link = new Link();
                    $link->link = "https://www.avtogermes.ru" . $row;
                    $link->category = $pages[$l][0];
                    $link->save();
                }


            }

            sleep(15);


        }
    }

    public function autogermes_products()
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
                "Седан" => 85,
                "Хэтчбек" => 86,
                "Универсал" => 87,
                "Кроссовер" => 143,
                "Внедорожник" => 88,
                "Кабриолет" => 89,
                "Минивэн" => 144
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
        $links = CarModel::where('id', '>', 1429)->get();
        //return $links;
        foreach ($links as $link) {

            $response = $this->client->get('https://avtomir.ru' . $link->link); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;


            $name = $crawler->filter('h1')->text();
            $name = mb_substr($name, 0, -4);
            return $name;

            try {
                $specifications = $crawler->filter("div.product-page__specifications")->outerHtml();
            } catch (Exception $e) {

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
            $product->name = $name;
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

    public function str_replace_last($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = mb_strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
}
