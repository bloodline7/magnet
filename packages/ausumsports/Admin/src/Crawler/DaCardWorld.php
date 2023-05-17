<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Http\PrintPer;
use Ausumsports\Admin\Models\Mongo\ShopItems;
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;


class DaCardWorld extends PrintPer
{

    private $baseUrl;
    private $channel;
    private $result;
    /**
     * @var ShopItems
     */
    private $mongo;
    /**
     * @var string
     */
    private $sports;

    public function __construct()
    {


        parent::__construct("Shop Items");

        $this->channel = "dacardworld";

        $this->baseUrl = "https://www.dacardworld.com/";
        $this->result = [];

        $this->mongo = new ShopItems();


        Log::info("Channel :: " . $this->channel);

    }

    public function get($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        return $crawler;
    }


    function dataReplace()
    {
        $Result = $this->mongo->get();

        $this->total = $Result->count();



        foreach ($Result as $Item)
        {
            $Price = str_replace(['$', ','], '', $Item->price);
            $Item->update(['price' => $Price]);

            $this->printPer();
        }



    }

    function getData()
    {

        $this->myslaps();

        return $this->dataReplace();

        $this->api = true;

        $baseUrl = "https://www.dacardworld.com/sports-cards/all-boxes-of-baseball-trading-cards";
        $this->sports = 'baseball';

        $max = 30;
        /*

        $baseUrl = "https://www.dacardworld.com/sports-cards/all-boxes-of-basketball-trading-cards";
        $this->sports = 'basketball';
        $max = 21;


        $baseUrl = "https://www.dacardworld.com/sports-cards/all-boxes-of-football-trading-cards";
        $this->sports = 'football';
        $max = 23;



        $baseUrl = "https://www.dacardworld.com/sports-cards/all-boxes-of-hockey-trading-cards";
        $this->sports = 'hockey';
        $max = 11;


        $baseUrl = "https://www.dacardworld.com/sports-cards/racing-trading-card-boxes";
        $this->sports = 'racing';
        $max = 2;

        $baseUrl = "https://www.dacardworld.com/sports-cards/ufc-and-mma-trading-cards";
        $this->sports = 'MMA';
        $max = 2;

        $baseUrl = "https://www.dacardworld.com/sports-cards/wrestling";
        $this->sports = 'wrestling';
        $max = 2;


        $baseUrl = "https://www.dacardworld.com/sports-cards/golf-trading-card-boxes";
        $this->sports = 'golf';
        $max = 1;

        $baseUrl = "https://www.dacardworld.com/sports-cards/other-sports";
        $this->sports = 'other';
        $max = 1;


        $baseUrl = "https://www.dacardworld.com/sports-cards/2017-and-earlier-soccer-trading-card-boxes";
        $this->sports = 'soccer';
        $max = 1;
*/



        /*




                $baseUrl = "https://www.dacardworld.com/sports-cards/other-sports";
                $this->sports = 'other';
                $max = 1;*/




        for ($i =1; $i<=$max; $i++)
        {

            if($i > 1)
            {
                $url = $baseUrl . '?Page='.$i;
            }
            else
            {

                $url = $baseUrl;
            }

            Log::warning($url);
            $page = $this->get($url);
            $this->parseScript($page);
        }
    }

    function myslaps()
    {


        $this->api = true;

        $baseUrl = "https://myslabs.com/search/wax?publish_type=1";

        $this->sports = 'baseball';

        $max = 72;


        for ($i =1; $i<=$max; $i++)
        {

            if($i > 1)
            {
                $url = $baseUrl . '&page='.$i;
            }
            else
            {
                $url = $baseUrl;
            }

            Log::warning($url);

            $page = $this->get($url);

            $this->parseScriptmySlabs($page);
        }

    }

    function parseScriptmySlabs($crawler)
    {

        Log::info("MySlabs Parsing Start.................");


        $crawler->filter('div.slab_item')
            ->each(function ($node) {

                $title = trim($node->filter('.slab-title')->text());
                $img = $node->filter('img')->attr('data-src');

                if(!$node->filter('.slab-price')->count()) return;

                $price = trim($node->filter('.slab-price')->text());


                $msg = new \stdClass();
                $msg->channel = 'MySlabs';

                //$msg->sports = $this->sports;

                $msg->title = $title;
                $msg->img = $img;
                $msg->price = $price;
                // $msg->detail = $detail;


                try {

                    Log::info(json_encode($msg));

                    $this->mongo->create((array)$msg);

                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());

                }


                //Log::debug($title . "\n", [$img, $price, $detail]);
            });
    }

    function parseScript($crawler)
    {

        Log::info("DaCardWorld Parsing Start.................");

        $crawler->filter('div.list-item')
            ->each(function ($node) {

                $title = $node->filter('.item-title')->text();
                $img = $node->filter('img')->attr('data-src');

                if(!$node->filter('strong.price')->count()) return;

                $price = $node->filter('strong.price')->text();
                //$detail = $node->filter('.button-group > li')->last()->filter('a')->attr('href');


                $msg = new \stdClass();
                $msg->channel = $this->channel;
                $msg->sports = $this->sports;

                $msg->title = $title;
                $msg->img = $img;
                $msg->price = $price;
               // $msg->detail = $detail;


                try {

                    Log::info(json_encode($msg));
                    $this->mongo->create((array)$msg);

                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());

                }


                //Log::debug($title . "\n", [$img, $price, $detail]);
            });
    }
}
