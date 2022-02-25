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
use App\Models\UnoProduct;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UnoTechnoController extends Controller
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
    public function parse_links(){
        $url = "https://unotechno.ru/category/aksessuary/zashchitnye-stekla/?page=";
        for ($i=1; $i<=73; $i++){

            $response = $this->client->get($url.$i); // URL, where you want to fetch the content
            // get content and pass to the crawler
            $content = $response->getBody()->getContents();
            $crawler = new Crawler( $content );
            
            $_this = $this;
            
            $data = $crawler->filter('div.product-tile__outer')
                            ->each(function (Crawler $node, $i) use($_this) {
                                return $node->filter('div.product-tile__name a')->attr('href');
                                
                            }
                        );
                        //return $data;

                        foreach($data as $row){
                            $link = new Link();
                            $link->link= "https://unotechno.ru".$row;
                            $link->save();
                        }
                        



        }
    }

    public function uno_products(){
        $links = Link::where('id', '>', 1083)->get();
        foreach($links as $link){       
        

        $response = $this->client->get($link->link); // URL, where you want to fetch the content
        // get content and pass to the crawler
        $content = $response->getBody()->getContents();
        $crawler = new Crawler( $content );         
        $_this = $this;

        $name = $crawler->filter('h1 span')->text();         
        $category = $crawler->filter('ul.breadcrumbs li')->eq(3)->filter('a span')->text();
        $mark =$crawler->filter('ul.breadcrumbs li')->eq(2)->filter('a span')->text();
        
       // $model = $crawler->filter('li.product-model span')->text();

       try{
        $color = $crawler->filter('div.value_color-modific')->filter('.product-modifications__value--active span.product-modifications__name')->text();
       }
       catch (\InvalidArgumentException $e) {
        $color  = null;
        }
        
        $price = $crawler->filter('.product-card__prices .price')->attr('data-price');
        

        try{
            $old_price = $crawler->filter('.product-card__prices .price-compare')->attr('data-compare-price');
           }
           catch (\InvalidArgumentException $e) {
            $old_price  = null;
            }

        try{
            $versions = $crawler->filter('.product-modifications')->filter('.product-modifications__item')->eq(0)->filter('.product-modifications__values')->filter('a')->each(function (Crawler $node, $i) use($_this) {
                return $node->filter('.product-modifications__name')->text();                             
            }
             );
           }
           catch (\InvalidArgumentException $e) {
            $versions  = [];
            }
            

        try{
            $ram = $crawler->filter('.product-modifications')->filter('.product-modifications__item')->eq(1)->filter('.product-modifications__values')->filter('a')->each(function (Crawler $node, $i) use($_this) {
                return $node->filter('.product-modifications__name')->text();                             
            }
             );
           }
           catch (\InvalidArgumentException $e) {
            $ram  = [];
            }
            

        try{
            $colors = $crawler->filter('.product-modifications')->filter('.product-modifications__item')->eq(2)->filter('.product-modifications__values')->filter('a')->each(function (Crawler $node, $i) use($_this) {
                return $node->filter('.product-modifications__name')->text();                             
            }
             );
           }
           catch (\InvalidArgumentException $e) {
            $colors  = [];
            }
        //return $colors;

       $images = $crawler->filter('div.product-gallery-main__el-outer a')
       ->each(function (Crawler $node, $i) use($_this) {
           return 'https://unotechno.ru' . $node->attr('href');                             
       }
        );


       try {
           $desc = $crawler->filter('div.product-card__summary')->text();
       }
       catch (\InvalidArgumentException $e) {
           $desc  = null;
       }

       try {
           $feature = $crawler->filter('table.product_features')->outerHtml();
       }
       catch (\InvalidArgumentException $e) {
           $feature  = null;
       }

       try {
           $sku = $crawler->filter('.product-code span')->eq(1)->text();
       }
       catch (\InvalidArgumentException $e) {
           $sku  = null;
       }

       
       
       
                  
                   $product = new UnoProduct();
                   $product->name = $name;                   
                   $product->category =  $category;
                   $product->description = $desc;
                   $product->feature = $feature;
                   $product->pictures = $images;
                   $product->mark = $mark;
                   $product->color = $colors;
                   $product->color_name = $color;
                   $product->ram = $ram;
                   $product->sku = $sku;
                   $product->price = $price;
                   $product->old_price = $old_price;
                   $product->versions = $versions;
                   $product->save();
                }
}


    public function list(){
        $list = UnoProduct::get();
        return $list;
    }
}
