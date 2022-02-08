<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Exception;
use App\Models\Link;
use App\Models\Product;
use App\Models\Attachment;
use App\Models\MilanoProduct;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;




class CrawlerController extends Controller
{
    private $client;
    /**
     * Class __contruct
     */
    public function __construct()
    {
        $this->client = new Client([
                'timeout'   => 100,
                'verify'    => false
            ]);
    }
    public function index(){
        $url = "https://positronica.ru/catalog/televizory/?PAGEN_1=";
        for ($i=16; $i<=16; $i++){

            $response = $this->client->get($url.$i); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            
            $_this = $this;
            
            $data = $crawler->filter('ul.productList li')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('li.card_one a')->attr('href');
                                
                            }
                        );

                        foreach($data as $row){
                            $link = new Link();
                            $link->link= "https://positronica.ru".$row;
                            $link->save();
                        }
                        



        }
    }

    public function parse(){
        $links = Link::get();
        foreach($links as $link){

            $response = $this->client->get($link->link); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            //return intval($crawler->filter('div.price-item')->text());
            $_this = $this;
            $data = $crawler->filter('div.carousel-inner a')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('img')->attr('data-src');
                                
                            }
                        );

                       //return $data;



                        $urls = [];
                        
                       foreach ($data as $media) {
                            //$res = $this->storeImage($media, 'tempFolder');
                            //$tempFile = public_path($res);
                            $attachment = new Attachment;
                            $attachment->save();
                            $attachment->addMediaFromUrl($media)->toMediaCollection();
                          //  $attachment->addMultipleMediaFromRequest($data)->each(function ($fileAdder) {
                            //    $fileAdder->toMediaCollection()
                           // });
                            
                           foreach ($attachment->getMedia() as $image) {
                            $converted_url = [
                               'thumbnail' => $image->getUrl('thumbnail'),
                               'original' => $image->getUrl(),
                               'id' => $attachment->id
                           ];
                           
                       }
                       $urls[] = $converted_url;
                           

                        }
                        
                        //return $urls;
                        
            
            
        
           // return $urls;

            $product = new Product();
            $product->name = $crawler->filter('div.main-head h1')->text();
            $product->meta_title = "Отзывы и рейтинг по отзывам на ".$crawler->filter('div.main-head h1')->text();
            $product->meta_description = "Обзор всех отзывов и рейтинг по отзывам среди Интернет-магазинов на ".$crawler->filter('div.main-head h1')->text(). ' Отзывы на телевизоры- Рейтинг - Топ товаров - Электроника - Каталог - Цены - Обзор - Где купить';
            $product->name = $crawler->filter('div.main-head h1')->text();
            $product->slug = Str::slug($crawler->filter('div.main-head h1')->text());            
            $product->price = $crawler->filter('div.price-item')->text();
            $product->sale_price = $crawler->filter('div.price-item')->text();
            $product->description = $crawler->filter('table.table-hover')->outerHtml();

            $product->brand_id = 1;
            if (count($urls)> 0) {
                $product->image = json_encode($urls[0]);           
                $product->gallery = json_encode($urls);
            }
            //$product->image = $urls[0];
            //$product->gallery = $urls;
            $product->quantity = 1000;
            $product->is_taxable = true;
            $product->in_stock = true;
            $product->status = 'publish';
            $product->unit = 123;
            $product->type_id = 9;
            $product->product_type = 'simple';
            $product->save();
            sleep(30);
    }
    return "yes";

    }

    public static function storeImage($remote, $desDir)
    {
        $adapter = new Local($desDir);
 
        $filesystem = new Filesystem($adapter);
 
        $pathInfo = pathinfo($remote);
 
        $stream = fopen($remote, 'r');
 
        if ($filesystem->putStream($pathInfo['basename'], $stream)) {
 
            fclose($stream);
 
            return trim($desDir) . DIRECTORY_SEPARATOR . $pathInfo['basename'];
        }
 
        return null;
    }

    public function milano(){
        $urls1 = array (
            array("https://leventozel.com.tr/ru/%D1%82%D0%BA%D0%B0%D0%BD%D1%8C-%D0%B4%D0%BE%D0%B1%D0%B1%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-%D0%B4%D0%BB%D1%8F-%D1%84%D0%BE%D0%BD%D0%B0?page=",1,4),
            array("https://leventozel.com.tr/ru/%D0%B6%D0%B0%D0%BA%D0%BA%D0%B0%D1%80%D0%B4%D0%BE%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-%D0%B4%D0%BB%D1%8F-%D1%84%D0%BE%D0%BD%D0%B0?page=",1,5),
            array("https://leventozel.com.tr/ru/%D0%B1%D0%B0%D1%80%D1%85%D0%B0%D1%82%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D0%B8-%D0%B2%D1%8F%D0%B7%D0%B0%D0%BD%D1%8B%D0%B5-%D1%84%D0%BE%D0%BD%D0%BE%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80?page=",1,2),
            array("https://leventozel.com.tr/ru/%D0%BB%D1%8C%D0%BD%D1%8F%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D0%B7%D0%B0%D1%82%D0%B5%D0%BC%D0%BD%D0%B5%D0%BD%D0%B8%D1%8F",1,1),
            array("https://leventozel.com.tr/ru/%D0%BF%D0%B0%D0%BD%D0%BD%D0%BE-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-%D1%81-%D1%86%D0%B8%D1%84%D1%80%D0%BE%D0%B2%D0%BE%D0%B9-%D0%BF%D0%B5%D1%87%D0%B0%D1%82%D1%8C%D1%8E-%D0%B4%D0%BB%D1%8F-%D1%84%D0%BE%D0%BD%D0%B0",1,1),
            array("https://leventozel.com.tr/ru/%D0%BF%D1%80%D0%BE%D0%B7%D1%80%D0%B0%D1%87%D0%BD%D0%B0%D1%8F-%D1%82%D0%BA%D0%B0%D0%BD%D1%8C-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-%D0%B8%D0%B7-%D1%82%D1%8E%D0%BB%D1%8F?page=",1,2),
            array("https://leventozel.com.tr/ru/%D0%BB%D1%8C%D0%BD%D1%8F%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D1%82%D1%8E%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80?page=",1,3),
            array("https://leventozel.com.tr/ru/%D0%BF%D0%BB%D0%B5%D1%82%D0%B5%D0%BD%D1%8B%D0%B5-%D1%82%D1%8E%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-leno?page=",1,4),
            array("https://leventozel.com.tr/ru/%D0%B2%D1%8F%D0%B7%D0%B0%D0%BD%D0%B0%D1%8F-%D1%82%D0%BA%D0%B0%D0%BD%D1%8C-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80-%D0%B8%D0%B7-%D1%82%D1%8E%D0%BB%D1%8F-%D1%80%D0%B0%D1%88%D0%B5%D0%BB%D1%8C?page=",1,2),
            array("https://leventozel.com.tr/ru/%D0%B2%D1%8B%D1%88%D0%B8%D1%82%D1%8B%D0%B5-%D1%82%D1%8E%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80",1,1),
            array("https://leventozel.com.tr/ru/%D0%BF%D0%BB%D0%B5%D1%82%D0%B5%D0%BD%D1%8B%D0%B5-%D0%BF%D0%BB%D0%BE%D1%82%D0%BD%D1%8B%D0%B5-%D1%88%D1%82%D0%BE%D1%80%D1%8B-%D0%B4%D0%BE%D0%B1%D0%B1%D0%B8",1,1),
            array("https://leventozel.com.tr/ru/%D0%BB%D1%8C%D0%BD%D1%8F%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D0%B7%D0%B0%D1%82%D0%B5%D0%BC%D0%BD%D0%B5%D0%BD%D0%B8%D1%8F",1,1),
            array("https://leventozel.com.tr/ru/%D0%B4%D0%B2%D1%83%D1%81%D1%82%D0%BE%D1%80%D0%BE%D0%BD%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BB%D0%BE%D1%82%D0%BD%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8",1,1),
            array("https://leventozel.com.tr/ru/%D0%B6%D0%B0%D0%BA%D0%BA%D0%B0%D1%80%D0%B4%D0%BE%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D0%B7%D0%B0%D1%82%D0%B5%D0%BC%D0%BD%D0%B5%D0%BD%D0%B8%D1%8F",1,1),
            array("https://leventozel.com.tr/ru/%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D1%81-%D0%BF%D0%BE%D0%BA%D1%80%D1%8B%D1%82%D0%B8%D0%B5%D0%BC-blackout",1,1)          );
        foreach($urls1 as $key=>$ur1){
            for ($i=$ur1[1]; $i<=$ur1[2]; $i++){
            if ($ur1[2]>1)    {
                $response = $this->client->get($ur1[0].$i);
            }
            else {
                $response = $this->client->get($ur1[0]); // URL, where you want to fetch the content
            }
            
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            
            $_this = $this;
            
            $data = $crawler->filter('div.product-thumb')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('div.image a')->attr('href');
                                
                            }
                        );                      

                        foreach($data as $row){
                            $category = $this->getBetween($row, 'ru/', '/');                           
                            $link = new Link();
                            $link->link= $row;
                            $link->category= $this->my_mb_ucfirst($category);
                            $link->category_id= $key+1;
                            $link->save();
                        }
                        



        }
        }
    }

    public function milano_products(){
       // $url = "https://leventozel.com.tr/ru/%D0%B2%D1%8B%D1%88%D0%B8%D1%82%D1%8B%D0%B5-%D1%82%D1%8E%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5-%D1%82%D0%BA%D0%B0%D0%BD%D0%B8-%D0%B4%D0%BB%D1%8F-%D1%88%D1%82%D0%BE%D1%80";
        //for ($i=1; $i<=1; $i++){
            $links = Link::get();
            foreach($links as $link){

            
            

            $response = $this->client->get($link->link); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            
            $_this = $this;

            $name = $crawler->filter('div.page-title')->text();
            $mark = $crawler->filter('li.product-manufacturer a')->text();
            $model = $crawler->filter('li.product-model span')->text();
            $price_usd = substr($crawler->filter('div.product-price')->text(), 1) ?? null;
            $price = ($price_usd + 1) *75;
            $feature = $crawler->filter('div.table-responsive')->outerHtml();
            

           
            
            $data = $crawler->filter('ul.prodvar li')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('a')->attr('href');
                                
                            }
                        );    
                        
                        $images = $crawler->filter('div.main-image .swiper-slide')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('img')->attr('src');
                                
                            }
                        );

                        $pictures=[];

                        

                       foreach($images as $key => $image){
                       // sleep(5);
                        //$filename = $link->id.'_'.($key+1).'_'.Str::random(5).'_'.basename($image);
                        //Image::make($image)->save(public_path("images/". $filename));
                        $pictures[]=$image;
                       }


                       $product = new MilanoProduct();
                       $product->name = $name;
                       $product->mark = $mark;
                       $product->model = $model;
                       $product->price_usd = $price_usd;
                       $product->price = $price;
                       $product->description = $feature;
                       $product->pictures = $pictures;
                       $product->link =  $link->link;                       
                       $product->category_id =  $link->category_id;
                       $product->category =  $link->category;
                       $product->save();
                       $parent_id = $product->id;
                      // dd($product); die();
                        
                        
                        
                        

                        foreach($data as $key => $row){
                            //sleep(80);
                            $response2 = $this->client->get($row); // URL, where you want to fetch the content
                            // get content and pass to the crawler
                            $content2 = $response2->getBody()->getContents();
                            $crawler2 = new Crawler( $content2 );
            
           

                            $name = $crawler2->filter('div.page-title')->text();
                            $mark = $crawler2->filter('li.product-manufacturer a')->text();
                            $model = $crawler2->filter('li.product-model span')->text();
                            $price_usd = substr($crawler2->filter('div.product-price')->text(), 1) ?? null;
                            $price = ($price_usd + 1) *75;
                            $feature = $crawler2->filter('div.table-responsive')->outerHtml();
            

           
            
          
                        
                        $imgs = $crawler2->filter('div.main-image .swiper-slide')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('img')->attr('src');
                                
                            }
                        );

                        $pics=[];

                        

                       foreach($imgs as $image){
                        //$filename = $link->id.'_'.$parent_id.'_'.($key+1).'_'.Str::random(5).'_'.basename($image);
                        //Image::make($image)->save(public_path("images/". $filename));
                        $pics[]=$image;
                       }


                       $product2 = new MilanoProduct();
                       $product2->name = $name;
                       $product2->mark = $mark;
                       $product2->model = $model;
                       $product2->price_usd = $price_usd;
                       $product2->price = $price;
                       $product2->description = $feature;
                       $product2->pictures = $pics;
                       $product2->parent_id =  $parent_id;
                       $product2->category_id =  $link->category_id;
                       $product2->category =  $link->category;
                       $product2->link =  $row;
                       $product2->save();
                            
                        }
                       // dd($product2);
                    }
    }

    function getBetween($string, $start = "", $end = ""){
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

    function my_mb_ucfirst($str) {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }

    function download_images() {
        $products = MilanoProduct::where('images','[]')->get();
        foreach($products as $product){
            $pictures = [];
            //$palette = new \BrianMcdo\ImagePalette\ImagePalette($product->pictures[1] ?? $product->pictures[0]);
            //return $palette;
            foreach($product->pictures as $key=>$image){
                
                //return utf8_encode($image);
                //return $image;
                
               if (str_starts_with($image, 'https://')){
                
                try{
                    //$image=urlencode($image);
                    //$filename = $product->id.'_'.($key+1).'_'.Str::random(5).'_'.str_replace(' ', '_', basename($image));
                   // Image::make($image)->save(public_path("new_image/". $filename));
                   $contents = file_get_contents($image);
                    $name = $product->id.'_'.($key+1).'_'.Str::random(5).'_'.str_replace(' ', '_',substr($image, strrpos($image, '/') + 1));
                    Storage::put($name, $contents); 
                   $pictures[]=$name;
                }
                catch(Exception $e){
                    return $e;
                }                   
               
               }
               else continue;
                }
                sleep(5);
                //return $pictures;
            $item = MilanoProduct::where('id',$product->id)->first();
            $item->images=$pictures;
            //$item->color=$palette->color;;
            $item->save();
        }
    }


    function refresh_images() {
        $products = MilanoProduct::where('images','[]')->get();
        //$i=0;
        foreach($products as $product){
            $pictures = [];
            //$palette = new \BrianMcdo\ImagePalette\ImagePalette($product->pictures[1] ?? $product->pictures[0]);
            //return $palette;

            if (!str_starts_with($product->pictures[0], 'https://')){
                //return $product->pictures;
                $client = new Client(['headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36']]);
                //$client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36");
                $response = $client->request('GET',$product->link); // URL, where you want to fetch the content
                // get content and pass to the crawler
                $content = $response->getBody()->getContents();
                $crawler = new Crawler( $content );

                
                
                $_this = $this;

                $images = $crawler->filter('div.main-image .swiper-slide')
                ->each(function (Crawler $node, $i) use($_this) {
                    return $node->filter('img')->attr('src');
                    
                }

            );
            //return $images;

            $pictures=[];

            

           foreach($images as $key => $image){
           // sleep(5);
            //$filename = $link->id.'_'.($key+1).'_'.Str::random(5).'_'.basename($image);
            //Image::make($image)->save(public_path("images/". $filename));
            $pictures[]=$image;
           }

                    //return $pictures;
                $item = MilanoProduct::where('id',$product->id)->first();
                $item->pictures=$pictures;
                //$item->color=$palette->color;;
                $item->save();
            }
            
                
        }
    }

    public function setColor(){
        $products = MilanoProduct::where('id','>', 4138)->get();
        
        foreach ($products as $product){
            
        
        try {
            $palette = new \BrianMcdo\ImagePalette\ImagePalette($product->pictures[1] ?? $product->pictures[0]);
            // get the prominent colors
            $colors = $palette->colors; // array of Color objects
            //dd($colors);
            //return response()->json($colors);

            $color =  $this->getColor((array)$colors[0]);
            $item = MilanoProduct::where('id',$product->id)->first();
            $item->color_name = $color;
            $item->color = $colors;
            $item->save();
            }
            catch (Exception $e){
                    continue;
            }

            

        }

    // foreach($colors as $color ){
            //return $colors->rgbString;
        // echo '<p style="font-size:32px; background-color:'.$color.';">test</p>';

    // }
    }                                   

    function getColor($value){

    //$value = "#819001";
    $distances = array();
    //$val = $this->html2rgb($value);
    //return $value['r'];
    $val = array($value['r'], $value['g'], $value['b'],);
    //return $val;
    $colors = array(
        "Черный"     => array(0, 0, 0),
        "Зеленный"     => array(0, 128, 0),
        "Серебристый"    => array(192, 192, 192),
        "Бежевый"    => array(245, 245, 220),
        "Бисквит"    => array(255, 228, 196),
        "Бисквит"    => array(255, 235, 205),
        "Фиолетовый"    => array(138, 43, 226),
        "Лайм"      => array(0, 255, 0),
        "Серый"      => array(128, 0, 128),
        "Оливковый"     => array(128, 128, 0),
        "Белый"     => array(255, 255, 255),
        "Желтый"    => array(255, 255, 0),
        "Бордовый"    => array(128, 0, 0),
        "Темно-синий"      => array(0, 0, 128),
        "Темно-синий"      => array(159, 175, 223),
        "Темно-синий"      => array(25, 25, 112),
        "Темно-розовый"      => array(255, 20, 147),
        "Золотистый"      => array(255, 215, 0),        
        "Золотистый"    => array(184, 134, 11),
        "Золотистый"    => array(222, 184, 135),
        "Золотистый"    => array(255, 248, 220),
        "Золотистый"    => array(153, 102, 51),
        "Золотистый"    => array(192, 120, 10),
        "Хаки"      => array(240, 230, 140),
        "Красный"       => array(255, 0, 0),
        "Синий"      => array(0, 0, 255),
        "Пурпурный"    => array(128, 0, 128),
        "Бирюзовый"      => array(0, 128, 128),
        "Фуксия"   => array(255, 0, 255),
        "Морской волны"      => array(0, 255, 255),
    );
    //return $value;
    foreach ($colors as $name => $c) {
        $distances[$name] = $this->distancel2($c, $val);
    }
    
    $mincolor = "";
    $minval = pow(2, 30); /*big value*/
    foreach ($distances as $k => $v) {
        if ($v < $minval) {
            $minval = $v;
            $mincolor = $k;
        }
    }
    
    return $mincolor;
    }






    function html2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                    $color[2].$color[3],
                                    $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0],
                $color[1].$color[1], $color[2].$color[2]);
        else
            return false;    
        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }

    function distancel2(array $color1, array $color2) {
        return sqrt(pow($color1[0] - $color2[0], 2) +
            pow($color1[1] - $color2[1], 2) +
            pow($color1[2] - $color2[2], 2));
    }


    public function exportCsv(Request $request)
    {
            $fileName = 'tasks.csv';
            $products = MilanoProduct::get();
            
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            //$columns = array('Title', 'Assign', 'Description', 'Start Date', 'Due Date');
            $columns = array("Тип","Артикул","Имя","Опубликован","рекомендуемый?","Видимость в каталоге","Краткое описание","Описание","Дата начала действия продажной цены","Дата окончания действия продажной цены","Статус налога","Налоговый класс","В наличии?","Запасы","Величина малых запасов","Возможен ли предзаказ?","Продано индивидуально?","Вес (kg)","Длина (cm)","Ширина (cm)","Высота (cm)","Разрешить отзывы от клиентов?","Примечание к покупке","Цена распродажи","Базовая цена","Категории","Метки","Класс доставки","Изображения","Лимит загрузок","Число дней до просроченной загрузки","Родительский","Сгруппированные товары","Апсейл","Кросселы","Внешний URL","Текст кнопки","Позиция","Имя атрибута 1","Значение(-я) аттрибута(-ов) 1","Видимость атрибута 1","Глобальный атрибут 1","Имя атрибута 2","Значение(-я) аттрибута(-ов) 2","Видимость атрибута 2","Глобальный атрибут 2");
            
            $callback = function() use($products, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($products as $product) {

                    return json_decode($product->images);
                    $row['Тип']  = "variable";
                    $row['Артикул']  = $product->id.'-'.Str::slug($product->model, '-');
                    $row['Имя']  = $product->title;
                    $row['Опубликован']  = 1;
                    $row['рекомендуемый?']    = 0;
                    $row['Видимость в каталоге']    = 'visible';
                    $row['Краткое описание']  = $product->title . ' '. $product->color_name;
                    $row['Описание']  = $product->description;
                    $row['Дата начала действия продажной цены']  = "";
                    $row['Дата окончания действия продажной цены']  = "";
                    $row['Статус налога']  = "taxable";
                    $row['Налоговый класс']  = "";
                    $row['В наличии?']  = 1;
                    $row['Запасы']  = 1000;
                    $row['Величина малых запасов']  = "";
                    $row['Возможен ли предзаказ?']  = 0;
                    $row['Продано индивидуально?']  = 0;
                    $row['Вес (kg)']  = "";
                    $row['Длина (cm)']  = "";
                    $row['Ширина (cm)']  = "";
                    $row['Высота (cm)']  = "";
                    $row['Разрешить отзывы от клиентов?']  = 1;
                    $row['Примечание к покупке']  = "";
                    $row['Цена распродажи']  = "";
                    $row['Базовая цена']  = $product->price;
                    $row['Категории']  = $product->category;
                    $row['Метки']  = "";
                    $row['Класс доставки']  = "";
                    $images="";
                    if (count($product->img>0)){
                        foreach($product->images as $image){        
                                            
                            $images  = $images . "https://milano-collection.com/wp-content/uploads/2021/".getNum($product->id).$image.', ';
                        } 
                    }
                                       
                    $row['Изображения']  = substr($images, 0, -2);
                    $row['Лимит загрузок']  = "";
                    $row['Число дней до просроченной загрузки']  = "";                   
                    $row['Родительский']  = "";                    
                    $row['Сгруппированные товары']  = "";                    
                    $row['Апсейл']  = "";
                    $row['Кросселы']  = "";
                    $row['Внешний URL']  = "";
                    $row['Текст кнопки']  = "";
                    $row['Позиция']  = 0;
                    $row['Имя атрибута 1']  = "Цвет";
                    $row['Значение(-я) аттрибута(-ов) 1']  = "Бежевый, Белый, Голубой, Желтый, Зеленый, Коричневый, Красный, Оранжевый, Розовый, Салатовый, Серий, Фиолетовый, Черный";
                    $row['Видимость атрибута 1']  = 1;
                    $row['Глобальный атрибут 1']  = 1;
                    $row['Имя атрибута 2']  = "";
                    $row['Значение(-я) аттрибута(-ов) 2']  = "";
                    $row['Видимость атрибута 2']  = "";
                    $row['Глобальный атрибут 2']  = "";

                    fputcsv($file, array( $row['Тип'], $row['Артикул'] , $row['Имя'],  $row['Опубликован'], $row['рекомендуемый?'], $row['Видимость в каталоге'], $row['Краткое описание'], $row['Описание'],  $row['Дата начала действия продажной цены'], $row['Дата окончания действия продажной цены'], $row['Статус налога'], $row['Налоговый класс'],  $row['В наличии?'], $row['Запасы'], $row['Величина малых запасов'], $row['Возможен ли предзаказ?'], $row['Продано индивидуально?'],  $row['Вес (kg)'],  $row['Длина (cm)'], $row['Ширина (cm)'], $row['Высота (cm)'], $row['Разрешить отзывы от клиентов?'], $row['Примечание к покупке'], $row['Цена распродажи'], $row['Базовая цена'], $row['Категории'], $row['Метки'], $row['Класс доставки'],  $row['Изображения'], $row['Лимит загрузок'], $row['Число дней до просроченной загрузки'], $row['Родительский'], $row['Сгруппированные товары'], $row['Апсейл'], $row['Кросселы'], $row['Внешний URL'], $row['Текст кнопки'], $row['Позиция'], $row['Имя атрибута 1'], $row['Значение(-я) аттрибута(-ов) 1'], $row['Видимость атрибута 1'], $row['Глобальный атрибут 1'], $row['Имя атрибута 2'], $row['Значение(-я) аттрибута(-ов) 2'], $row['Видимость атрибута 2'], $row['Глобальный атрибут 2']));
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        function getNum($id) {
            if ($id >0 and $id<=1000){
                return '1/';
            }
            if ($id >1000 and $id<=2000){
                return '2/';
            }            
            if ($id >3000 and $id<=4000){
                return '3/';
            }            
            if ($id >4000 and $id<=5000){
                return '5/';
            }
        }
        function limit_text($text, $limit) {
            if (str_word_count($text, 0) > $limit) {
                $words = str_word_count($text, 2);
                $pos   = array_keys($words);
                $text  = substr($text, 0, $pos[$limit]) . '...';
            }
            return $text;
        }


        public function renameTurkish(){

            $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç");//turkish letters
            $english   = array("i", "g", "u", "s", "o", "c");//english cooridinators letters

            
            $products = MilanoProduct::whereNotNull('images')->where('id','>', 0)->get();
            
            foreach ($products as $product){
            //dd($product->images);
             $images=[]; 
            foreach ($product->images as $key=>$image){
                $image_name = str_replace($turkish, $english, $image);           
                $image_name = strtolower(str_replace('%20', '_', $image_name));
                try{
                    Storage::move('images/'.$image, 'images/'.$this->folderName($product->id).'/'.$image_name);
                } 
                catch(Exception $e){
                    continue;
                }    
               
                $images[]=$image_name;
            }

            $item = MilanoProduct::where('id',$product->id)->first();
            $item->new_images = $images;
            $item->save();



                
            
        }
        }

        public function folderName($t){
            $result = 0;
            switch (true) {
            case  ($t < "1000"):
                $result =1;
                break;
            case  ($t < "2000"):
                $result =2;
                break;
            case  ($t < "3000"):
                $result =3;
                break;
            case  ($t < "4000"):
                $result =4;
                break;
            case  ($t < "5000"):
                $result =5;
                break;
            case  ($t < "6000"):
                $result =6;
                break;
            default:
                $result =7;
            }
            return $result;
        }

        public function getProducts(){
            $products = MilanoProduct::paginate(30); 
            return response()->json($products);
        }

        public function setColor2(Request $request){
            $product = MilanoProduct::where('id', $request->id)->first();
            if (!empty($product)){
                $product->color2 = $request->color;
                $product->color_code = $request->color_code;
                $product->colored = 1;
                $product->save();
            } 
            return response()->json($product);
        }

        public function tradein()
    {
        try {
            $response = $this->client->get('https://www.tradein-bc.ru/Toyota/Corolla/COROLLA-55254/'); 
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            
            $_this = $this;
            $array = [
                'title' => $this->hasContent($crawler->filter('h1')) != false ? $crawler->filter('h1')->text() : '',
                'price' => $this->hasContent($crawler->filter('span.pr')) != false ? $crawler->filter('span.pr')->text() : '',
                'feature' => $this->hasContent($crawler->filter('table.cartable2')) != false ? $crawler->filter('table.cartable2')->outerHtml() : '',
                'tpad2' => $this->hasContent($crawler->filter('p.tpad2')) != false ? $crawler->filter('p.tpad2')->outerHtml() : '',
                'featured_image' => [
                     $this->hasContent($crawler->filter('a.photo0')) != false ? $crawler->filter('a.photo0')->eq(0)->attr('href') : '',
                     $this->hasContent($crawler->filter('a.photo1')) != false ? $crawler->filter('a.photo1')->eq(0)->attr('href') : '',
                     $this->hasContent($crawler->filter('a.photo2')) != false ? $crawler->filter('a.photo2')->eq(0)->attr('href') : '',
                     $this->hasContent($crawler->filter('a.photo3')) != false ? $crawler->filter('a.photo3')->eq(0)->attr('href') : '',
                ]
            ];

            $images = $crawler->filter('div.main-image .swiper-slide')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('img')->attr('src');
                                
                            }
                        );

            $item = new Universal();
            $item = 
            
            dd($array);
           // header("Content-type: image/gif");
           //echo base64_decode($crawler->filter('div.swiper-slide img'));
            
        } catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }



    public function parse_links()
    {
        

            $urls = array (
                array('https://www.tradein-bc.ru/Audi/',1, 1),
                array('https://www.tradein-bc.ru/BMW/',1, 1),
                array('https://www.tradein-bc.ru/Cadillac/',1, 1),
                array('https://www.tradein-bc.ru/Chery/',1, 1),
                array('https://www.tradein-bc.ru/Chevrolet/',1, 1),
                array('https://www.tradein-bc.ru/Chrysler/',1, 1),
                array('https://www.tradein-bc.ru/Citroen/',1, 1),
                array('https://www.tradein-bc.ru/Daewoo/',1, 1),
                array('https://www.tradein-bc.ru/Datsun/',1, 1),
                array('https://www.tradein-bc.ru/DongFeng/',1, 1),
                array('https://www.tradein-bc.ru/Faw/',1, 1),
                array('https://www.tradein-bc.ru/Fiat/',1, 1),
                array('https://www.tradein-bc.ru/Ford/',1, 1),
                array('https://www.tradein-bc.ru/Gaz/',1, 1),
                array('https://www.tradein-bc.ru/Geely/',1, 1),
                array('https://www.tradein-bc.ru/Genesis/',1, 1),
                array('https://www.tradein-bc.ru/Haval/',1, 1),
                array('https://www.tradein-bc.ru/Honda/',1, 1),
                array('https://www.tradein-bc.ru/Hyundai/',1, 5),
                array('https://www.tradein-bc.ru/Infiniti/',1, 1),
                array('https://www.tradein-bc.ru/Jaguar/',1, 1),
                array('https://www.tradein-bc.ru/Jeep/',1, 1),
                array('https://www.tradein-bc.ru/Kia/',1, 4),
                array('https://www.tradein-bc.ru/Lada/',1, 3),
                array('https://www.tradein-bc.ru/Land_Rover/',1, 1),
                array('https://www.tradein-bc.ru/Lexus/',1, 2),
                array('https://www.tradein-bc.ru/Lifan/',1, 1),
                array('https://www.tradein-bc.ru/Mazda/',1, 1),
                array('https://www.tradein-bc.ru/Mercedes-Benz/',1, 2),
                array('https://www.tradein-bc.ru/Mini/',1, 1),
                array('https://www.tradein-bc.ru/Mitsubishi/',1, 1),
                array('https://www.tradein-bc.ru/Nissan/',1, 3),
                array('https://www.tradein-bc.ru/Opel/',1, 1),
                array('https://www.tradein-bc.ru/Porsche/',1, 1),
                array('https://www.tradein-bc.ru/Ravon/',1, 1),
                array('https://www.tradein-bc.ru/Renault/',1, 2),
                array('https://www.tradein-bc.ru/Skoda/',1, 2),
                array('https://www.tradein-bc.ru/SsangYong/',1, 1),
                array('https://www.tradein-bc.ru/Subaru/',1, 1),
                array('https://www.tradein-bc.ru/Suzuki/',1, 1),
                array('https://www.tradein-bc.ru/toyota/',1, 9),
                array('https://www.tradein-bc.ru/UAZ/',1, 1),
                array('https://www.tradein-bc.ru/volkswagen/',1, 3),
                array('https://www.tradein-bc.ru/volvo/',1, 1),
                array('https://www.tradein-bc.ru/Zotye/',1, 1)
            );
            $urls2 = array (
                array('https://www.tradein-bc.ru/toyota/',1, 9),
            );
            foreach($urls2 as $key=>$ur1){
                for ($i=$ur1[1]; $i<=$ur1[2]; $i++){
                if ($ur1[2]>1)    {
                    $response = $this->client->get($ur1[0].'#page-'.$i);
                }
                else {
                    $response = $this->client->get($ur1[0]); // URL, where you want to fetch the content
                }
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            
            $_this = $this;
            
            try {
            $data = $crawler->filter('div.item')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node;
                                
                            }
                        );

                       
                                 

                        foreach($data as $row){                        
                            $link = new Link();
                            if ($row->filter('a img')->attr('src')==='/img/no_image.png'){
                                continue;
                            }
                            $link->link= 'https://www.tradein-bc.ru'.$row->filter('a')->attr('href');
                            $link->title= $row->filter('div.fr a')->text();
                            $link->param= $row->filter('div.param')->text();
                            $link->price= $row->filter('div.price b')->text();
                            $link->save();
                        }

                    } catch ( Exception $e ) {
                        // echo $e->getMessage();
                     }


                //$array = [
                 //   'title' => $this->hasContent($crawler->filter('div.fr')) != false ? $crawler->filter('div.fr a')->text() : '',
                 //   'link' => $this->hasContent($crawler->filter('div.fr')) != false ? $crawler->filter('div.fr a')->eq(0)->attr('href') : '',
                 //   'param' => $this->hasContent($crawler->filter('div.param')) != false ? $crawler->filter('div.param')->text() : '',
                 //   'price' => $this->hasContent($crawler->filter('div.price b')) != false ? $crawler->filter('div.price b')->text() : '',
    
                    //'price' => $this->hasContent($crawler->filter('span.pr')) != false ? $crawler->filter('span.pr')->text() : $crawler->filter('div.current-price-container')->eq(0)->attr('content'),
                    //'feature' => $this->hasContent($crawler->filter('table.cartable2')) != false ? $crawler->filter('table.cartable2')->outerHtml() : '',
                   // 'tpad2' => $this->hasContent($crawler->filter('p.tpad2')) != false ? $crawler->filter('p.tpad2')->outerHtml() : '',
                   /* 'featured_image' => [
                         $this->hasContent($crawler->filter('a.photo0')) != false ? $crawler->filter('a.photo0')->eq(0)->attr('href') : '',
                         $this->hasContent($crawler->filter('a.photo1')) != false ? $crawler->filter('a.photo1')->eq(0)->attr('href') : '',
                         $this->hasContent($crawler->filter('a.photo2')) != false ? $crawler->filter('a.photo2')->eq(0)->attr('href') : '',
                         $this->hasContent($crawler->filter('a.photo3')) != false ? $crawler->filter('a.photo3')->eq(0)->attr('href') : '',
                    ] */
                //];
    
              
            }
            //sleep(5);
            }

            
            
        
    }


    public function parseTradein(){
        $links = Link::groupBy('link')->where('id', '>', 1873)->get();
       //return count($links);
        foreach($links as $link){

            $response = $this->client->get($link->link); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler($content);
            //return intval($crawler->filter('div.price-item')->text());
            $_this = $this;
            
            $data = $crawler->filter('a.photo1')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->attr('href');
                                
                            }
                        );

                       array_unshift($data, 'https://www.tradein-bc.ru'.$crawler->filter('a.photo0')->attr('href'));

                       //return $data;



                        $urls = [];
                        
                       foreach ($data as $media) {

                        //try{
                            $image = file_get_contents($media);
                            //$fname = basename($media).PHP_EOL;
                            //file_put_contents(public_path($fname), $image);
                            $name = substr($media, strrpos($media, '/') + 1);
                            Storage::put('tradein_new/'.$this->getBetween($link->link, 'www.tradein-bc.ru/', '/').'/'.$this->getBetween($data[0], 'catalogauto/', '/').'/'.$name, $image);                           
                           
                            $urls[] = $image;

                        //} catch ( Exception $e ) {
                        //    echo $e->getMessage();
                       // }
                           

                        }
                        
                        //return $urls;

            $product = Link::find($link->id);
            $product->feature = $crawler->filter('table.cartable2')->outerHtml();
            //$product->complectation = $crawler->filter('table.cartable3')->outerHtml();
            ///$product->description = $crawler->filter('table.cartable4')->outerHtml();
            //$product->meta_title = $crawler->filter('title')->text();
            //$product->meta_description = $crawler->filter('description')->text();
           // if (count($urls)> 0) {
            //    $product->image = $urls[0];           
               $product->pictures = json_encode($urls);
           // }
           // $product->old_id = $this->getBetween($data[0], 'catalogauto/', '/');
           // $product->mark = $this->getBetween($link, 'www.tradein-bc.ru/', '/');
            $product->save();
            //sleep(3);
        
    
    }



}
}
