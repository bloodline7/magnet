<?php

namespace Ausumsports\Admin\Crawler;

use Ausumsports\Admin\Http\PrintPer;
use Ausumsports\Admin\Models\Mongo\Brands;
use Ausumsports\Admin\Models\Mongo\Product;
use Ausumsports\Admin\Models\Mongo\ProductList;
use Ausumsports\Admin\Models\Mongo\Products;
use Ausumsports\Admin\Models\Mongo\ScCheckLists;
use Illuminate\Support\Facades\Log;
use Goutte\Client;
use Psy\Util\Json;
use stringEncode\Exception;
use Symfony\Component\HttpClient\HttpClient;
use Ausumsports\Admin\Events\Crawling as Event;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;


use function PHPUnit\Framework\throwException;


class SportsCardChecklist extends PrintPer
{

    private $baseUrl;
    private $channel;
    private $result;
    private $maxRequest;
    private $max;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var int
     */
    private $version;
    /**
     * @var Client
     */
    private $client;
    private $mongo;
    private $update;
    /**
     * @var int
     */
    private $delay;
    /**
     * @var bool
     */
    private $synchronous;
    /**
     * @var int
     */
    private $timeout;
    private $api;

    public function __construct($version = 1)
    {
        ini_set('memory_limit', '-1');

        parent::__construct("CheckList Crawling");

        $this->channel = "SportsCardCheckList";
        $this->baseUrl = "https://www.sportscardchecklist.com/";
        $this->result = [];

        $this->message = [];
        $this->total = $this->success = 0;
        $this->client = new Client();
        $this->mongo = new ScCheckLists();

        $this->api = false;


        Log::info("Channel :: " . $this->channel);


        $this->maxRequest = 6;
        $this->timeout = 10;
        $this->max = 100;
        $this->delay = 0;

        $this->synchronous = false;


        /*
                $this->maxRequest = 2;
                $this->getCards();

        */


        //$this->getMenu();
        //$this->getProduct();
        //        $this->getProduct(true);
        //  $this->getProduct('NO_SET');
        //$this->checkProduct();

    }

    function checkList($version = 1)
    {
        $this->version = $version;
        /*
                $this->getMenu();
                $this->getYear();
                $this->getProduct();

                $this->getSet();
                $this->checkSet();*/

//        $this->limit = 50;

        $this->getCards();

    }

    function SetStatus($id, $Status)
    {
        $mongo = $this->mongo;

        if (is_array($Status))
            $mongo->where("_id", $id)->update($Status);
        else
            $mongo->where("_id", $id)->update(["status" => $Status]);

    }

    function rename($name)
    {
        $name = strtoupper(trim($name));
        switch ($name) {
            case 'RC' :
                return 'rookieCard';
            case 'AUTO' :
                return 'auto';
            case 'MEM' :
                return 'memorial';
            case 'SP' :
                return 'special';
            default :
                Log::warning("Ignored name : " . $name);

                $this->append($name);

                return false;
        }
    }


    function TeamNameUpdate ()
    {

        //$mongo = $this->mongo->where('type',  'Card')->where('Status', '<>' , 'Checked')->Where('Status', '<>' , 'TeamUpdate')->WhereNotNull('team');

        $mongo = $this->mongo
            ->where('team', 'like', '%AUTO%')
            ->orwhere('team', 'like', '%MEM%')
            ->orwhere('team', 'like', '%SP%')->orwhere('team', 'like', '%RC%');



        $this->total = $mongo->count();

        $this->printPer(2, false);

        $mongo->chunkbyId(10000, function ($Result) {

            foreach ($Result as $Item) {

                if(!isset($Item->team))
                {
                    if(!($this->success % 100))
                        $this->printPer();
                    else
                        $this->success++;

                    continue;
                }


                //$Item = json_decode(json_encode($Item));


                $team = $Item->team;


                if (preg_match("/([Serial\/]+[0-9]+)+/", $team, $match)) {
                    $match = $match[1];
                    $team = str_replace($match, '', $team);
                }

                $team = str_replace('AUTO', '', $team);
                $team = str_replace('RC', '', $team);
                $team = str_replace('MEM', '', $team);
                $team = str_replace('SP', '', $team);

                $team = trim($team);

                if($team !== $Item->team)
                {
                    $Status = 'TeamUpdate';
                }
                else {
                    $Status = "Checked";
                }

                if($team)
                {
                    $this->mongo->where('_id', $Item->_id)->update(['team' => $team , 'Status' => $Status]);
                    $this->append($Status);
                }
                else
                {
                    $this->mongo->where('_id', $Item->_id)->unset('team');
                    $this->append('teamUnset');
                }

                if(!($this->success % 100))
                $this->printPer();
                else
                    $this->success++;
            }
        });
    }

    function CardParsing($crawler, $Item)
    {
        if ((isset($Item->status) && $Item->status == 'parsing')) {
            $this->cardDelete($Item);
        }


        $Id = $Item->_id;

        $this->SetStatus($Id, 'parsing');

        $menu = $crawler->filter("ol.breadcrumb li");


        if ($cnt = $menu->count()) {
            $SetName = $menu->eq($cnt - 2)->filter("a")->first()->text();

            if (!trim($Item->setTitle) && $SetName) {

                $update = [];
                $update['setTitle'] = $SetName;
                $this->SetStatus($Item->_id, $update);
                $Item->setTitle = $SetName;
                $this->append("SetName");
            }


            $CardSet = $menu->eq($cnt - 1)->filter("a")->first()->text();

            if (preg_match("/[Cards]$/", $CardSet)) {
                $CardSet = str_replace("Cards", '', $CardSet);
                $Item->card = trim($CardSet);
            } else {
                Log::warning(json_encode($Item));
                Log::error("No Card Name -- " . $CardSet);
                return;
            }
        } else {
            Log::warning(json_encode($Item));
            Log::error("No Header : " . $Item->setLink);
            return;
        }


        unset($Item->createdAt);
        unset($Item->updatedAt);
        unset($Item->status);


        $CardList = $crawler->filter("div.row div.panel");

        if ($CardList->count()) {


            $OrgItem = $Item;
            $OrgItem->set = $Id;


            $CardList->each(function ($node) use ($OrgItem) {

                $Item = $OrgItem;

                $header = $node->filter("div.form-header h5")->first()->text();

                preg_match("/[^\r]+(\#[\S]+|\#[\s]{1})+([^\r]+)+$/", $header, $match);

                if (sizeof($match) != 3) {
                    Log::error("Header Pasing Error:" . $header);
                    //     Log::warning("Page Link : " . $Item->setLink);
                    $this->printPer(0, false);
                    return;
                }

                $Item->cardNo = trim($match[1]);
                $Item->player = trim($match[2]);

                $TeamArea = $node->filter("div.form-header div.border-muted");

                if ($TeamArea->count()) {

                    $TeamName = $TeamArea->text();
                    $Badge = $TeamArea->filter('small div.badge');

                    if ($Badge->count()) {

                        $BgName = $Badge->text();
                        $TeamName = trim(str_replace($BgName, '', $TeamName));

                        $Badge->each(function ($node) use ($Item) {

                            $BadgeName = $node->text();

                            if (preg_match("/^[Serial\/]+([0-9]+)+$/", $BadgeName, $match)) {
                                if ($match[1] > 0) {
                                    $Item->printRun = (int)$match[1];
                                    $this->append('PrintRun');
                                }
                            } else {
                                $BadgeName = $this->rename($node->text());

                                if ($BadgeName) {
                                    $this->append($BadgeName);
                                    $Item->$BadgeName = true;
                                }
                            }
                        });

                    }

                    $TeamName = trim($TeamName);

                    if ($TeamName) {
                        $Item->team = $TeamName;
                        $this->append('Team');
                    }
                }

                $body = $node->filter("div.gallery-wrapper a.popup-image");

                if ($body->count()) {
                    $cardImages = '';

                    $body->each(function ($node) use (&$cardImages) {

                        if ($cardImages)
                            $cardImages .= ',';

                        $cardImages .= $node->attr('href');
                    });

                    if ($cardImages) {
                        $Item->cardImages = $cardImages;
                        $this->append('CardImages');
                    }
                }

                $Item->type = 'Card';
                unset($Item->_id);
                unset($Item->setLink);
              //  unset($Item->cardImages);

                //  Log::debug(json_encode($Item));

                $this->mongo->create((array)$Item);

                $this->append('Cards');
            });


            $this->SetStatus($Id, "Complete");
            $this->setVersion($Id);

            $this->append("Complete");
            $this->printPer();

        } else {
            Log::warning(json_encode($Item));
            Log::error("No CardList : " . $Item->setLink);

            $this->SetStatus($Id, "NoCards");

            $this->append("NoCards");
            $this->printPer(0);

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

        $requests = [];
        foreach ($Items as $Item) {
            $link = (isset($Item->productLink)) ? $Item->productLink : $Item->setLink;

            $link = $this->useApi($link);

            array_push($requests, new Request('GET', $link));
        }

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
            ],
            'decode_content' => true,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
            'delay' => $this->delay,
            'expect' => $this->synchronous,
            'synchronous' => $this->synchronous
        ];


        $client = new GuzzleHttp\Client();
        $pool = new GuzzleHttp\Pool($client, $requests, [
            'concurrency' => $this->maxRequest, //how many concurrent requests we want active at any given time
            'options' => $defaults,
            'fulfilled' => function ($response, $index) use ($Items) {

                $Item = $Items[$index];
                $link = (isset($Item->productLink)) ? $Item->productLink : $Item->setLink;

                $crawler = new Crawler(null, $link);
                $crawler->addContent(
                    $response->getBody()->__toString()
                );


                if (isset($Item->productLink))
                    $this->parsingSetList($crawler, $Item);
                else
                    $this->CardParsing($crawler, $Item);

            },
            'rejected' => function ($response, $index) use ($Items) {

                $Item = $Items[$index];
                $id = $Item->_id;
                $link = (isset($Item->productLink)) ? $Item->productLink : $Item->setLink;

                if (isset($Item->status) && $Item->status == "Rejected") {
                    $this->SetStatus($id, 'Long');
                    $this->append("Long");

                    Log::emergency("URL Rejected and Set Long : " . $link);
                } else {
                    $this->SetStatus($id, 'Rejected');
                    $this->append("Rejected");

                    Log::emergency("URL Rejected : " . $link);
                }


                $this->PrintPer(0);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    function cardDelete($Item)
    {
        $this->mongo->where('type', 'Card')->where('set', $Item->_id)->delete();
    }

    function getRejectedCard($version = 1)
    {

        $this->timeout = 120;
        $this->maxRequest = 8;
        // $this->api = true;
        $this->version = $version;

        $mongo = $this->mongo;
        $mongo = $mongo->where('type', 'Set');
        $mongo = $mongo->where('status', 'Rejected');
        $mongo = $mongo->where('version', '<', $this->version);
        $mongo = $mongo->orderby("_id");

        if ($this->limit)
            $mongo = $mongo->take($this->limit);

        $result = $mongo->options(['allowDiskUse' => true])->get();
        $this->total = $result->count();

        Log::emergency(" TOTAL :" . $this->total);


        if($this->total)
        $this->printPer(0, false);

        $requests = [];

        foreach ($result as $Item) {
            $Item = json_decode(json_encode($Item));

            array_push($requests, $Item);
            if (sizeof($requests) >= $this->max) {
                $this->getRequests($requests);
                $requests = [];
            }
        }
        if (sizeof($requests))
            $this->getRequests($requests);

    }

    function getRejectedLong($version = 1)
    {

        $this->timeout = 0;
        $this->maxRequest = 1;
        $this->synchronous = true;
        $this->version = $version;

        $mongo = $this->mongo;
        $mongo = $mongo->where('type', 'Set');
        $mongo = $mongo->where('status', 'Long');
        $mongo = $mongo->where('version', '<', $this->version);
        $mongo = $mongo->orderby("_id");

        $Arr = [];

        $mongo->chunkById(10, function ($Result) use (&$Arr) {

            $this->total = $Result->count();

            foreach ($Result as $Item) {
                $Item = json_decode(json_encode($Item));

                array_push($Arr, $Item);
                if ((sizeof($Arr) % 10) == 0) {
                    $this->getRequests($Arr);
                    $Arr = [];
                }
            }
        });

        if (sizeof($Arr))
            $this->getRequests($Arr);

    }

    function getStatus($id)
    {
        $result = $this->mongo->where('_id', $id)->first();

        if(isset($result->status))
            return $result->status;

    }

    function getCards($order = "asc", $limit = 10000)
    {
        $this->timeout = 60;
        $this->maxRequest = 8;
        $this->limit = $limit;
        $this->max = 5000;
        $this->version = 1;

        //$this->api = true;

        $mongo = $this->mongo->where('type', 'Set');
        $mongo = $mongo->where('status', '<>', 'NoCards');
        $mongo = $mongo->where('status', '<>', 'Rejected');
        $mongo = $mongo->where('version', '<', $this->version);
        $mongo = $mongo->orderby("_id", $order);

        if ($this->limit)
            $mongo = $mongo->take($this->limit);

        $result = $mongo->options(['allowDiskUse' => true])->get();

        $this->total = $result->count();


        $requests = [];

        foreach ($result as $Item) {
            $Item = json_decode(json_encode($Item));


            array_push($requests, $Item);
            if (sizeof($requests) >= $this->max) {
                $this->getRequests($requests);
                $requests = [];
            }
        }


        if (sizeof($requests))
            $this->getRequests($requests);


        if ($this->total == $limit) {
            Log::info("Repeat Get Card.");
            $this->append("Repeat");
            $this->printPer(0, false);
            $this->getCards($order, $limit);
        }

    }

    function append($Key, $Message = '')
    {
        if ($Message)
            $this->message[$Key] = $Message;
        else
            $this->message[$Key] = (isset($this->message[$Key])) ? $this->message[$Key] + 1 : 1;
    }


    public function getMenu()
    {

        if (!$this->update) return false;


        $crawler = $this->client->request('GET', $this->baseUrl, ['stream' => true]);

        $crawler->filter(".left-sidebar .panel-body li")->each(function ($node) {

            $link = $node->filter("a")->attr('href');
            $Sports = $node->filter("a")->text();

            $Obj = new \stdClass();

            $Obj->sport = trim($Sports);
            $Obj->sportLink = trim($link);
            $Obj->type = "Sports";

            try {
                $this->mongo->create((array)$Obj);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            Log::emergency($link);
        });
    }

    function setVersion($id)
    {
        $this->mongo->where("_id", $id)->update(['version' => $this->version]);
    }

    function getYear()
    {

        Log::info("Start Year Parsing.");

        $result = $this->mongo->where('type', 'Sports')
            ->Where('version', '<', $this->version)->orderby("_id")->get();

        $this->total = $result->count();

        foreach ($result as $Item) {

            $id = $Item->_id;
            unset($Item->_id);
            unset($Item->createdAt);
            unset($Item->updatedAt);

            $Item->type = "Year";
            $Item = json_decode(json_encode($Item));

            $crawler = $this->client->request('GET', $Item->sportLink);

            unset($Item->sportLink);

            $crawler->filter(".tab-content .col-sm-3 li")->each(function ($node) use ($Item) {
                $link = $node->filter("a")->attr('href');
                $Year = $node->filter("a")->text();
                $Item->year = trim($Year);
                $Item->yearLink = trim($link);

                try {
                    $this->mongo->create((array)$Item);
                    $this->append("Year");
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    $this->append("YearRep");
                }

                $this->printPer();
            });

            $this->setVersion($id);
            $this->printPer();
        }
    }

    function checkProduct()
    {
        $mongo = new ScCheckLists();

        $result = $mongo->where('Type', 'Product')->get();

        Log::warning("TOTAL :" . $result->count());

        foreach ($result as $Item) {
            $resultSet = $mongo->where('Type', 'Set')->where('ProductLink', $Item->ProductLink)->get();

            if (!$resultSet->count()) {
                Log::error("ItemSet Not Found");

                $mongo->where('_id', $Item->_id)->update(['Status' => 'NO_SET']);
            }
        }
        Log::warning("TOTAL :" . $result->count());
    }


    function checkSet()
    {
        $result = $this->mongo->where('type', 'Set')
            ->whereNotNull('productImage')
            ->get();

        $this->total = $result->count();

        foreach ($result as $Item) {
            $Item = json_decode(json_encode($Item));
            $this->productToSet($Item);

            $this->printPer(0);
        }

    }

    function getSet()
    {
        Log::info("Start getting Set .");

        $result = $this->mongo->where('type', 'Product')
            ->where('version', '<', $this->version)
            ->orderby("_id")
            ->get();

        $this->total = $result->count();

        if (!$this->total) {
            Log::info("No More Product  to Set Parsing.");
            return;
        }

        $this->printPer(0, false);

        $requests = [];

        foreach ($result as $Item) {
            $Item = json_decode(json_encode($Item));
            array_push($requests, $Item);
            if (sizeof($requests) >= $this->max) {
                $this->getRequests($requests);
                $requests = [];
            }
        }

        if (sizeof($requests))
            $this->getRequests($requests);

    }

    function productToSet($Item)
    {

        $id = $Item->_id;

        $Item->type = 'Set';

        if (isset($Item->productLink))
            $Item->setLink = $Item->productLink;

        if (isset($Item->productTitle))
            $Item->setTitle = $Item->productTitle;

        if (isset($Item->productImage))
            $Item->setImage = $Item->productImage;


        unset($Item->productLink);
        unset($Item->productTitle);
        unset($Item->productImage);
        unset($Item->_id);

        $this->mongo->where('_id', $id)->update((array)$Item);
        $this->mongo->where('_id', $id)->unset('productLink')->unset('productTitle')->unset('productImage');

        $this->append("ProductToSet");

    }

    function parsingSetList($crawlers, $Item)
    {
        $crawler = $crawlers->filter(".tab-content div.col-sm-6 li");


        if (!$crawler->count()) {

            $crawler = $crawlers->filter(".tab-content div.col-sm-4 li");
            if (!$crawler->count()) {
                Log::error("Set Not Found :  " . $Item->productLink);

                $this->SetStatus($Item->_id, "NotSet");


                $menu = $crawlers->filter("ol.breadcrumb li");

                if ($menu->count()) {
                    $this->productToSet($Item);
                    Log::info("product to set :" . json_encode($Item));
                }

                $this->append("notSet");
                $this->printPer(0);
                return;
            }
        }

        $id = $Item->_id;

       // unset($Item->productTitle);
        unset($Item->productLink);
        //unset($Item->productImage);

        $crawler->each(function ($node) use ($Item) {

            unset($Item->setTitle);
            unset($Item->setLink);
            unset($Item->setImage);
            unset($Item->productTitle);
          //  unset($Item->productLink);
           // unset($Item->productImage);

            $Img = ($node->filter("img")->count()) ? $node->filter("img")->first()->attr('src') : null;
            $lastLink = $node->filter("a")->last();

            $allText = trim($node->text());
            $title = trim($lastLink->text());

            // Log::info('AllText : ' . $allText . ' === ' . $title);


            if ($allText !== $title) {
                $Item->productTitle = $title;
                $Item->type = 'Product';
                $Item->productLink = $lastLink->attr('href');

                if ($Img) $Item->productImage = $Img;

                Log::debug(json_encode($Item));

                $this->append('Products');
                $this->printPer(0);
                return false;

            } else {
                $Item->setTitle = trim($lastLink->attr('title'));
                $Item->type = 'Set';
                $Item->setLink = trim($lastLink->attr('href'));
                if ($Img) $Item->setImage = $Img;
                $this->append('Set');
            }

            try {

//                Log::debug(json_encode($Item));
                $this->mongo->create((array)$Item);
//                $this->printPer(0, false);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                $this->printPer(0, false);
            }


        });

        $this->setVersion($id);
        $this->printPer();

    }


    function getProduct()
    {

        Log::info("Start Product or Set Parsing.");

        $result = $this->mongo->where('type', 'Year')
            ->Where('version', '<', $this->version)
            ->orderby("_id")->get();


        $this->total = $result->count();

        if (!$this->total) {
            return false;
        }

        $this->printPer(0, false);

        foreach ($result as $Item) {

            $Item = json_decode(json_encode($Item));

            $crawler = $this->client->request('GET', $Item->yearLink);
            $crawler = $crawler->filter(".tab-content div.col-sm-4 li");


            $id = $Item->_id;
            unset($Item->_id);
            unset($Item->yearLink);
            unset($Item->createdAt);
            unset($Item->updatedAt);

            $crawler->each(function ($node) use ($Item) {

                unset($Item->productTitle);
                unset($Item->productLink);
                unset($Item->setTitle);
                unset($Item->setLink);
                unset($Item->setImage);
                unset($Item->productImage);

                $Img = ($node->filter("img")->count()) ? $node->filter("img")->first()->attr('src') : null;
                $lastLink = $node->filter("a")->last();

                $allText = trim($node->text());
                $title = trim($lastLink->text());

                // Log::info('AllText : ' . $allText . ' === ' . $title);

                if ($allText !== $title) {
                    $Item->productTitle = $title;
                    $Item->type = 'Product';
                    $Item->productLink = $lastLink->attr('href');

                    if ($Img) $Item->productImage = $Img;

                    Log::debug(json_encode($Item));

                    $this->append('Products');
                } else {
                    $Item->setTitle = $lastLink->attr('title');
                    $Item->type = 'Set';
                    $Item->setLink = $lastLink->attr('href');

                    if ($Img) $Item->setImage = $Img;

                    $this->append('Set');
                }

                try {
                    $this->mongo->create((array)$Item);
                    $this->printPer(2, false);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    $this->printPer(0, false);
                }


            });

            $this->setVersion($id);
            $this->printPer();
        }
    }
}
