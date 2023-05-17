<?php

namespace Ausumsports\Admin\Crawler;

use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;


class ChicagoLand
{

    private $baseUrl;
    private $channel;
    private $result;

    public function __construct()
    {
        $this->channel = "chicagolandsportscards";
        $this->baseUrl = "https://www.chicagolandsportscards.com";
        $this->result = [];

    }

    public function search($keyword)
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
        $crawler = $client->request('GET', $this->baseUrl);


        $form = $crawler->filter("#search_mini_form")->form();
        $crawler = $client->submit($form, ['q' => $keyword]);

        Log::info($crawler->html());


        $this->parseScript($crawler);


        $paging = $crawler->filter('.pager');

        if ($paging->count())
            $paging->filter('li.current')
                ->nextAll('li')->each(function ($node) use ($client) {

                    if ($node->filter('a')->matches('.next')) return;


                    $link = $node->filter('a')->link();
                    $uri = $link->getUri();
                    Log::notice($uri);

                    $crawler = $client->click($link);
                    $this->parseScript($crawler);
                });


        return $this->result;
    }


    function parseScript($crawler)
    {

        Log::info("ChicagoLand SportsCards Parsing Start.................");



        $Count = $crawler->filter('ol.products-list')->filter('li.item')->count();

        Log::alert($Count . ' Row Found.');


        $crawler->filter('ol.products-list')->filter('li.item')
            ->each(function ($node) {

                //Log::info($node->html());

                $title = $node->filter('.product-name > a')->text();
                $detail = $node->filter('.product-name > a')->attr('href');

                $img = $node->filter('img')->attr('src');


                if($node->filter('div.price-box')->count())
                {
                    $price = $node->filter('div.price-box')->filter('.price')->text();

                }
                else
                {
                    $price = "Out of stock";
                }


/*
                if($price)
                {
                    $price = str_replace(",", "", $price);

                    preg_match('/(\d[\d.]*)/', $price, $matches);

                    $price =   '$'.number_format($matches[0], 2);

                }*/



                $msg = new \stdClass();

                $msg->channel = $this->channel;
                $msg->title = $title;
                $msg->img = $img;
                $msg->price = $price;
                $msg->detail = $detail;


                array_push($this->result, $msg);
                broadcast(new \Ausumsports\Admin\Events\Crawling(Json::encode($msg)));

                Log::debug($title . "\n", [$img, $price, $detail]);
            });
    }
}
