<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Http\PrintPer;
use Ausumsports\Admin\Models\Mongo\EbayProducts;
use GuzzleHttp\RedirectMiddleware;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Illuminate\Support\Facades\Storage;
use MongoDB\Driver\Exception\BulkWriteException;
use Psy\Util\Json;
use stringEncode\Exception;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\HttpBrowser;

use function PHPUnit\Framework\throwException;

class Ebay extends PrintPer
{

    private $baseUrl;
    private $channel;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var string
     */
    private $CardList;
    private $BoxList;
    private $CaseList;
    /**
     * @var int
     */
    private $maxRequest;

    private $db;
    /**
     * @var int
     */
    private $delay;
    /**
     * @var false
     */
    private $synchronous;
    /**
     * @var string
     */
    private $cookie;
    private $referer;
    /**
     * @var int
     */
    private $timeOut;
    /**
     * @var int
     */
    private $noMore;
    /**
     * @var bool
     */
    private $api;

    public function __construct()
    {
        $this->db = new EbayProducts();
        $this->channel = "Ebay";

        $this->CardList = [
            "https://www.ebay.com/b/Trading-Card-Singles/261328?Graded=Yes&LH_Complete=1&LH_Sold=1&mag=1&rt=nc&_dmd=2"
        ];

        $this->BoxList = [
            "https://www.ebay.com/b/Baseball-Sealed-Sports-Trading-Card-Boxes/261332/bn_16961389?LH_Complete=1&LH_Sold=1&mag=1&rt=nc&LH_ItemCondition=1000",
            "https://www.ebay.com/b/Sealed-Sports-Trading-Card-Cases/261333/bn_7117727398?LH_Complete=1&LH_Sold=1&rt=nc&LH_ItemCondition=1000",
        ];

        $this->api = false;

        parent::__construct("Ebay Crawling....");
        Log::info("Channel :: " . $this->channel);
    }

    public function getSeasonCardList($List = null)
    {
        ini_set('memory_limit', '-1');
        parent::__construct("Ebay List Crawling by API....");

        $Lists =  ($List) ? $List : $this->CardList;
        $this->timeOut = 30;

        if(!$List)
        $Lists = $this->setSports($Lists);
        $Lists = $this->setSeason($Lists, 1930, 1939, 'Mix');
        $Lists = $this->setOrderSeason($Lists);
        $this->total = count($Lists);

        Log::info("TOTAL : " . $this->total);


        foreach ($Lists as $list)
        {
            $this->noMore = 0;

            for ($i = 1; $i <= 210; $i++) {

                $link = ($i > 1) ? $list . '&_pgn=' . $i : $list;
                Log::warning($link);

                $api = ($i > 4);

                try {

                    if($this->getRequestByClient($link, $api)) continue;
                    break;
                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                    Log::error("URL :" . $link);

                    continue;
                }
            }
        }

        if(!$List)
            $this->getSeasonCardList($this->BoxList);

    }


    function setSports($input): array
    {
        $return = [];
        $Sports = $this->getSports();

        if(is_array($input))
        {
            foreach ($input as $Link)
            {
               // array_push($return, $Link );

                foreach ($Sports as $list)
                {
                    $setLink = $Link . "&Sport=".$list;
                    array_push($return, $setLink );
                }

                array_push($return, $Link );
            }

        }
        else {
            foreach ($Sports as $list)
            {
                $Link = $input . "&Sport=".$list;
                array_push($return, $Link);
            }
            array_push($return, $input );
        }

        return $return;
    }


    function setOrderSeason($input): array
    {
        $return = [];
        $order = ['10', '16'];

        if(is_array($input))
        {
            foreach ($input as $Link)
            {
                foreach ($order as $list)
                {
                    $setLink = $Link . "&_sop=".$list;
                    array_push($return, $setLink);
                }
            }
        }
        else {
            foreach ($order as $list)
            {
                $Link = $input . "&&_sop=".$list;
                array_push($return, $Link);
            }
        }

        return $return;
    }

    function setOrder($input): array
    {
        $return = [];
        //$order = ['10', '16'];
        $order = ['13'];

        if(is_array($input))
        {
            foreach ($input as $Link)
            {
                foreach ($order as $list)
                {
                    $setLink = $Link . "&_sop=".$list;
                    array_push($return, $setLink);
                }
            }
        }
        else {
            foreach ($order as $list)
            {
                $Link = $input . "&&_sop=".$list;
                array_push($return, $Link);
            }
        }

        return $return;
    }



    function getRequestByClient($url, $api=false, $retry=false)
    {
        $client = new GuzzleHttp\Client();


        if($api)
        {
            $url = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=".$url;
        }

        $res = $client->request('GET', $url, [
            'timeout' => $this->timeOut,
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate, br',
            ]
        ]);

        $Code = $res->getStatusCode();

        if($Code === 200 )
        {
            $Contents = $res->getBody();
            $M = preg_match("!<ul class=\"b-list__items_nofooter srp-results srp-grid\">(.*?)<\/ul>!is", $Contents, $match);

            if($M)
            {
                $Contents = $match[0];
                $this->append("FOUND");
                $crawler = new Crawler(null, $url);

                $crawler->addContent(
                    $Contents
                );

                return $this->itemSave($crawler);

            }
            else
            {
                if($api)
                {
                    Log::error("NOT FOUND EVEN API");
                    $this->append("NOT_FOUND");
                    $this->printPer(0);

                    sleep(2);

                    return $this->getRequestByClient($url, false, true);
                }
                else
                {

                    if($retry)
                    {
                        $this->append("Retried");
                        $this->printPer(0);

                        return false;
                    }

                    $this->append("API_CALL");

                    return $this->getRequestByClient($url, true);
                }
            }
        }

        Log::error("CODE : " . $Code );

        $this->printPer();
        return false;
    }


    function setSeason($input, $Start, $End, $Con): array
    {
        $return = [];
        $season = $this->getSeason($Start, $End, $Con);

        if(is_array($input))
        {

            foreach ($input as $Link)
            {
                   foreach ($season as $list)
                   {
                       $newLink = $Link . "&Season=".$list;
                       array_push($return, $newLink);
                   }
            }
        }
        else {
            foreach ($season as $list)
            {
                $setLink = $input . "&Season=".$list;
                array_push($return, $setLink);
            }
        }

        return $return;
    }

    /**
     * @param int $Start Start Year
     * @param int $End End Year
     * @param mixed $Con Continue Year default Mix
     * @return array
     */
    function getSeason(int $Start, int $End=2023, $Con="Mix"): array
    {
        $Season = [];

        for ($year = $Start; $year <= $End; $year++) {

            if($Con === false || $Con == "Mix")
            array_push($Season, (string)$year);

            if($Con === true || $Con == "Mix")
            {
                $next = $year + 1;
                $combine = $year . '%252D' . substr($next, 2, 2);
                array_push($Season, (string)$combine);
            }
        }

        return $Season;
    }

    function getSports(): array
    {
        return [
            "Baseball",
            "Basketball",
            "Football",
            "Ice%2520Hockey",
            "Soccer",
            "Wrestling",
            "Mixed%2520Martial%2520Arts%2520%2528MMA%2529",
            "Bowling",
            "Boxing",
            "Golf",
            "Auto%2520Racing",
            "Tennis",
            "Australian%2520Football"

        ];
    }


    public function getCardList($List = "")
    {
        ini_set('memory_limit', '-1');
        parent::__construct("Ebay List Crawling by API....");

        $Lists =  ($List) ? $List : $this->CardList;
        $this->timeOut = 30;

        if(!$List)
            $Lists = $this->setSports($Lists);


        $Lists = $this->setOrder($Lists);
        $this->total = count($Lists);


        Log::info("TOTAL : " . $this->total);


        foreach ($Lists as $list)
        {
            $this->noMore = 0;

            for ($i = 1; $i <= 210; $i++) {

                $link = ($i > 1) ? $list . '&_pgn=' . $i : $list;
                Log::warning($link);

                $api = ($i > 4);

                try {

                    if($this->getRequestByClient($link, $api)) continue;
                    break;
                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                    Log::error("URL :" . $link);

                    continue;
                }
            }
        }

        if(!$List)
            $this->getCardList($this->BoxList);

    }


    public function getBoxList()
    {

        parent::__construct("Ebay Box List Crawling....");


        $this->delay = 5000;
        $this->total = 0;
        $this->maxRequest = 1;
        $this->synchronous = true;

        $this->getCardList($this->BoxList);

    }


    function itemSave($crawler)
    {
        $list = $crawler->filter("ul.srp-results li.s-item");

        if (!$list->count()) {

            $body = $crawler->filter("div.container")->first();

            if ($body->count())
                Log::info($body->html());
            else
                Log::warning("Not Found Body Part");

            Log::emergency(" No Items - End Of Job");
            $this->append("NoItem");

            $this->printPer(0);
            return;
        }

        $Rows = $list->count();
        $this->printPer(0, false);

        $added = 0;
        $list->each(function ($node) use (&$added) {

            $Obj = new \stdClass();
            $link = explode("?", $node->filter('a')->first()->attr('href'))[0];
            $Obj->EbayItemUrl = $link;
            $Arr = explode('/', $link);
            $Obj->EbayItemNumber = end($Arr);

            try {
                $this->db->create((array)$Obj);
                $this->append("List");
                $added++;

            } catch (\Exception $e) {
                // Log::error($e->getMessage());
                $this->append("DubItemNumber");
            }

        });


        $this->printPer();

        if ($Rows < 48 )
        {
            Log::alert("Last Page : " . $Rows . " Rows");
            return false;
        }




        if($added == 0)
        {
            Log::alert("No More Added Items");

            if( $this->noMore > 5 )
            {

                Log::error("No Items.... Go to Next Step ");
                $this->printPer(0, false);

                return false;
            }
            else
            {
                $this->noMore++;
            }
            $this->append("APPEND" ,  0 . " [0%]");
        }
        else {

            $this->noMore = 0;
            $this->append("APPEND" ,  $added . " [". round( ($added / 48 ) * 100 ) . '%]');
            $this->printPer(0, false);

        }

        return true;
    }


    function itemRejected($order, $process)
    {

        $this->synchronous = ($process == 1);
        $this->maxRequest = $process;
        $this->delay = 0;
        $this->limit = ($process == 1) ? 10 : 1000;
        $this->api = true;

        $db = $this->db;


        $db = $db->where('Status', '=', 'Rejected');
        $db = $db->orderBy("_id", $order);

        $Result = $db->take($this->limit)->get();

        $this->total = $Result->count();
        $Arr = [];

        foreach ($Result as $Item)
        {
            array_push($Arr, $Item);
        }

        $this->getRequests($Arr);

        if($this->limit == $this->total)
        {
            $this->itemRejected($order, $process);
        }

        Log::info("Item Rejected Crawling Complete.");

        return true;
    }



    function itemEmpty($order, $process)
    {

        Log::info("Item Empty Check Again");

        $this->synchronous = ($process == 1);
        $this->maxRequest = $process;
        $this->delay = 0;
        $this->limit = ($process == 1) ? 10 : 1000;
        $this->api = true;


        $db = $this->db;


        $db = $db->where('Status', '=', 'Empty');
        $db = $db->orderBy("_id", "desc");

        $this->total = $db->count();

        Log::info("Item Empty Check Again");


        $db->chunkById(1000, function ($Result) {

            $Arr = [];

            foreach ($Result as $Item)
            {
                array_push($Arr, $Item);

                if(!(sizeof($Arr)%$this->limit))
                {
                    $this->getRequests($Arr);
                    $Arr = [];
                }
            }

            $this->getRequests($Arr);

            Log::info("Item Empty Check Again");

        });


        return true;
    }

    function itemDetail($orderBy="", $limit=0, $process=10)
    {
        $this->synchronous = false;
        $this->maxRequest = $process;
        $this->delay = 0;
        $this->api = false;


        $db = $this->db->whereNull('Status');

       // $db = $db->orWhere('Status', 'EmptyCheck');

        if($orderBy)
        {
            $db = $db->orderBy("_id", $orderBy);
        }

        $Arr = [];

        if($limit)
        {
            $Result = $db->take($limit)->get();
            $this->total = $Result->count();

            foreach ($Result as $Item)
            {

              // $Item->EbayItemUrl = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=". $Item->EbayItemUrl;

                array_push($Arr, $Item);
            }
            $this->getRequests($Arr);
            return 0;
        }


        //$db = $db->orderBy("_id", 'asc');
        $this->total = $db->count();


        $db->chunkById(1000, function ($Result) use (&$Arr) {

            foreach ($Result as $Item)
            {

                if($Item->Status)
                {
                    Log::critical("OverLoad Item : ". json_encode($Item));
                    $this->printPer(0);
                    continue;
                }

                array_push($Arr, $Item);
                if((sizeof($Arr) % 1000) == 0)
                {
                    $this->getRequests($Arr);
                    $Arr = [];
                }

            }
        });

        $this->getRequests($Arr);

        return 0;
    }



    function itemEmptyDetail($orderBy="", $limit=0, $process=10, $skip = 0)
    {
        $this->synchronous = false;
        $this->maxRequest = $process;
        $this->delay = 0;
        $this->api = true;

        $db = $this->db->where('Status', "EmptyCheck");

        if($orderBy)
        {
            $db = $db->orderBy("_id", $orderBy);
        }

        $Arr = [];

        if($limit)
        {

            if($skip)
            {
                $db = $db->skip($skip * $limit);
                $this->append("Skip", $skip * $limit );
            }

            $Result = $db->take($limit)->get();
            $this->total = $Result->count();

            foreach ($Result as $Item)
            {
               // $Item->Retry = 1;

                // $Item->EbayItemUrl = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=". $Item->EbayItemUrl;

                array_push($Arr, $Item);
            }


            $this->getRequests($Arr);

            $this->itemEmptyDetail($orderBy, $limit, $process, $skip);

            return 0;
        }


        //$db = $db->orderBy("_id", 'asc');
        $this->total = $db->count();


        $db->chunkById(1000, function ($Result) use (&$Arr) {

            foreach ($Result as $Item)
            {

                if($Item->Status)
                {
                    Log::critical("OverLoad Item : ". json_encode($Item));
                    $this->printPer(0);
                    continue;
                }

                array_push($Arr, $Item);
                if((sizeof($Arr) % 1000) == 0)
                {
                    $this->getRequests($Arr);
                    $Arr = [];
                }

            }
        });

        $this->getRequests($Arr);

        return 0;
    }



    function ItemUpdate($crawler, $Set)
    {

        try {

            if(!$crawler->filter("#icImg")->count())
            {

                Log::emergency("No Image :".json_encode($Set));
                $this->printPer(0);

                return $this->setStatus($Set->_id, "Empty", $Set);
            }

        }
        catch (\Exception $e)
        {
            Log::emergency($e->getMessage());

            return $this->setStatus($Set->_id, "EmptyError", $Set);
        }


        try {


            $Obj = new \stdClass();

            if(!$crawler->filter("#vi-lkhdr-itmTitl")->count())
            {
                return $this->setStatus($Set->_id, "Block");
            }

            $Obj->ItemTitle = $crawler->filter("#vi-lkhdr-itmTitl")->text();

            $Obj->ItemCondition = $crawler->filter("#vi-itm-cond")->text();
            $Obj->ItemImage = $crawler->filter("#icImg")->attr('src');


            $time = $crawler->filter("span.timeMs");

            if ($time) {

                $Obj->Ended = date("d.m.Y H:i:s", ((int)$time->attr('timems'))/1000);
                $Obj->EndedAt = new \MongoDB\BSON\UTCDateTime((int)$time->attr('timems'));
            }

            if($crawler->filter("div.vi-price-np")->count())
                $Obj->SoldPrice = (float)preg_replace(['/US/', '/AU/', '/C/', '/\$/', '/\,/'], "", $crawler->filter("div.vi-price-np")->first()->text());

            else if($crawler->filter("#prcIsum")->count())
            {

                if($crawler->filter("#prcIsum")->attr("style"))
                {
                    return $this->setStatus($Set->_id, "OfferPrice");
                }

                $Obj->SoldPrice = (float)preg_replace(['/US/', '/AU/', '/C/', '/\$/', '/\,/','/\s/'], "", $crawler->filter("#prcIsum")->text());
            }
            else
                $Obj->SoldPrice = (float)$crawler->filter("[itemprop=price]")->attr('content');


            if($crawler->filter("#fshippingCost")->count())
            {
                $Obj->Shipping = $crawler->filter("#fshippingCost")->text();
            }

            $crawler->filter("div.ux-layout-section__row")->each(function ($node) use ($Obj) {

                $i = 0;
                $node->filter('div.ux-labels-values__labels')->each(function ($divnode) use ($node, &$i, &$Obj) {

                    $Key = trim(preg_replace(['/\:/', '/\s/'], '', $divnode->text()));
                    $Value = $node->filter('div.ux-labels-values__values')->eq($i)->text();
                    $Obj->$Key = $Value;
                    $i++;
                });
            });

            $this->setItemUpdate($Set->_id, $Obj);


        }
        catch (\Exception $e)
        {


        }



    }


    function setItemUpdate($id, $Obj)
    {
        $Obj->Status = 'Complete';
        $this->append("Complete");

        try {

            $this->db->where("_id", $id)->update((array)$Obj);
        }
        catch (BulkWriteException $e)
        {
            Log::emergency($e->getMessage());
            return $this->setStatus($id, "EmptyError");
        }

        $this->printPer();

    }

    function setStatus($id, $Status , $Set=null): bool
    {
        $this->append($Status);


        $update = ['Status' => $Status];
        if($Set && $Status == "Rejected")
        {
            $this->append("Retry");
            $update['Retry'] = ($Set->Retry) ? $Set->Retry+1 : 1;
        }


        $this->db->where("_id", $id)->update($update);
        $this->printPer();


        return true;
    }

    function getRequests($Links)
    {

        $requests = [];

        foreach ($Links as $Link) {

            $Link = json_decode(json_encode($Link));

            if (isset($Link->EbayItemUrl))
            {

                if($this->api)
                {
                    $Link->EbayItemUrl =  "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=". $Link->EbayItemUrl;
                    $this->append("API");
                }

                array_push($requests, new Request('GET', $Link->EbayItemUrl));

            }
            else {

                array_push($requests, new Request('GET', $Link));

            }

        }


        /* $curl = new \GuzzleHttp\Handler\CurlMultiHandler();
         $handler = \GuzzleHttp\HandlerStack::create($curl);*/

        $defaults = [
            'allow_redirects' => [
                'max' => 15,
                'strict' => true,
                'referer' => false,
                'protocols' => ['http', 'https'],
                'track_redirects' => false
            ],
          //  'http_errors' => true,
          //  'verify' => true,
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Encoding' => 'gzip, deflate',
                'Cookie' => "nonsession=BAQAAAYQ/7+h2AAaAADMABWWfo+IwNDYyMQDKACBngNdiOWZlNTc5MGIxODUwYTY0NTRiNmRkNjJkZmZmZTZkNzkAywACY753ajE5TLKYiC0gDZtmkuXOnUuMrDk5g/4*;dp1=bbl/KRen-US6780d762^",
            ],
            'decode_content' => true,
            'timeout' => 10,
            'connect_timeout' => 10,
            'delay' => $this->delay,
            'expect' => $this->synchronous,
            'synchronous' => $this->synchronous
        ];

        $client = new GuzzleHttp\Client();

        $pool = new GuzzleHttp\Pool($client, $requests, [
            'concurrency' => $this->maxRequest,
            'options' => $defaults,
            'fulfilled' => function ($response, $index) use (&$Links) {

               // Log::info("get Data:". $Links[$index]);


                $this->append("Full");

                $current = $Links[$index];

                $detail = (isset($current->EbayItemUrl));


                $crawler = new Crawler(null, ($detail) ? $current->EbayItemUrl : $current);

                $Contents = $response->getBody()->getContents();



                //Log::notice("get Contents:". $Links[$index]);

                if($detail)
                {

                    $M = preg_match("!exceeded the number of requests allowed in one day.!is", $Contents, $match);

                    if($M)
                    {
                        if(!$this->api)
                        {
                            $this->api = true;
                            return $this->getRequests($Links);
                        }

                        return false;
                    }


                    $Code = $response->getStatusCode();

                    if($Code !== 200)
                    {
                        Log::error('Code :'. $Code );

                        $this->append($Code);

                        $this->printPer(0);
                        sleep(5);

                        return false;
                    }

                    $crawler->addContent(
                        $Contents
                    );

//                    unset($Links[$index]);

                    $this->printPer(0, false);
                    $this->itemUpdate($crawler, $current);
                }
                else
                {
                    $M = preg_match("!<ul class=\"b-list__items_nofooter srp-results srp-grid\">(.*?)<\/ul>!is", $Contents, $match);

                    if ($M) {

                        $Contents = $match[0];
                        $crawler->addContent(
                            $Contents
                        );

                        Log::info("Added Html Contents");
                        $this->printPer(0, false);
                        $this->itemSave($crawler);

                    } else
                    {


                        if(str_contains($Contents, "srp-results "))
                        {
                            $crawler->addContent(
                                $Contents
                            );

                            Log::error("Added Html Contents");
                            $this->printPer(0, false);
                            $this->itemSave($crawler);
                        }
                        else
                        {

                            Log::error("Content Unmatched :     " . $current);
                            $this->append("ContentError");
                            $this->printPer(0);
                        }


                        return;
                    }
                }



            },
            'rejected' => function ($response, $index) use ($Links) {


                $this->append("Error");

                $this->PrintPer(0, false);



                $current = $Links[$index];
                $detail = (isset($current->EbayItemUrl));

                if($detail)
                {

                    if ($response instanceof GuzzleHttp\Exception\ClientException)
                    {
                        $body = $response->getResponse()->getBody();

                        $M = preg_match("!We looked everywhere.!is", $body, $match);

                        if($M)
                        {
                            Log::info( "Set Empty : " . $current->EbayItemUrl);
                            $this->PrintPer(0, false);
                            $this->setStatus($current->_id, 'Empty');
                        }


                    }
                    else if(isset($current->Status) && $current->Status == "Rejected")
                    {
                        Log::info( "Set Empty : " . $current->EbayItemUrl);
                        $this->PrintPer(0, false);
                        $this->setStatus($current->_id, 'Empty');

                    }
                    else {

                        if($current->Retry > 1)
                        {
                            Log::error("Set Retry Over : " .  $current->EbayItemUrl);
                            $this->PrintPer(0, false);
                            $this->setStatus($current->_id, 'Empty', $current);

                        }
                        else
                        {
                            Log::error("Set Rejected : " .  $current->EbayItemUrl);
                            $this->PrintPer(0, false);
                            $this->setStatus($current->_id, 'Rejected', $current);

                        }

                    }

                }

            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }
}
