<?php

namespace Ausumsports\Admin\Crawler;

use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
//use GuzzleHttp\Client;
//use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\CookieJar;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;
use Symfony\Component\BrowserKit\HttpBrowser;

class BlowoutCards
{

    private $baseUrl;
    private $channel;
    private $result;
    private $api;

    public function __construct()
    {
        $this->channel = "blowoutcards";
        $this->baseUrl = "https://www.blowoutcards.com";
        $this->result = [];

    }



    function useApi($link)
    {

        if ($this->api) {
            $link = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=" . $link;
        }

        return $link;
    }



    public function get($url)
    {

       /* $cookieJar = new CookieJar();


        $cookie = new Cookie('visid_incap_1171487', 'McJAth1+TDaYfeaJhYn1mmMh3GAAAAAAQkIPAAAAAACA0kSdAbdiDtXzj5xtNL4ylKOCsJgJmKfh', strtotime('+1 day'));
        $cookieJar->set($cookie);

        $cookie = new Cookie('incap_ses_948_1171487', 'cj2QZ1v+NHbiR2sFXPknDWMh3GAAAAAA62RCDh121hqv3bjc9Tuh/g==', strtotime('+1 day'));
        $cookieJar->set($cookie);

        $cookie = new Cookie('_GRECAPTCHA', '09ABU7dzPv6e1Bn4orLFiZ01_bGr4fcAuK4IN9LH5MoXpMStB6KPAzwszAllZOoofbtDOvj-r8DKIzH53q-WcGNuE', strtotime('+1 day'));
        $cookieJar->set($cookie);*/



        $client = new Client();

        $cookie = new Cookie("visid_incap_1171487", 'McJAth1+TDaYfeaJhYn1mmMh3GAAAAAAQkIPAAAAAACA0kSdAbdiDtXzj5xtNL4ylKOCsJgJmKfh', null, "/", ".blowoutcards.com", true, true);
        $client->getCookieJar()->set($cookie);

        $cookie = new Cookie("incap_ses_948_1171487", 'cj2QZ1v+NHbiR2sFXPknDWMh3GAAAAAA62RCDh121hqv3bjc9Tuh/g==', null, "/", ".blowoutcards.com", true, false);
        $client->getCookieJar()->set($cookie);

        $cookie = new Cookie("frontend", 'e743b9e740ca95dda37c9e3357445ef1', null, "/", ".www.blowoutcards.com", false, true);
        $client->getCookieJar()->set($cookie);


        $client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36');

        /*$buff = preg_split('/\s+/',$keyword,-1,PREG_SPLIT_NO_EMPTY);

        $url = $this->baseUrl."/catalogsearch/result/?q=".implode("+", $buff);
        */

        //$url = $this->useApi($url);


        $crawler = $client->request('GET', $url);

        return $crawler;








        $this->parseScript($crawler);


        $paging = $crawler->filter('div.pages');

        if ($paging->count())
            $paging->filter('li.current')
                ->nextAll('li')->each(function ($node) use ($client) {

                    if ($node->matches('.next')) return;

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

        Log::info("Blowout Cards Parsing Start.................");

        $crawler->filter('div.category-products > ul.products-grid li')
            ->each(function ($node) {

                Log::error($node->html());
                $title = $node->filter('.product-name > a')->text();
                $img =  $this->baseUrl.$node->filter('img')->attr('src');

                if($node->filter('span.price')->count())
                {
                    $price = trim($node->filter('span.price')->text());

                }
                else
                {
                    $price = "out of stock";
                }


                $detail = $node->filter('.product-name > a')->attr('href');

                $msg = new \stdClass();
                $msg->channel = $this->channel;
                $msg->title = $title;
                $msg->img = $img;
                $msg->price = $price;
                $msg->detail = $detail;


                Log::info(json_encode($msg));

            });
    }

    function getData()
    {

        $this->api = true;

        $baseUrl = "https://www.blowoutcards.com/sports-cards/baseball-cards.html";

        for ($i =1; $i<23; $i++)
        {

            if($i > 1)
            {
                $url = $baseUrl . '?p='.$i;
            }
            else
            {

                $url = $baseUrl;
            }


            Log::warning($url);


            $page = $this->get($url);


            Log::debug($page->html());

            $this->parseScript($page);
        }


    }
}
