<?php

namespace App\Http\Controllers;

use App\Models\AutomirMark;
use App\Models\AutomirModel;
use App\Models\CarBody;
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
    private $marks;

    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client(['timeout' => 100,
            'verify' => false, 'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36']]);
        $this->description = '<p>Chery центр Юг<br />
        Дилерский центр предлагает полный спектр услуг по продаже и обслуживанию автомобилей марки Chery: продажа новых автомобилей, услуги кредитования и страхования, продажа и установку дополнительного оборудования и аксессуаров, гарантийное и постгарантийное сервисное обслуживание.<br />
        Клиенты автоцентра могут ознакомиться с актуальным модельным рядом автомобилей в шоу-руме, получить квалифицированную консультацию, провести тест-драйв интересующего автомобиля.</p>

        <p>В автоцентре Вы можете:<br />
        - Купить автомобиль Chery за наличные или в кредит;<br />
        - Обменять свой автомобиль на новый Chery по системе Trade In;<br />
        - Купить полис ОСАГО или КАСКО на выгодных условиях;<br />
        - Пройти сервисное обслуживание или ремонт любой сложности;<br />
        - Купить или заказать запасные части или аксессуары Chery;<br />
        - Приобрести и установить любое дополнительное оборудование.</p>

        <p>Бонусы при покупке:<br />
        - Рассрочка 0%.<br />
        - Сигнализация в подарок</p>
        ';
        $this->marks = array(
            'BMW' => '117',
            'Chery' => '34',
            'Changan' => '32',
            'Ford' => '63',
            'Geely' => '67',
            'Honda' => '76',
            'Haval' => '290',
            'Hyundai' => '79',
            'Kia' => '92',
            'Lada' => '215',
            'Mazda' => '123',
            'Nissan' => '127',
            'Renault' => '147',
            'Skoda' => '159',
            'Volkswagen' => '184',
            'Uaz' => '227',
            'Suzuki' => '167',
            'Peugeot' => '135',
            'Mitsubishi' => '123',
            'Jeep' => '89',
            'Citroen' => '37'
        );
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
        //$cars = RolfProduct::where('mark', '<>', 'Citroen')->where('mark', '<>', 'Peugeot')->where('mark', '<>', 'Jeep')->get();
        $cars = RolfProduct::where('mark', 'Kia')->get();
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
        //$items = RolfProduct::where("id", '>', 0)->where('mark', '<>', 'Citroen')->where('mark', '<>', 'Peugeot')->where('mark', '<>', 'Jeep')->whereNotNull('price')->get();
        $items = RolfProduct::where('mark', 'Chery')->get();


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
            $item->user_id = 8;
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
                            DB::table('eboard_filter_val_files')->insert(['filter' => 6, 'msg' => $id, 'file' => $image_name, 'folder' => 8, 'sort' => $p]);
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
            DB::table('eboard_filter_val_string')->insert(['filter' => 3, 'msg' => $id, 'val' => 'г. Краснодар', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 14, 'msg' => $id, 'val' => '+7 (861) 211-43-52', 'ind_hash' => Str::uuid()]);
            DB::table('eboard_filter_val_string')->insert(['filter' => 40, 'msg' => $id, 'val' => '45.04080382964087,38.97723485777147,9', 'ind_hash' => Str::uuid()]);

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

    public function automir_marks()
    {

        $url = 'https://avtomir.ru/';




            $response = $this->client->get($url); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $_this = $this;


            $data = $crawler->filter('a.carmodels__item')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node;
                }
                );
            //return $data;

            foreach ($data as $row) {
                $slug = $this->getBetween($row->attr('href'), 'new-cars/', '/' );
                $image =  $row->filter('img.carmodels__img-color')->attr('src');
                $image_grey =  $row->filter('img.carmodels__img-grey')->attr('src');

                try {
                    $contents1 = file_get_contents('https://avtomir.ru' . $image_grey);
                    $filename_grey = substr($image, strrpos($image_grey, '/') + 1);
                    Storage::put('mark_logos/' . $slug . '_grey.png', $contents1);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }
                try {
                    $contents = file_get_contents('https://avtomir.ru' . $image);
                    $filename = substr($image, strrpos($image, '/') + 1);
                    Storage::put('mark_logos/' . $slug . '.png', $contents);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }
                $link = new AutomirMark();
                $link->name = $row->filter('div.carmodels__name')->text();
                $link->slug = $slug;
                $link->logo = $slug . '.png';
                $link->logo_grey = $slug . '_grey.png';
                $link->link = "https://avtomir.ru" . $row->attr('href');
                $link->save();
            }

    }


    public function automir_models()
    {

        $url = 'https://avtomir.ru/new-cars/';
        $marks = AutomirMark::where('active', 1)->get();
        foreach ($marks as $item) {

            $response = $this->client->get($item->link); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $_this = $this;


            $data = $crawler->filter('div.category__list')->filter('div.card')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node;
                }
                );
            //return $data;

            foreach ($data as $row) {
                $slug =  Str::slug(substr($row->attr('data-href'), strpos($row->attr('data-href'), ($item->slug.'/'))+ strlen($item->slug.'/')));
                //return $slug;
                $tmp = $row->filter('div.card__text-links a')->attr('href');
                $body_slug = substr($tmp, strpos($tmp, "#") + 1);
                //return $body_slug;
                $body = CarBody::firstOrNew(['name'=>$row->filter('a.card__text-link')->text()], ['slug'=>$body_slug]);
                $body->save();

                $image = $row->filter('div.card__img img')->attr('data-src');
                try {
                    $contents = file_get_contents('https://avtomir.ru' . $image);
                    $filename = $item->slug.'_'.Str::snake($slug) . '.png';
                    Storage::put('model_images/' . $item->slug.'_'.Str::snake($slug) . '.png', $contents);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                $str = new CarModel();
                $str->name =  substr($row->filter('a.card__name')->text(), strpos($row->filter('a.card__name')->text(), " ") + 1);
                $str->slug = $slug;
                $str->picture = $filename;
                try{
                    $str->year = preg_replace('/^[^\d]*(\d{4}).*$/', '\1',$row->filter('div.card__date')->text());
                } catch (\InvalidArgumentException $e) {
                    //continue;
                }

                $str->body_id = $body->id;
                $str->mark_id = $item->id;
                $str->link = "https://avtomir.ru" . $row->attr('data-href');
                $str->save();

            }


        }
    }

    public function automir_complectations()
    {


        $models = AutomirModel::get();
        foreach ($models as $item) {

            $response = $this->client->get($item->link); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            $_this = $this;


            $data = $crawler->filter('a.category__list_bordered-link')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node;
                }
                );
            //return $data;

            foreach ($data as $row) {
                $slug =  Str::slug($this->getBetween($row->attr('href'), $item->slug . '/', '/'));
                return $slug;
                $tmp = $row->filter('div.card__text-links a')->attr('href');
                $body_slug = substr($tmp, strpos($tmp, "#") + 1);
                //return $body_slug;
                $body = CarComplectation::firstOrNew(['name'=>$row->filter('a.card__text-link')->text()], ['slug'=>$body_slug]);
                $body->save();

                $image = $row->filter('div.card__img img')->attr('data-src');
                try {
                    $contents = file_get_contents('https://avtomir.ru' . $image);
                    $filename = $item->slug.'_'.Str::snake($slug) . '.png';
                    Storage::put('model_images/' . $item->slug.'_'.Str::snake($slug) . '.png', $contents);
                } catch (\InvalidArgumentException $e) {
                    continue;
                }

                $str = new CarModel();
                $str->name =  substr($row->filter('a.card__name')->text(), strpos($row->filter('a.card__name')->text(), " ") + 1);
                $str->slug = $slug;
                $str->picture = $filename;
                try{
                    $str->year = preg_replace('/^[^\d]*(\d{4}).*$/', '\1',$row->filter('div.card__date')->text());
                } catch (\InvalidArgumentException $e) {
                    //continue;
                }

                $str->body_id = $body->id;
                $str->mark_id = $item->id;
                $str->link = "https://avtomir.ru" . $row->attr('data-href');
                $str->save();

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
        $links = Link::where('id', '>', 437)->get();
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

    function getBetween($string, $start = "", $end = "")
    {
        if (strpos($string, $start)) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return str_replace("-", " ", substr($firstSubStr, 0, $endCharCount));
        } else {
            return '';
        }
    }
}
