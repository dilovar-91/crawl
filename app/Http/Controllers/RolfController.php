<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception\InvalidOrderException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Exception;
use App\Models\Link;
use App\Models\Product;
use App\Models\Attachment;
use App\Models\MilanoProduct;
use App\Models\OrekhvillProduct;
use App\Models\RolfProduct;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class RolfController extends Controller
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


    public function maximum_links()
    {

        $url = 'https://maximum-auto.ru/in-stock/?page=';


        for ($i = 1; $i <= 148; $i++) {


            $response = $this->client->get($url . $i . '#stock'); // URL, where you want to fetch the content

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            //$mark = $this->string_between_two_string($pages[$l][0], 'cars/', '/page');

            $_this = $this;


            $data = $crawler->filter('.styles_card__2wi_I a')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node->attr('href');
                }
                );
            //return $data;

            foreach ($data as $row) {
                $link = new Link();
                $link->link = "https://maximum-auto.ru" . $row;
                //$link->category = $mark;
                $link->save();
            }


        }
    }

    public function links()
    {
        $url = 'https://rolf-probeg.ru/spb/cars/page/';
        for ($i = 1; $i <= 108; $i++) {

            $response = $this->client->get($url . $i . '/?dealer%5B0%5D=17'); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            //$mark = $this->string_between_two_string($pages[$l][0], 'cars/', '/page');
            $_this = $this;


            $data = $crawler->filter('a.card-car')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node->attr('href');
                }
                );
            //return $data;

            foreach ($data as $row) {
                $link = new Link();
                $link->link = "https://rolf-probeg.ru" . $row;
                //$link->category = $mark;
                $link->save();
            }


        }
    }


    public function rest()
    {
        $url = 'https://www.bips.ru/dilers/rolf-severo-zapad';


            $response = $this->client->get($url); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);

            //$mark = $this->string_between_two_string($pages[$l][0], 'cars/', '/page');
            $_this = $this;


            $data = $crawler->filter('div.card_list')->filter('div.card')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return 'https://www.bips.ru' . $node->filter('.card_url')->attr('href');
                }
                );
            //return $data;


            foreach ($data as $row) {
                $link = new Link();
                $link->link = "https://rolf-probeg.ru" . $row;
                //$link->category = $mark;
                $link->save();
            }


    }

    public function bips_products()
    {

        $links = Link::where('id', '>', 0)->get();
        foreach ($links as $link) {
            //$link="https://www.bips.ru/used/kia/rio/17444";

            try {
                $response = $this->client->get($link->link); // URL, where you want to fetch the content
            } catch (Exception $e) {
                //continue;
            }

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;

            $name = $crawler->filter('h1')->text();
            //$html = $crawler->filter('div.img_bgg')->outerHtml();
            //return $html;



            try {

                $title = $crawler->filter('meta[itemprop="name"]')->eq(0)->attr('content');
            } catch (Exception $e) {
                //continue;
            }

            $images = $crawler->filter('.img_gg')->filter('a')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node->attr('href');
                }
                );

                //return $images;

            $link_array = explode('/', substr($link, 0, -1));

            //return $link_array;
            $mark = $link_array[4];
            $model = $link_array[5];

            $page = end($link_array);

            foreach ($images as $image) {
                if ($image !== null) {
                    try {
                        //if ($image === '/static/images/banners/premium-banner1.jpg' || $image === '/static/images/banners/premium-banner2.jpg') continue;

                        $contents = file_get_contents($image);
                        $filename = substr($image, strrpos($image, '/') + 1);
                        Storage::put("china_cars/" . $mark . '/' . $model . '/' . $page . '/' . $filename, $contents);
                    } catch (InvalidOrderException $e) {
                        continue;
                    }
                }
            }
            $product = new RolfProduct();
            $product->name = $name;
            $product->mark = $mark;
            $product->model = $model;
            $product->category = "china_cars/" . $mark . '/' . $model . '/' . $page;
            $product->link = $link;
            $product->save();
        }
    }


    public function rolf_links()
    {
        $links = Link::where('id', '>', 0)->get();
        return response()->json($links);
    }
    public function rolf_products()
    {

        $links = Link::where('id', '>', 0)->get();
        foreach ($links as $link) {

            try {
                $response = $this->client->get($link->link); // URL, where you want to fetch the content
            } catch (Exception $e) {
                continue;
            }

            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;

            $name = $crawler->filter('h1')->text();

            try {

                $title = $crawler->filter('meta[itemprop="name"]')->eq(0)->attr('content');
            } catch (Exception $e) {
                continue;
            }

            $images = $crawler->filter('li.gallery__slide')
                ->each(function (Crawler $node, $i) use ($_this) {
                    return $node->attr('href');
                }
                );

            $link_array = explode('/', substr($link->link, 0, -1));

            if (strpos($link->link, "/spb") !== false) {
                $mark = $link_array[5];
                $model = $link_array[6];
            } else {
                $mark = $link_array[4];
                $model = $link_array[5];
            }
            $page = end($link_array);

            foreach ($images as $image) {
                if ($image !== null) {
                    try {
                        if ($image === '/static/images/banners/premium-banner1.jpg' || $image === '/static/images/banners/premium-banner2.jpg') continue;

                        $contents = file_get_contents($image);
                        $filename = substr($image, strrpos($image, '/') + 1);
                        Storage::put("middle_class/" . $mark . '/' . $model . '/' . $page . '/' . $filename, $contents);
                    } catch (InvalidOrderException $e) {
                        continue;
                    }
                }
            }
            $product = new RolfProduct();
            $product->name = $name;
            $product->mark = $mark;
            $product->model = $model;
            $product->category = "middle_class/" . $mark . '/' . $model . '/' . $page;
            $product->link = $link->link;
            $product->save();
        }
    }

    public function maximum_products()
    {
        $links = Link::where('id', '>', 784)->get();
        foreach ($links as $link) {
            try {
                $response = $this->client->get($link->link); // URL, where you want to fetch the content
            } catch (Exception $e) {
                continue;
            }
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            $_this = $this;

            $name = $crawler->filter('h1')->text();

            //return $name;

            /*try {

                $title = $crawler->filter('meta[itemprop="name"]')->eq(0)->attr('content');
            } catch (Exception $e) {
                continue;
            }*/

            $images = $crawler->filter('.styles_mainPhoto__r4LIa')->filter('li.splide__slide')->each(function (Crawler $node, $i) use ($_this) {
                return $node->filter('img')->attr('src');
            }
            );

            $bread = $crawler->filter('ol.styles_breadCrumbs__7yHEv')->filter('li')->each(function (Crawler $node, $i) use ($_this) {
                return $node->filter('a')->filter('span')->text();
            }
            );

            //return $images;


            $mark = $bread[1];
            $model = $bread[2];

            //$page = end($link_array);
            if (count($images) > 0) {
                foreach ($images as $image) {
                    if ($image !== null) {
                        try {
                            //sleep(1);//if ($image === '/static/images/banners/premium-banner1.jpg' || $image === '/static/images/banners/premium-banner2.jpg') continue;
                            $contents = file_get_contents($image);
                            $filename = substr($image, strrpos($image, '/') + 1);
                            Storage::put("maximum/" . $mark . '/' . $model . '/' . $model . '-' . $link->id . '/' . $filename, $contents);
                        } catch (Exception $e) {
                            sleep(1);
                            //$contents = file_get_contents($image);
                           // $filename = substr($image, strrpos($image, '/') + 1);
                            //Storage::put("maximum/" . $mark . '/' . $model . '/' . $model . '-' . $link->id . '/' . $filename, $contents);
                            continue;
                        }
                    }
                }
            }
            $product = new RolfProduct();
            $product->name = $name;
            $product->mark = $mark;
            $product->model = $model;
            $product->category = "maximum/" . $mark . '/' . $model . '/' . $model . '-' . $link->id;
            $product->link = $link->link;
            $product->save();
        }
    }


}
