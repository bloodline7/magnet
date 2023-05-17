<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Models\Mongo\Soccer as SoccerModel;

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




class Soccer extends PrintPer
{

    /**
     * @var int
     */
    private $process;
    private $limit;
    private $client;

    private $soccerModel;

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

        parent::__construct("Soccer");

        $this->client = new Client();
        $this->soccerModel = new SoccerModel();
        $this->timeout = 10;
    }


    function getPlayers()
    {

        //$this->getPlayer();

        //$this->getPlayerList();

        $this->parseDetail();

    }


    function textClean($Text)
    {
        $Text = str_replace('&nbsp;', '' , $Text);
        $Text = str_replace('▪', '' , $Text);
        return str_replace("\u{00a0}", '' , $Text);
    }

    function cleaner()
    {

        $Model = $this->soccerModel->orderby('_id');

        $this->total = $Model->count();

        $Model->chunkbyId(1000, function ($Result) {

            foreach ( $Result as $Item) {

                $Obj = new \stdClass();

                if(isset($Item->citizenship))
                {
                   $Obj->citizenship = $this->textClean($Item->citizenship);
                }

                if(isset($Item->position))
                {
                    $Obj->position = $this->textClean($Item->position);
                }

                if(isset($Item->youthNationalTeam))
                {
                    $Obj->youthNationalTeam = $this->textClean($Item->youthNationalTeam);
                }

                if(isset($Item->nationalTeam))
                {
                    $Obj->nationalTeam = $this->textClean($Item->nationalTeam);
                }


//                Log::info(json_encode($Obj));
                $Item->update((array)$Obj);

                $this->printPer();
            }


        });



    }


    function getPlayerList()
    {
        $domain = 'https://fbref.com';
        $baseUrl = $domain.'/en/players/';

        $client = new Client();


        $crawler = $client->request('GET', $baseUrl);


        $crawler->filter(".page_index a")->each(function ($node) use ($domain)
        {

            $Obj = new \stdClass();

            $Url = $domain.$node->attr('href');

            $Obj->type = "topLink";
            $Obj->topLink = $Url;

            $this->soccerModel->create((array)$Obj);

            Log::info("URL :" . $Url);

        });
    }


    function parseDetail()
    {
        $model = $this->soccerModel->where('type', 'player');
        $model = $model->whereNull('Status');
        $model = $model->orderBy('_id', 'asc');

        $this->total = $model->count();

        $this->api = true;

        $this->timeout = 10;
        $this->maxRequest = 32;

        $Items = [];

        $model->chunkbyId(1000, function ($Result) use (&$Items) {

            foreach(  $Result as $Item )
            {
                $Item = json_decode(json_encode($Item));

                array_push($Items, $Item);


                if(sizeof($Items) > 1000)
                {
                    $this->getRequests($Items);
                    $Items = [];
                }
            }

        });

        $this->getRequests($Items);

    }

    function getPlayer()
    {
        $domain = 'https://fbref.com';
        $baseUrl = $domain.'/en/players/';

        $client = new Client();


        $Result = $this->soccerModel->where('type','topLink')->orderBy('_id', 'asc')->get();


        $this->total = $Result->count();

        $this->api = true;


        foreach(  $Result as $Item )
        {

            $Item = json_decode(json_encode($Item));


            $Player = $this->soccerModel->where('type','player')->where('topLink', $Item->topLink)->get();

            if($Player->count())
            {
                $this->soccerModel->where('_id', $Item->_id)->update(['Status' => 'Complete']);
                $this->printPer(0);
                continue;
            }


            $ItemAdded = 0;


            $crawler = $client->request('GET', $this->useApi($Item->topLink));

            $crawler->filter(".section_content > p")->each(function ($node) use ($domain, $Item, &$ItemAdded)
            {

                $Obj = new \stdClass();

                $Obj->link = $domain.$node->filter('a')->attr('href');
                $Obj->player = $node->filter('a')->text();
                $Obj->type = "player";
                $Obj->topLink = $Item->topLink;

                if(preg_match("/([0-9]{4})\-([0-9]{4})/",$node->text() , $match))
                {
                    $Obj->from = (int)$match[1];
                    $Obj->to = (int)$match[2];
                }

                if($node->filter('a strong')->count()) $Obj->act = true;

                try {
                    $this->soccerModel->create((array)$Obj);
                    $ItemAdded++;

                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                }

                //Log::info("" . json_encode($Obj));

            });

            $this->printPer(0);

            if($ItemAdded > 2)
            $this->soccerModel->where('_id', $Item->_id)->update(['Status' => 'Complete']);
            else
                $this->soccerModel->where('_id', $Item->_id)->update(['Status' => 'Error']);

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

                $this->append("Empty");
                $this->PrintPer(0);

                 $this->soccerModel->where('_id', $Item->_id)->update(['Status' => 'Empty']);


                }

            },
            'rejected' => function ($response, $Item)  {

                Log::error("Url Error : " . $Item->link);
                $this->soccerModel->where('_id', $Item->_id)->update(['Status' => 'UrlError']);

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

                if ($node->filter('p')->count() > 1 )
                {
                    $content = [];
                    $node->filter('p')->each(function ($node) use (&$content) {
                        array_push($content, $node->text());
                    });
                }
                else
                {
                    $content = $node->filter('p')->text();
                }

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

        $this->soccerModel->where('_id', $Item->_id)->update((array)$Obj);

        $this->append("Complete");
        $this->PrintPer(0);
    }



}
