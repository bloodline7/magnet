<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Models\Mongo\Brands;
use Ausumsports\Admin\Models\Mongo\Product;
use Ausumsports\Admin\Models\Mongo\ProductList;
use Ausumsports\Admin\Models\Mongo\Products;
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;


class CardBoardConnection
{

    private $baseUrl;
    private $channel;
    private $result;

    public function __construct()
    {


        $this->channel = "CardBoardConnection";

        $this->baseUrl = "https://www.cardboardconnection.com/";
        $this->result = [];

        Log::info("Channel :: " . $this->channel);

    }


    public function brands()
    {

        $client = new Client();
        $mongo = new Brands();
        $crawler = $client->request('GET', $this->baseUrl . "/brand");

        $crawler->filter("#product_detail2")->each( function ($node) use ($client, $mongo) {


            $obj = new \stdClass();

            $obj->manufacturers_logo = $node->filter("img")->attr('data-src');
            $obj->manufacturers_title = $node->filter("h2.product-full")->filter("a")->text();
            $obj->manufacturers_link = $node->filter("h2.product-full")->filter("a")->attr('href');

             $brands =  $client->click( $node->filter("h2.product-full")->filter("a")->link() );

            if( $brands->filter("div.multi-column-taxonomy-list")->count() )
            {
                $brands->filter("div.multi-column-taxonomy-list")
                    ->first()->filter("ul")->filter("li")
                    ->each( function ($node) use ($client, $obj, $mongo)
                    {
                        $obj->brand_link =  $node->filter("a")->attr('href');
                        $obj->brand_title =  $node->filter("a")->text();

                        $mongo->create((array)$obj);
                        Log::debug(json_encode($obj) . "\n");
                    });
            }
            else
            {
                $obj->brand_link =  $obj->manufacturers_link;
                $obj->brand_title =  $obj->manufacturers_title;

                $mongo->create((array)$obj);
                Log::debug(json_encode($obj) . "\n");
            }
            array_push($this->result, $obj);
        });

        return $this->result;
    }


    /** 상품 목록  */
    function products ()
    {
        $brands = new Brands();
        $client = new Client();
        $product = new Products();

        $Result = $brands->orderby('_id')->get();

        foreach ($Result as $item)
        {
            $crawler = $client->request('GET', $item->brand_link);
            $crawler->filter("#productListing > div.brandproduct")->each(function ($node) use ($item, $product) {

                $obj = new \stdClass();
                $obj->brand_id = $item->_id;
                $obj->product_image = $node->filter("img")->attr('data-src');
                $obj->product_title = $node->filter("h2.product-subtitle > a")->text();
                $obj->product_link = $node->filter("h2.product-subtitle > a")->attr('href');

                $product->create((array)$obj);
                Log::debug(json_encode($obj) . "\n");

            });

            Log::debug(json_encode($item) . "\n");
        }
    }

    function getProduct()
    {

        $client = new Client();
        $product = new Products();

        //$product->where('new', true )->delete();


        $crawler = $client->request('GET', "https://www.cardboardconnection.com/sports-cards-sets");

        $crawler->filter("#productListing > div.brandproduct")->each(function ($node) use ($product) {

            $obj = new \stdClass();

            $obj->product_title = $node->filter("h2.product-subtitle > a")->text();

            /*
            $result = $product->where('product_title', $obj->product_title)->get();

            if($result->count())
            {
                return;
            }
            */

            //$obj->brand_id = $this->getBrand( $obj->product_title );

            if($node->filter("img")->count())
            $obj->product_image = $node->filter("img")->attr('data-src');

            $obj->product_title = $node->filter("h2.product-subtitle > a")->text();
            $obj->product_link = $node->filter("h2.product-subtitle > a")->attr('href');
            $obj->new = true;


            $product->create((array)$obj);

            Log::info("new Product Created" . json_encode($obj) . "\n");
        });

    }

    function getNewBrand()
    {

        $client = new Client();
        $product = new Products();
        $productList = new ProductList();

        $last = "6377322dee807d960e0ee97e";

        $first = "6377322dee807d960e0eea9f";

        $product = $product->where('new', true );
        $product = $product->where('_id', '>' , $last );
        $product = $product->where('_id', '<' , $first );

        $product = $product->orderby("_id", 'desc');

        $Result = $product->get();

        Log::notice($Result->count() . ' Rows');

        foreach($Result as $Item ) {

            Log::warning("ACCESS :" . $Item->product_link );
            $crawler = $client->request('GET', $Item->product_link);


            if($crawler->filter("a[rel=tag]")->count())
            {
                $brand = $crawler->filter("a[rel=tag]")->first()->text();

                Log::info($Item->_id. " :: " . $brand);
                Products::where("_id", $Item->_id)->update(['brand_id', $brand]);
            }

            $this->saveProducts($crawler, $Item->id, $productList);
        }
    }



    function getProductDetail()
    {
        $product = new Products();

        $productList = new ProductList();


        $max = $productList->max('product_id');


        $Result = $product->where('_id', '>=', $max)->orderby('_id')->get();

        Log::emergency($Result->count()." Products will be Collecting..");

        foreach ($Result as $Item)
        {
            $this->productDetail($Item->_id, $Item->product_link);
        }
    }

    function parseMoney($String)
    {
        return (float) preg_replace('/[\$\,]/', '', $String);
    }

    function saveProducts($crawler, $id, $productList)
    {
        if($crawler->filter("table.rowprice")->count())
            $crawler->filter("table.rowprice")->each(function ($node) use ($id, $productList) {

                $Obj = new \stdClass();
                $Obj->product_id = $id;
                $Obj->product_type = "Box";

                $Obj->ebay_link =  $node->filter("a")->first()->attr('href');
                $Obj->item_title =  $node->filter("a")->first()->text();
                $Obj->item_price =  $this->parseMoney($node->filter("a")->eq(1)->text());


                $productList->create((array)$Obj);

                Log::warning("BOX ITEM: ". json_encode($Obj));


            });

        $crawler->filter("div.ak-ebay-columns-wrapper > div.ak-ebay-column")
            ->each(function ($node) use ($id, $productList) {

                $Obj = new \stdClass();

                $Obj->product_id = $id;
                $Obj->item_image = $node->filter("img")->last()->attr('src');
                $Obj->product_type = "Single Card";

                $Obj->ebay_link =  $node->filter("div.ak-ebay-link")->filter("a")->first()->attr('href');
                $Obj->item_title =  $node->filter("div.ak-ebay-link")->filter("a")->first()->text();
                // $Obj->item_price =  $node->filter("div.ak-ebay-link")->text();

                $Obj->item_price = $this->parseMoney(trim( str_replace($Obj->item_title,'' , $node->filter("div.ak-ebay-link")->text()) ));


                $productList->create((array)$Obj);

                Log::warning("BOX ITEM: ". json_encode($Obj));

                Log::info("CARD ITEM: ". json_encode($Obj));
            });

    }

    function productDetail($id, $link)
    {

        $client = new Client();

        $productList = new ProductList();

        Log::alert($link);

        $crawler = $client->request('GET', $link);

        $this->saveProducts($crawler, $id, $productList);
    }
}
