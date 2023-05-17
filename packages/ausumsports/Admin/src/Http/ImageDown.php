<?php

namespace Ausumsports\Admin\Http;

use Ausumsports\Admin\Models\Mongo\EbayItems;
use Ausumsports\Admin\Models\Mongo\EbayProducts;
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


class ImageDown extends PrintPer
{

    /**
     * @var int
     */
    private $process;
    private $limit;
    private $Item;
    private $client;
    /**
     * @var EbayItems
     */
    private $ebayItems;
    /**
     * @var ScCheckLists
     */
    private $checklist;

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

        parent::__construct("Image Download");

        $this->client = new Client();
        $this->checklist = new ScCheckLists();
        $this->ebayItems = new EbayItems();
        $this->timeout = 10;

    }

    function useApi($link)
    {

        if ($this->api) {
            $link = "http://api.scraperapi.com?api_key=ff1b656f0f609dab4b89c4390be8ccf2&url=" . $link;
            $this->append("API");
        }

        return $link;
    }


    function getRequests($Items, $type = null)
    {
        $client = new GuzzleHttp\Client();

        $requests = function ($Items) use ($client, $type) {

            foreach ($Items as $Item) {
                // The magic happens here, with yield key => value
                yield $Item => function () use ($client, $Item, $type) {
                    // Our identifier does not have to be included in the request URI or headers
                    $link = (isset($Item->$type)) ? $Item->$type : null;
                    $resource = '/tmp/' . $type . '/' . $Item->_id;
                    $link = $this->useApi($link);
                    return $client->getAsync($link, ['sink' => $resource]);
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
            'fulfilled' => function ($response, $Item) use ($type) {
                try {

                    $resource = '/tmp/' . $type . '/' . $Item->_id;
                    $content = file_get_contents($resource);
                    $this->setImages($content, $Item, $type);
                    File::delete($resource);
                    //Log::debug($content);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());

                }


            },
            'rejected' => function ($response, $Item) use ($Items, $type) {

                Log::error("Url Error : " . $Item->$type);

                $this->checklist->where('_id', $Item->_id)->update(['status' => 'UrlError']);

                $this->append("Error");
                $this->PrintPer(0);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    function setImages($contents, $Item, $type)
    {
        try {
            $pathArray = explode('.', $Item->$type);

            if (is_array($pathArray)) $ext = end($pathArray);
            else $ext = "jpg";

            $Url = $this->saveImage($contents, $type . '/' . $Item->_id . '.' . $ext);

            if ($Url) {
                Log::info("saved Image :" . $Url);
                $key = $type . 'Link';
                $this->checklist->where('_id', $Item->_id)->update([$key => $Url, 'status' => 'Checked']);
                $this->append($key);
                $this->printPer(0);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->append("Error");
            $this->printPer(0);
        }
    }


    function getSetListLink($crawlers)
    {
        $crawler = $crawlers->filter(".tab-content div.col-sm-6 li");

        if (!$crawler->count()) {
            Log::error("Not found Set List");
            return;
        }


        $return = [];

        $crawler->each(function ($node) use (&$return) {

            $lastLink = $node->filter("a")->last();

            //         $Item->setTitle = trim($lastLink->attr('title'));
            $setLink = trim($lastLink->attr('href'));
            array_push($return, $setLink);
        });

        return $return;
    }


    function checkProductInformation()
    {
        $checklist = $this->checklist;
        $checklist = $checklist->where('type', 'Product');
        $checklist = $checklist->where('version', 0);
        $checklist = $checklist->orderBy('productTitle');

        $checklist = $checklist->options(['allowDiskUse' => true]);

        $Result = $checklist->get();
        $this->total = $Result->count();

        foreach ($Result as $Product) {
            Log::debug($Product->productTitle);
            $Arr = new \stdClass();

            $Arr->productTitle = trim($Product->productTitle);

            if (isset($Product->productImageLink)) {
                $Arr->productImageLink = $Product->productImageLink;
            }


            $Item = json_decode(json_encode($Product));

            $crawler = $this->client->request('GET', $Item->productLink);

            $List = $this->getSetListLink($crawler);

            if (is_array($List)) {

                foreach ($List as $SetLink) {
                    $this->checklist->where('type', 'Set')->where('setLink', $SetLink)->update((array)$Arr);
                    Log::info("SetLink :" . $SetLink);
                    $this->append("SetLink");
                }

            } else {
                Log::error("Set List Not Found : " . $Item->productLink);
                $this->checklist->where('_id', $Item->_id)->update(['version' => 0]);
            }

            $this->printPer(0);
        }

    }


    function checkProductSetInformation()
    {
        $checklist = $this->checklist;
        $checklist = $checklist->where('type', 'Set');
        $checklist = $checklist->whereNull('productTitle');
        $checklist = $checklist->where('status', '<>', 'SetChecked');
        $checklist = $checklist->options(['allowDiskUse' => true]);

        $Result = $checklist->get();
        $this->total = $Result->count();

        foreach ($Result as $Set) {
            $Arr = new \stdClass();

            // $Arr->productTitle = trim($Set->productTitle);

//            if(isset($Set->productImageLink)) $Arr->productImageLink =  $Set->productImageLink;
            if (isset($Set->setImageLink)) $Arr->setImageLink = $Set->setImageLink;

            $Arr->version = 1;

            $this->checklist->where('type', 'Card')->where('set', $Set->_id)->update((array)$Arr);
            $this->checklist->where('_id', $Set->_id)->update(['status' => 'SetChecked']);


            if (!($this->success % 100))
                $this->printPer(0);
            else
                $this->success++;
        }

    }


    function checkListCardImages($order = '', $api=true)
    {

        $this->maxRequest = 64;
        $this->api = $api;

        $this->checklist->whereNotNull('cardImagesLink')->update(['status' => 'Checked']);

        $checklist = $this->checklist;
        $type = "cardImages";

        $checklist = $checklist->where('type', 'Card')
            ->whereNotNull($type)
            ->where('status', '<>', 'Checked');

        if ($order) $checklist = $checklist->orderby('_id', $order);
        $checklist = $checklist->options(['allowDiskUse' => true]);

        $this->total = $checklist->count();

        Log::warning(" TOTAL : " . $this->total);

        $checklist->chunkbyID(10000, function ($Result) use ($type) {
            $Arr = [];

            foreach ($Result as $ItemList) {

                $Item = json_decode(json_encode($ItemList));

               // Log::debug($ItemList->_id);

                $Item->$type = explode(',', $Item->$type)[0];
                if (preg_match("/^http/", $Item->$type))
                {
                    array_push($Arr, $Item);

                    if (!(sizeof($Arr)%1000)) {

                        $this->getRequests($Arr, $type);
                        $Arr = [];
                    }

                } else {

                    $this->checklist->where('_id', $Item->_id)->update(['status' => 'URL Check']);
                    $this->append("SetImagePathError");
                    $this->printPer();
                }
            }

            $this->getRequests($Arr, $type);

        });

    }


    function checkListSetImages()
    {

        $this->maxRequest = 24;
        $this->api = true;


        $checklist = $this->checklist;
        $type = "setImage";

        $checklist = $checklist->where('type', 'Set')
            ->whereNotNull("$type")
            ->whereNull("setImageLink")
            ->orderby('_id');

        $this->total = $checklist->count();

        Log::warning(" TOTAL : " . $this->total);


        return;

        $checklist->chunkbyId(1000, function ($Result) use ($type) {
            $Arr = [];
            foreach ($Result as $Item) {

                $Item = json_decode(json_encode($Item));

                if (preg_match("/^http/", $Item->$type)) {

                    array_push($Arr, $Item);
                    if (!sizeof($Arr) % 100) {
                        $this->getRequests($Arr, $type);
                        $Arr = [];
                    }
                } else {

                    $this->append("SetImagePathError");
                    $this->printPer(0);
                }

            }

            $this->getRequests($Arr, $type);
        });

    }


    function checkListProductImages()
    {
        $checklist = new ScCheckLists();

        $result = $checklist->where('type', 'Product')->whereNotNull("productImage")
            ->whereNull("productImageLink")
            ->orderby('_id')->get();

        $this->total = $result->count();

        Log::warning(" TOTAL : " . $this->total);

        foreach ($result as $Item) {
            $this->Item = json_decode(json_encode($Item));

            if (preg_match("/^http/", $this->Item->productImage)) {
                Log::info("Request : " . $this->Item->productImage);


                try {
                    $contents = file_get_contents($this->Item->productImage);
                    $pathArray = explode('.', $this->Item->productImage);

                    if (is_array($pathArray)) $ext = end($pathArray);
                    else $ext = "jpg";

                    $Url = $this->saveImage($contents, 'productImage/' . $this->Item->_id . '.' . $ext);

                    if ($Url) {
                        $checklist->where('_id', $this->Item->_id)->update(['productImageLink' => $Url]);
                        $this->append("ProductImageSave");
                        $this->printPer();
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    $this->append("Error");
                    $this->printPer(0);
                }

            } else {

                $this->append("ProductImagePathError");
                $this->printPer();
            }

        }
    }

    function saveImage($contents, $fileName)
    {
        if (Storage::disk('s3')->put($fileName, $contents, 'public'))
            return Storage::disk('s3')->url($fileName);
    }

    function updateCheckList($id, $update, $unset)
    {
        $checklist = new ScCheckLists();
        $checklist = $checklist->where("_id", $id);

        if (count($update)) $checklist->update($update);

        foreach ($unset as $Value) {
            $checklist->unset($Value);
        }
    }
}
