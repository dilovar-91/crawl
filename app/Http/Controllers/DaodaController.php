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

class DaodaController extends Controller
{

    private $client;
    private $description;
    private $marks;
    private $transmissions;

    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 100,
            'verify' => false

        ]);
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

        $this->transmissions= array(
           'Access' => '1',
           'ACTIVE LONG' => '2',
           'Active Plus' => '3',
           'ACTIVE STANDART' => '4',
           'ALLURE' => '5',
           'Base' => '6',
           'Business' => '7',
           'Classic' => '8',
           'Comfort' => '9',
           'Comfort +Winter' => '10',
           'COMFORT N (19)' => '11',
           'COSMO' => '12',
           'Drive' => '13',
           'Elegance+Prestige+Safety' => '14',
           'Elite' => '15',
           'Exclusive' => '16',
           'Family' => '17',
           'Family Plus' => '18',
           'FEEL' => '19',
           'FEEL Edition' => '20',
           'FEEL M' => '21',
           'FEEL XL' => '22',
           'FLAGSHIP' => '23',
           'Fourgon' => '24',
           'GL' => '25',
           'GLX(CD)' => '26',
           'GT' => '27',
           'High-Tech +Exclusive' => '28',
           'HIGHLINE' => '29',
           'Instyle' => '30',
           'Instyle Yandex' => '31',
           'Intense' => '32',
           'Intense+' => '33',
           'Intense+ Yandex' => '34',
           'Invite' => '35',
           'Life' => '36',
           'Lifestyle' => '37',
           'Lifestyle + Smart Sense' => '38',
           'LIMITED' => '39',
           'Luxe' => '40',
           'Luxe EnjoY Pro' => '41',
           'Luxe EnjoY Pro/Prestige' => '42',
           'Luxe/LADA Connect' => '43',
           'OVERLAND' => '44',
           'Premium' => '45',
           'Premium+' => '46',
           'Prestige' => '47',
           'Prestige (2-tone color)' => '48',
           'PRESTIGE 1.5T' => '49',
           'Prestige+Smart Sense' => '50',
           'Prime' => '51',
           'SHINE' => '52',
           'SHINE Ultimate' => '53',
           'STEPWAY DRIVE' => '54',
           'Style' => '55',
           'Style TCe 150' => '56',
           'Style+Smart Sense' => '57',
           'Ultimate' => '58'
        );
    }

    public function getModelId($model, $mark)
    {

        if($mark !== null){
        $car = Model2::where('name','like',  '%'.$model. '%')->first();
        if (empty($car)) {
            return null;
        }
        return $car->id;
        }

    }

    public function links()
    {

        $url = 'https://www.avtogermes.ru/sale/';
        $pages = array(
            array("kia", 48),
            array("hyundai", 13),
            array("chery", 5),
            array("suzuki", 4),
            array("jeep", 3),
            array("mitsubishi", 6),
            array("renault", 9),
            array("peugeot", 4),
            array("citroen", 4),
            array("lada", 52),
            array("uaz", 5)
        );
        for ($l = 0; $l < count($pages); $l++) {


           // for ($p = 1; $p <= $pages[$l][1]; $p++) {

                $uri = $url . $pages[$l][0];
               // return $uri;


                $response = $this->client->get($uri); // URL, where you want to fetch the content


                // get content and pass to the crawler
                $content = $response->getBody()->getContents();
                $crawler = new Crawler($content);


                $data = $crawler->filter('div.cars')->filter('div.pb-col')
                    ->each(function (Crawler $node) {
                        return $node->filter('a')->eq(1)->attr('href');
                    }
                    );
                //return $data;

                foreach ($data as $row) {
                    $model = $this->getBetween($row, $pages[$l][0]."/",  "/");
                    $res = CarModel::where('name', 'like', $model.'%')->first();
                    if (isset($res)){
                        $model_id = $res->id;
                    }
                    else {
                        $set = New CarModel();
                        $set->name = $model;
                        $set->mark_id = $l+1;
                        $set->slug = strtolower($model);
                        $set->save();
                        $model_id = $set->id;
                    }
                    //return $model;
                    $link = new Link();
                    $link->link = "https://www.avtogermes.ru" . $row;
                    $link->mark_id = $l+1;
                    $link->model_id = $model_id;
                    $link->category = $pages[$l][0];
                    $link->save();
                    unset($set);
                    //return;
                }


            //}

            sleep(15);


        }
    }

    public function daoda_cars()
    {
        $links = Link::where('id', '>', 0)->get();
        foreach ($links as $link) {
            $response = $this->client->get($link->link); // URL, where you want to fetch the content


            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;
            $name = $crawler->filter('h1')->text();
            try {
                $str1 = $crawler->filter('i.ag-icon-rashod')->nextAll();
                $raskhod =  floatval($this->getBetween($str1->text(), "топлива",  "л/"));
            }catch (Exception $e) {
                $raskhod = null;
            }
            try {
                $str1 = $crawler->filter('i.ag-icon-rashod')->nextAll();
                $raskhod =  floatval($this->getBetween($str1->text(), "топлива",  "л/"));
            }catch (Exception $e) {
                $raskhod = null;
            }



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
                "Передний" => 1,
                "Задный" => 2,
                "Полный" => 3
            );
            try {
                $color = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(4)->text();
            } catch (Exception $e) {
                $color = null;
            }
            try {
                $tkan = $crawler->filter("div.options")->filter('div.col-lg-7')->eq(5)->text();
            } catch (Exception $e) {
                $tkan = null;
            }
            $bodies = array(
                "Седан" => 1,
                "Хэтчбек" => 2,
                "Универсал" => 3,
                "Кроссовер" => 4,
                "Внедорожник" => 5,
                "Кабриолет" => 6,
                "Минивэн" => 7,
                "Пикап" => 8,
                "Купе" => 9,
                "Фургон" => 10
            );
            $dvig = array(
                "Бензин" => 1,
                "Дизель" => 2,
                "Гибрид" => 3,
                "Электро" => 4,
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
                        Storage::put('daoda/' . $filename, $contents);
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
            return;
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

    public function str_replace_last($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = mb_strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
}
