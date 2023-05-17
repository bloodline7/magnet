<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Models\Mongo\EbayItems;
use Ausumsports\Admin\Models\Mongo\EbayProducts;
use Ausumsports\Admin\Models\Mongo\PlayerReference;
use Ausumsports\Admin\Models\Mongo\ScCheckLists;

use Ausumsports\Admin\Http\PrintPer;
use DateTimeImmutable;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stringEncode\Exception;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;



class Reference extends PrintPer
{

    /**
     * @var int
     */
    private $process;
    private $limit;
    private $client;
    /**
     * @var PlayerReference
     */
    private $playerReference;

    private $maxRequest;
    private $api;
    /**
     * @var int
     */
    private $timeout;
    private $lastUrl;

    function __construct()
    {
        ini_set('memory_limit', '-1');

        parent::__construct("Reference");

        $this->client = new Client();
        $this->playerReference = new PlayerReference();
        $this->timeout = 10;
    }


    function getPlayers()
    {
        //$this->baseball();
        //$this->basketBall();

       // $this->football();

        //$this->hockey();

        $this->getDetail();

    }


    function hockey()
    {
        $domain = 'https://www.hockey-reference.com';
        $baseUrl = $domain.'/players/';

        $client = new Client();

        foreach (range('a', 'z') as $abc)
        {
            $url = $baseUrl . $abc . '/';

            $crawler = $client->request('GET', $url);

            Log::info($url);


            $crawler->filter("#div_players > p")->each(function ($node) use ($domain) {


                $text = $node->text();

                $Obj = new \stdClass();
                $Obj->sport  = 'Hockey';

                $Obj->link = $domain.$node->filter('a')->attr('href');
                $Obj->player = $node->filter('a')->text();


                $birth = preg_match("/([0-9]{4})\-([0-9]{4})/",$text, $match);

                if($birth)
                {
                    $Obj->from = (int)$match[1];
                    $Obj->to = (int)$match[2];

                }

                if($node->filter('b')->count()) $Obj->act = true;


                if(strpos($text, '+') > 0)
                {
                    $Obj->hallOfFamer = true;
                }

                Log::info(json_encode($Obj));


                try {

                   $this->playerReference->create((array)$Obj);

                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                }



            });
        }
    }




    function football()
    {
        $domain = 'https://www.pro-football-reference.com';
        $baseUrl = $domain.'/players/';

        $client = new Client();

        foreach (range('A', 'Z') as $abc)
        {
            $url = $baseUrl . $abc . '/';

            $crawler = $client->request('GET', $url);

            Log::info($url);


            $crawler->filter("#div_players > p")->each(function ($node) use ($domain) {


                $text = $node->text();

                $Obj = new \stdClass();
                $Obj->sport  = 'Football';

                $Obj->link = $domain.$node->filter('a')->attr('href');
                $Obj->player = $node->filter('a')->text();

                if(preg_match("/\(([^\)]+)\)/", $text, $Pos))
                {
                    if(sizeof($Pos) > 1)
                     $Obj->position = $Pos[1];
                }


                preg_match("/([0-9]{4})\-([0-9]{4})/",$text, $match);

                $Obj->from = (int)$match[1];
                $Obj->to = (int)$match[2];

                if($node->filter('b')->count()) $Obj->act = true;


                if(strpos($text, '+') > 0)
                {
                    $Obj->hallOfFamer = true;
                }

                Log::info(json_encode($Obj));


                try {

                    $this->playerReference->create((array)$Obj);

                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                }



            });
        }
    }



    function useApi($link)
    {

        if ($this->api) {
            $link = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=" . $link;
            $this->append("API");
        }

        return $link;
    }


    function getRequests($Items)
    {
        $client = new GuzzleHttp\Client();

        $requests = function ($Items) use ($client) {

            foreach ($Items as $Item)
            {
                yield $Item => function () use ($client, $Item) {
                    // Our identifier does not have to be included in the request URI or headers

                    $link = $this->useApi($Item->link);
                    return $client->getAsync($link);
                };
            }
        };


        $defaults = [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout
        ];

        $pool = new GuzzleHttp\Pool($client, $requests($Items), [
            'concurrency' => $this->maxRequest, //how many concurrent requests we want active at any given time
            'options' => $defaults,
            'fulfilled' => function ($response, $Item) {

            try {


                $crawler = new Crawler(null, $Item->link);

                $Contents = $response->getBody()->getContents();

                $crawler->addContent(
                    $Contents
                );


                $this->setDetail($Item,$crawler);

            } catch (\Exception $e) {
                    Log::error($e->getMessage());

                }


            },
            'rejected' => function ($response, $Item)  {

                Log::error("Url Error : " . $Item->link);
                $this->playerReference->where('_id', $Item->_id)->update(['Status' => 'UrlError']);

                $this->append("Error");
                $this->PrintPer(0);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    function unicodeString($str, $encoding=null) {
        if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match', 'return mb_convert_encoding(pack("H*", $match[1]), '.var_export($encoding, true).', "UTF-16BE");'), $str);
    }


    function setDetail($Item, $crawler)
    {

        $crawler = $crawler->filter("#info");
        $Obj = new \stdClass();


        $Image = $crawler->filter(".media-item img");

        $Img  = '';
        $Image->each(function ($node) use (&$Img)
        {
            if($Img) $Img .= ',';
            $Img .=$node->attr('src');
        });

        if($Img) $Obj->playerImages = $Img;

        $crawler->filter('p')->each( function ($node) use (&$Obj) {

            if($node->filter('strong')->count() == 1)
            {

                if($node->filter('strong')->filter('a')->count())
                {
                    return;
                }

                $title = $node->filter('strong')->text();
                $content = str_replace($title, '', $node->text());

                $title = Str::camel(trim(str_replace([' ', ':'], '', $title)));

                $content = trim(str_replace(':', '',$content));


                if($content)
                $Obj->$title = $content;
            }
            else if($node->filter('strong')->count() > 1)
            {
                    $html = $node->html();



                   $mt = preg_match("/<strong>([^<]+)<\/strong>([^<]+)<strong>([^<]+)<\/strong>([^<]+)/", $html, $match);

                   if($mt)
                   {

                       $title = Str::camel(trim(str_replace([' ', ':'], '', $match[1])));
                       $content = trim(str_replace([":", "\n"], '', $match[2]));

                       if($content)
                           $Obj->$title = $content;


                       $title = Str::camel(trim(str_replace([' ', ':'], '', $match[3])));
                       $content = trim(str_replace([":", "\n"], '',$match[4]));


                       if($content)
                           $Obj->$title = $content;


                   }




            }

        });

        if($crawler->filter("#necro-birth")->count()) {
            $Obj->birthDay  = $crawler->filter("#necro-birth")->attr('data-birth');
        }

        if($crawler->filter("#necro-death")->count()) {
            $Obj->deathDay  = $crawler->filter("#necro-death")->attr('data-death');
        }


        if($crawler->filter("div.stats_pullout")->count())
        {
            $stats = new \stdClass();

            $crawler->filter("div.stats_pullout div > div")->each( function ($node) use (&$stats)
            {
                $title = $node->filter('strong')->text();
                $content = $node->filter('p')->last()->text();
                $stats->$title = $content;
            });

            $Obj->stats = $stats;
        }

        if($crawler->filter("#bling > li")->count())
        {
            $bling = [];

            $crawler->filter("#bling > li")->each( function ($node) use (&$bling)
            {
                array_push($bling, $node->text());
            });

            $Obj->bling = $bling;
        }


        $Obj->Status = 'Complete';

        Log::info(json_encode($Obj));
        Log::debug($Item->link);


        $this->playerReference->where('_id', $Item->_id)->update((array)$Obj);

        $this->append("Complete");
        $this->PrintPer(0);
    }

    function getDetail()
    {

        $Reference = $this->playerReference;
        $Reference = $Reference->where("Status", '<>', 'Complete');
        $Reference = $Reference->orderBy("_id");

       // $Result = $Reference->get();
       // $client = new Client();

        $this->total = $Reference->count();

        $this->api = true;

        $Request = [];

        $Reference->chunkById(1000, function ($Result) use (&$Request)
        {

            foreach ($Result as $Item) {
                $Item = json_decode(json_encode($Item));

                if (isset($Item->stats)) {
                    $this->playerReference->where('_id', $Item->_id)->update(['Status' => 'Complete']);
                    continue;
                }

                array_push($Request, $Item);

                if (sizeof($Request) > 100) {
                    $this->getRequests($Request);
                    $Request = [];
                }
            }
        }
        );

        $this->getRequests($Request);



    }

    function baseball()
    {
        $domain = 'https://www.baseball-reference.com';
        $baseUrl = $domain.'/players/';

        $client = new Client();

        foreach (range('a', 'z') as $abc)
        {
            $url = $baseUrl . $abc . '/';

            $crawler = $client->request('GET', $url);

            Log::info($url);


            $crawler->filter("#div_players_ > p")->each(function ($node) use ($domain) {


                $text = $node->text();

                $Obj = new \stdClass();
                $Obj->sport  = 'Baseball';

                $Obj->link = $domain.$node->filter('a')->attr('href');
                $Obj->player = $node->filter('a')->text();

                preg_match("/\(([0-9]{4})\-([0-9]{4})/",$text, $match);

                $Obj->from = (int)$match[1];
                $Obj->to = (int)$match[2];

                if($node->filter('b')->count()) $Obj->act = true;


                if(strpos($text, '+') > 0)
                {
                    $Obj->hallOfFamer = true;
                }


                Log::info(json_encode($Obj));
                $this->playerReference->create((array)$Obj);

            });



        }



    }

    function basketBall()
    {
        $domain = 'https://www.basketball-reference.com';
        $baseUrl = $domain.'/players/';

        $client = new Client();

        foreach (range('a', 'z') as $abc)
        {
            $url = $baseUrl . $abc . '/';

            $crawler = $client->request('GET', $url);

            Log::info($url);


            $crawler->filter("#players > tbody > tr")->each(function ($node) use ($domain)
            {


                $Obj = new \stdClass();
                $Obj->sport  = 'Basketball';


                $Obj->link = $domain.$node->filter("th")->filter('a')->attr('href');
                $Obj->player = $node->filter("th")->filter('a')->text();

                $Obj->from = (int)$node->filter("td[data-stat=year_min]")->text();
                $Obj->to = (int)$node->filter("td[data-stat=year_max]")->text();

                $Obj->position = $node->filter("td[data-stat=pos]")->text();
                $Obj->height = $node->filter("td[data-stat=height]")->text();
                $Obj->weight = $node->filter("td[data-stat=weight]")->text();

                $BirthDay =$node->filter("td[data-stat=birth_date]")->attr('csk');

                $Obj->birthDay2 = $BirthDay;

                $Obj->birthDay = substr($BirthDay, 0, 4).'-'.substr($BirthDay, 4, 2).'-'.substr($BirthDay, -2, 2);

                $Obj->colleges = $node->filter("td[data-stat=colleges]")->text();

                if($node->filter("th")->filter('strong')->count())
                    $Obj->act = true;

                if(strpos($node->filter("th")->text(), '*') > 0)
                {
                    $Obj->hallOfFamer = true;
                }


                Log::info(json_encode($Obj));
                $this->playerReference->create((array)$Obj);

            });



        }



    }

}
