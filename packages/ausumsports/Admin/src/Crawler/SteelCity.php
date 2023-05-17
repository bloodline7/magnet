<?php

namespace Ausumsports\Admin\Crawler;

use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;


class SteelCity
{

    private $baseUrl;
    private $channel;
    private $result;

    public function __construct()
    {
        $this->channel = "steelcitycollectibles";
        $this->baseUrl = "https://www.steelcitycollectibles.com";
        $this->result = [];

    }

    public function search($keyword)
    {
        $client = new Client();
        $crawler = $client->request('GET', $this->baseUrl."/search?q=".$keyword);

/*
        $form = $crawler->filter("#srch-form-dk")->form();
        $crawler = $client->submit($form, ['q' => $keyword]);*/



        $this->parseScript($crawler);
        $paging = $crawler->filter('ul.pagination');

        if ($paging->count())
            $paging->filter('li.active')
                ->nextAll('li')->each(function ($node) use ($client) {

                    if ($node->matches('.arrow')) return;

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

        Log::info("SteelCityCollectibles Parsing Start.................");



        $Count = $crawler->filter('#search-results')->filter('div.pr')->count();

        Log::alert($Count . ' Row Found.');


        $crawler->filter('#search-results')->filter('div.pr')
            ->each(function ($node) {

                //Log::info($node->html());

                $title = $node->filter('.pr-title > a')->text();


                //Log::debug($title);


                $img = $node->filter('img')->attr('data-srcset');

                if($node->filter('span.sale-price')->count())
                {
                    $price = $node->filter('span.sale-price')->text();

                }
                else
                {
                    $price = $node->filter('div.pr-price')->text();
                }


                if($price)
                {
                    $price = str_replace(",", "", $price);


                    preg_match('/(\d[\d.]*)/', $price, $matches);



                    $price =   '$'.number_format($matches[0], 2);

                }

                $detail = $node->filter('.pr-title > a')->attr('href');

                $msg = new \stdClass();

                $msg->channel = $this->channel;
                $msg->title = $title;
                $msg->img = $img;
                $msg->price = $price;
                $msg->detail = $this->baseUrl.$detail;


                array_push($this->result, $msg);

                 broadcast(new \Ausumsports\Admin\Events\Crawling(Json::encode($msg)));

                Log::debug($title . "\n", [$img, $price, $detail]);
            });
    }
}
