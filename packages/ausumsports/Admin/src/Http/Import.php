<?php

namespace Ausumsports\Admin\Http;

use Ausumsports\Admin\Models\Mongo\EbayItems;
use Ausumsports\Admin\Models\Mongo\EbayProducts;
use Ausumsports\Admin\Models\Mongo\ScCheckLists;

use Ausumsports\Admin\Http\PrintPer;
use DateTimeImmutable;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MongoDate;
use stringEncode\Exception;

class Import extends PrintPer
{

    /**
     * @var int
     */
    private $process;
    private $limit;
    private $Item;
    /**
     * @var string[]
     */
    private $emptyWord;
    /**
     * @var array|\stdClass|string[]|null
     */
    private $Obj;
    /**
     * @var EbayItems
     */
    private $ebayItems;
    /**
     * @var EbayProducts
     */
    private $ebayProducts;
    private $itemNo;
    /**
     * @var mixed
     */
    private $ebayProduct;
    /**
     * @var EbayItems
     */
    private $ebayItem;
    /**
     * @var array
     */
    private $sportsArray;
    /**
     * @var Client
     */
    private $client;


    function __construct()
    {
        ini_set('memory_limit', '-1');

        parent::__construct("Importing");

        $this->client = new Client();

        $this->ebayItems = new EbayItems();
        $this->ebayProducts = new EbayProducts();


        $this->emptyWord = ['does not apply', 'n/a', 'na', 'no', 'not applicable', 'ungraded', 'unsigned', 'seller assumes all responsibility for this listing.'];

    }


    function checkListImages()
    {
        $this->checkListSetImages();

    }

    function checkListSetImages()
    {
        $checklist = new ScCheckLists();


        $result = $checklist->where('type', 'Set')->whereNotNull("setImage")
            ->whereNull("setImageLink")
            ->orderby('_id')->get();

        $this->total = $result->count();


        Log::warning(" TOTAL : " . $this->total);

        foreach ($result as $Item)
        {
            $this->Item = json_decode(json_encode($Item));

            if(preg_match("/^http/", $this->Item->setImage))
            {
                Log::info( "Request : " . $this->Item->setImage);



                try {
                    $contents = file_get_contents($this->Item->setImage);
                    $pathArray = explode('.', $this->Item->setImage);

                    if(is_array($pathArray)) $ext = end($pathArray);
                    else $ext = "jpg";

                    $Url = $this->saveImage($contents, 'setImage/'. $this->Item->_id . '.'. $ext );

                    if($Url)
                    {
                        $checklist->where('_id', $this->Item->_id)->update(['setImageLink' => $Url]);
                        $this->append("SetImageSave");
                        $this->printPer();
                    }
                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                    $this->append("Error");
                    $this->printPer(0);
                }

            }
            else
            {

                $this->append("SetImagePathError");
                $this->printPer();
            }


        }


    }


    function checkListProductImages()
    {
        $checklist = new ScCheckLists();

        $result = $checklist->where('type', 'Product')->whereNotNull("productImage")
            ->whereNull("productImageLink")
            ->orderby('_id')->get();

        $this->total = $result->count();

        Log::warning(" TOTAL : " . $this->total);

        foreach ($result as $Item)
        {
            $this->Item = json_decode(json_encode($Item));

            if(preg_match("/^http/", $this->Item->productImage))
            {
                Log::info( "Request : " . $this->Item->productImage);



                try {
                    $contents = file_get_contents($this->Item->productImage);
                    $pathArray = explode('.', $this->Item->productImage);

                    if(is_array($pathArray)) $ext = end($pathArray);
                    else $ext = "jpg";

                    $Url = $this->saveImage($contents, 'productImage/'. $this->Item->_id . '.'. $ext );

                    if($Url)
                    {
                        $checklist->where('_id', $this->Item->_id)->update(['productImageLink' => $Url]);
                        $this->append("ProductImageSave");
                        $this->printPer();
                    }
                }
                catch (\Exception $e)
                {
                    Log::error($e->getMessage());
                    $this->append("Error");
                    $this->printPer(0);
                }

            }
            else
            {

                $this->append("ProductImagePathError");
                $this->printPer();
            }






        }



    }


    function saveImage($contents, $fileName)
    {
        if(Storage::disk('s3')->put($fileName, $contents, 'public' ))
        return  Storage::disk('s3')->url($fileName);
    }


function getSports(): array
    {
        $checklist = new ScCheckLists();
        $result = $checklist->where('type', 'Sports')->orderby('_id')->get();

        $return = [];

        foreach ($result as $Item) {
            array_push($return, $Item->sport);
        }
        return $return;
    }

    function getProGrader(): array
    {

        return [
            "PSA",
            "BGS",
            "FGS",
            "FCG",
            "SGC",
            "CSG",
            "CGS",
            "KSA",
            "BCCG",
            "HGA",
            "GMA",
            "BVG",
            "VGS"

        ];
    }

    function isRookieCard()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;

        if (!isset($this->Item->itemTitle)) {

            $title = $this->getEbayProducts()->ItemTitle;

            if ($title) {

                $this->Item->itemTitle = $title;
                $this->Obj->itemTitle = $title;
                $this->append('itemTitle');
            } else {
                Log::error(json_encode($this->Item));
                return false;
            }
        }

        if (preg_match("/\/s+RC|rookie/i", $this->Item->itemTitle)) {

            //Log::info("ROOKIE FOUND :" . $this->Item->itemTitle);
            $this->append("Rookie");
            return true;
        } else {
            //Log::debug("ROOKIE Not FOUND :" . $this->Item->itemTitle);
        }

    }

    function isAutoCard()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;


        if (preg_match("/\/s+Auto|Sign/i", $this->Item->itemTitle)) {

            $this->append("Auto");
            return true;
        }

    }


    function getEbayProducts()
    {
        if ($this->Item->ebayItemNo === $this->itemNo) return $this->ebayProduct;
        else {
            $result = $this->ebayProducts->where("EbayItemNumber", $this->Item->ebayItemNo)->first();
            $this->itemNo = $this->Item->ebayItemNo;
            $this->ebayProduct = json_decode(json_encode($result));
            return $this->ebayProduct;
        }
    }

    function getGrade()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;


        if (isset($this->Item->grade)) {
            $grade = (float)$this->Item->grade;

            if (is_float($grade) && ($grade <= 10))
                return $grade;

            Log::error($this->Item->grade);
        }

        if (isset($this->Obj->grader)) {

            if (preg_match("/" . $this->Obj->grader . "+[^0-9]*([0-9\.])+/i", $this->Item->itemTitle, $match)) {
                $grade = (float)$match[1];
                if (is_float($grade) && ($grade <= 10)) return $grade;
            }
        }
        $this->append("NoGrade");
    }



    function setCardAttribute()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;


        // jersey , patch , sticker

        $words = [
            "jersey",
            "patch",
            "sticker",
            "insert"
        ];

        if (preg_match("/\/s+jersey|patch|sticker|insert/i", $this->Item->itemTitle)) {


            Log::emergency("ATTRIBUTE FOUND : " . $this->Item->itemTitle);

            foreach ($words as $word) {

                if (stripos($this->Item->itemTitle, $word) !== false) {

                    $this->Obj->$word = true;
                    $this->append($word);
                }
            }
        }

    }


    function getGrader()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;

        $grader = $this->getProGrader();
        $ebayProduct = $this->getEbayProducts();

        foreach ($grader as $value) {

            if (isset($ebayProduct->ProfessionalGrader)) {
                if (str_contains($ebayProduct->ProfessionalGrader, $value)) {
                    //  Log::warning("FIND GRADER BY ebayProduct : " . $ebayProduct->ProfessionalGrader);
                    $this->append($value);
                    return $value;
                }
            }

            if (str_contains($this->Item->itemTitle, ' ' . $value)) {
                $this->append($value);
                return $value;
            }
        }

    }


    function getCardNo()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;

        if (preg_match("/\#+([0-9])+/", $this->Item->itemTitle, $match)) {
            $this->append("CardNo");
            return (int)$match[1];
        }
    }

    function getPrintRun()
    {
        if (!isset($this->Item->type) || $this->Item->type !== 'Card') return;

        if (preg_match("/\/+([0-9])+/", $this->Item->itemTitle, $match)) {
            $this->append("PrintRun");
            return (int)$match[1];
        }
    }


    function getEndedAt()
    {

        //if(isset($this->Item->endedAt)) return;

        $EndDate = date_parse_from_format("d.m.Y H:i:s", $this->Item->ended);

        if ($EndDate['year'] && $EndDate['day'])
        {
            $Date = $EndDate['year'] . '-' . $EndDate['month'] . '-' . $EndDate['day'] .
                ' ' . $EndDate['hour'] . ":" . $EndDate['minute'] . ":" . $EndDate['second'];

            return  new \MongoDB\BSON\UTCDateTime(strtotime($Date)*1000);
        }

    }

    function getSport()
    {
        if(!$this->sportsArray) $this->sportsArray = $this->getSports();


        foreach ($this->sportsArray as $sports)
        {

            if(isset($this->Item->sport))
            {
                if(str_contains($this->Item->sport , ','))
                    return "MultiSport";

                if(stripos($this->Item->sport , $sports) !== false)
                    return $sports;

            }

            if(isset($this->Item->itemTitle) && stripos($this->Item->itemTitle , $sports) !== false)
                return $sports;

        }

        $Match =
            [
                "UFC" => "MMA",
                "MLB" => "Baseball",
                "PBA" => "Basketball",
                "NBA" => "Basketball",
            ];

        foreach ($Match as $Key => $Val)
        {
            if(isset($this->Item->itemTitle) && stripos($this->Item->itemTitle , $Key) !== false) return $Val;

            if((isset($this->Item->league)) && (stripos($this->Item->league , $Key) !== false)) return $Val;
        }


        if(isset($this->Item->itemTitle))
        Log::error("Item Sports Not Found : " . $this->Item->itemTitle );

        $this->append("NonSport");
        $this->printPer(0, false);

        return "NonSport";
    }

    function getSoldPrice()
    {
        if(isset($this->Item->soldPrice)) return;

        $ebayProduct = $this->getEbayProducts();

        if(isset($ebayProduct->SoldPrice))
        {

            Log::debug("SOLD :" . $ebayProduct->SoldPrice);
            $this->printPer(0, false);

            return (float) $ebayProduct->SoldPrice;
        }

        Log::error("Not Sold Price :" . json_encode($this->Item));

        $this->append("SOLD_PRICE_ERR");
        $this->printPer(0, false);

    }


    function getShippingPrice()
    {
        if(isset($this->Item->shipping)) return;
        $ebayProduct = $this->getEbayProducts();

        if(isset($ebayProduct->Shipping))
        {
            return $this->getData($ebayProduct->Shipping, 'float');
        }

    }



    function ebayItemsCheck()
    {
        $this->ebayItem = new EbayItems();

        $ebayItem =  $this->ebayItem;

    //    $ebayItem = $ebayItem->where('type', 'Card');

 //       $start = Carbon::createFromDate(2023, 3, 19);
  //      $ebayItem = $ebayItem->whereNull('endeddAt', '>', $start);


   //     $ebayItem = $ebayItem->whereNull('endedAt');

         $ebayItem = $ebayItem->where('Status', '<>', 'printRun');

        $ebayItem = $ebayItem->orderby('_id', 'asc');
        //$ebayItem = $ebayItem->skip(141500);

        $this->total = $ebayItem->count();

        $ebayItem->chunkbyId(1000, function ($Result) {

            foreach ($Result as $Item) {

                $this->Item = json_decode(json_encode($Item));
                $this->Obj = new \stdClass();

                $this->Obj->rookieCard = $this->isRookieCard();
                $this->Obj->auto = $this->isAutoCard();
                $this->Obj->grader = $this->getGrader();
                $this->Obj->grade = $this->getGrade();

                $this->Obj->endedAt = $this->getEndedAt();

                $this->Obj->sport = $this->getSport();
                $this->Obj->cardNo = $this->getCardNo();
                $this->Obj->printRun = $this->getPrintRun();

                $this->Obj->soldPrice = $this->getSoldPrice();

                $this->Obj->shipping = $this->getShippingPrice();

                $this->setCardAttribute();


                foreach ($this->Obj as $Key => $Value) {
                    if (!$Value) unset($this->Obj->$Key);
                }


                $this->eBayItemsUpdate();
                $this->success++;

                if(!($this->success % 100))
                $this->printPer();

            }

        });


    }



    function eBayItemsUpdate()
    {

        $ebayItems = $this->ebayItems;

        $this->Obj->Status = 'printRun';

        $update =  (array)$this->Obj;

        if (sizeof($update)) {

            $ebayProduct = $ebayItems->where("_id", $this->Item->_id);
            $ebayProduct->update($update);
        }


        /*
        if (sizeof($drop)) {
            $ebayProduct = new EbayProducts();
            $ebayProduct = $ebayProduct->where("_id", $id);

            foreach ($drop as $Field) {
                $ebayProduct->unset($Field);
                $this->append($Field);
            }
        }*/


    }



    function checkListCleanUp()
    {
        $checklist = new ScCheckLists();
        $this->total = $checklist->count();

        $process = 0;
        $this->limit = $this->total;

        $checklist->chunkById(1000, function ($Result) use (&$process) {

            foreach ($Result as $Item) {

                $Item = json_decode(json_encode($Item));

                foreach ($Item as $Key => $Value) {

                    if (str_contains($Key, '/')) {
                        $drop = $update = [];
                        if (preg_match("/^Serial/", $Key)) {
                            Log::error("Key:" . $Key);

                            $this->append($Key);

                            array_push($drop, $Key);

                            $this->printPer(0, false);
                        }
                        if (preg_match("/^Serial/", $Value)) {
                            Log::error("Key:" . $Key . " == " . $Value);


                            $Array = explode("Serial/", $Value);

                            if (sizeof($Array) == 2) {
                                $Team = trim($Array[0]);

                                if (!$Team) array_push($drop, $Key);
                                else {

                                    $update[$Key] = $Team;

                                    $this->append("UpdateTeamName");
                                }

                                $Serial = trim($Array[1]);

                                $int = (int)$Serial;

                                if (is_int($int)) {
                                    if (!isset($Item->PrintRun) or ($int != $Item->PrintRun)) {
                                        $update['PrintRun'] = $int;
                                        $this->append("UpdatePrintRun");
                                    }
                                } else {
                                    if (isset($Item->PrintRun)) {
                                        array_push($drop, "PrintRun");
                                        $this->append("UnsetPrintRun");
                                    }

                                    $update['Serial'] = $Serial;
                                    $this->append("SetSerial");
                                }
                            }

                            $this->printPer(0, false);
                        }

                        $this->itemUpdate($Item->_id, $update, $drop);
                    }

                }

                if (($process % 100) == 0)
                    $this->printPer();
                else
                    $this->success++;
            }
        });

        $this->printPer();
    }

    function getData($Data, $type)
    {

        if(is_object($Data))
        {
           // Log::error("Type Error : ". json_encode($Data));
            return $Data;
        }

        switch ($type) {
            case 'string' :
                $Data = trim($Data);

                if (!$Data) return null;

                if (!preg_match("/^([0-9a-zA-Z\#\/\(\:\'“]+)/", $Data)) return null;
                if (in_array(strtolower($Data), $this->emptyWord)) return null;
                return $Data;

            case 'float' :
                $Data = trim($Data);
                if (!$Data) return null;
                $Sp = preg_match("/([0-9\.\,]+)$/", $Data, $match);
                if ($Sp && $match[1]) return (float)str_replace(',', '', $match[1]);

                return null;

            case 'int' :

                $Data = trim($Data);
                if (!$Data) return null;
                $Sp = preg_match("/([0-9\,]+)$/", $Data, $match);
                if ($Sp && $match[1]) return (int)str_replace(',', '', $match[1]);
                return null;

            case 'year' :
                return $this->parseYear($Data);

        }
    }

    function get($Key, $match = false, $type = "string")
    {

        switch ($type) {
            case 'Season' :

                $Data = $this->getYear($this->get('Season', true), true);

                if (!$Data) $Data = $this->getYear($this->get('Set'), true);
                //  if(!$Data) $Data = $this->getYear($this->get('Year', true), true);

                if (!$Data) $Data = $this->getYear($this->Item->ItemTitle);

                if (!$Data) return null;

                $this->Item->year = (string)$Data->year;

                if (isset($Data->to)) return $Data->from . '-' . substr($Data->to, 2, 2);
                else return $Data->from;
                break;

            case 'Type' :

                if (isset($this->Item->Graded)) {
                    $graded = strtolower($this->Item->Graded);
                    if ($graded === 'yes') return "Card";
                }

                if (isset($this->Item->ItemCondition))
                    if (stripos($this->Item->ItemCondition, 'Used')) return "Card";

                if (isset($this->Item->Configration)) {
                    $configType = strtolower($this->Item->Configration);
                    if ($configType === 'case') return "Case";
                }

                if (stripos($this->Item->ItemTitle, ' Case')) return "Case";
                if (isset($configType) && $configType === 'box') return "Box";
                if (stripos($this->Item->ItemTitle, 'Box')) return "Box";

                Log::emergency(" Type Not Found : " . json_encode($this->Item));

                return '';
                break;
        }

        if ($match) {
            if (isset($this->Item->$Key)) {

                $Data = $this->getData($this->Item->$Key, $type);
                if ($Data) return $Data;
            }

            foreach ($this->Item as $field => $Value) {
                $pos = stripos($field, $Key);

                if ($pos !== false) {
                    $Data = $this->getData($Value, $type);
                    if ($Data) return $Data;
                }
            }
        }

        return (isset($this->Item->$Key)) ? $this->getData($this->Item->$Key, $type) : null;
    }


    function checkSerial()
    {
        $checklist = new ScCheckLists();

        $checklist = $checklist->where("Team", 'LIKE', '%Serial%');

        $this->total = $checklist->count();

        Log::info("TOTAL : " . number_format($this->total));


        $checklist->chunkById(1000, function ($Result) use (&$process) {

            foreach ($Result as $Item) {

                $update = $unset = [];

                $Item = json_decode(json_encode($Item));

                foreach ($Item as $Key => $Value) {
                    if (preg_match("/^Serial/", $Key)) {
                        array_push($unset, $Key);
                        $this->append('Unset_' . $Key);
                    }
                }

                $Array = explode("Serial/", $Item->Team);

                if (sizeof($Array) == 2) {
                    $Team = trim($Array[0]);
                    if (!$Team) array_push($unset, "Team");
                    else {

                        $update['Team'] = $Team;
                        $this->append("UpdateTeamName");
                    }

                    $Serial = trim($Array[1]);

                    $int = (int)$Serial;

                    if (is_int($int)) {
                        if (!isset($Item->PrintRun) or ($int != $Item->PrintRun)) {
                            $update['PrintRun'] = $int;
                            $this->append("UpdatePrintRun");
                        }
                    } else {
                        if (isset($Item->PrintRun)) {
                            array_push($unset, "PrintRun");
                            $this->append("UnsetPrintRun");
                        }

                        $update['Serial'] = $Serial;
                        $this->append("SetSerial");

                    }

                    $this->updateCheckList($Item->_id, $update, $unset);
                }

                if (($process % 100) == 0)
                    $this->printPer();
                else
                    $this->success++;

            }


        });

        $this->printPer();
        return;
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


    function ebayImport()
    {

        $ebayProduct = new EbayProducts();
        $ebayItem = new EbayItems();

        $ebayProduct = $ebayProduct->where('Status', "Complete");
        $ebayProduct = $ebayProduct->whereNull('Imported');
        $ebayProduct = $ebayProduct->orderBy('_id');
        $this->total = $ebayProduct->count();

        $ebayProduct->chunkbyId(100, function ($Result) use ($ebayItem) {

            foreach ($Result as $Item) {

                $this->Item = json_decode(json_encode($Item));

                $this->Obj = new \stdClass();

                $Obj = new \stdClass();

                $Obj->sport = $this->get('Sport');
                $Obj->type = $this->get('Type', false, 'Type');

                $Obj->ebayItemNo = $this->get('EbayItemNumber');
                $Obj->itemTitle = $this->get('ItemTitle');

                $Obj->ended = $this->get('Ended');
              //  $Obj->endedAt = [$this->get('EndedAt')];

                $Obj->soldPrice = $this->get('SoldPrice', false, 'float');
                $Obj->shipping = $this->get('Shipping', false, 'float');

                $Obj->set = $this->get('Set');
                $Obj->itemImage = $this->get('ItemImage');
                $Obj->itemCondition = $this->get('ItemCondition');

                $Obj->manufacturer = $this->get('Manufacturer', true);

                $Obj->season = $this->get('Season', true, 'Season');
                $Obj->year = $this->get('Year', true, 'year');
                $Obj->material = $this->get('Material', true);
                $Obj->team = $this->get('Team', true);
                $Obj->player = $this->get('Player', true);

                $Obj->league = $this->get('League', true);
                $Obj->autographed = $this->get('Autographed');
                $Obj->numberOfCases = $this->get('NumberOfCases', false, 'int');
                $Obj->numberOfBoxes = $this->get('NumberOfBoxes', false, 'int');
                $Obj->numberOfCards = $this->get('NumberOfCards', false, 'int');

                $Obj->grade = $this->get('Grade', true, 'float');
                $Obj->signedBy = $this->get('SignedBy', true);

                //$Obj->EbayItemUrl = $this->get('EbayItemUrl');

                foreach ($Obj as $Key => $Value) {
                    if (!$Value) unset($Obj->$Key);
                }

                $ebayItem->create((array)$Obj);

                $this->setImported(true);

                if (($this->success % 100) == 0)
                    $this->printPer();
                else
                    $this->success++;
            }
        });

        $this->printPer();
    }

    function setImported($mark)
    {
        $ebayProduct = new EbayProducts();
        $ebayProduct->where("_id", $this->Item->_id)->update(['Imported' => $mark]);
        $this->append("Imported");

    }


    function itemUpdate($id, array $update, array $drop)
    {

        if (sizeof($update)) {
            $ebayProduct = new EbayProducts();
            $ebayProduct = $ebayProduct->where("_id", $id);
            $ebayProduct->update($update);
        }

        if (sizeof($drop)) {
            $ebayProduct = new EbayProducts();
            $ebayProduct = $ebayProduct->where("_id", $id);

            foreach ($drop as $Field) {
                $ebayProduct->unset($Field);
                $this->append($Field);
            }
        }


    }


    function parseYear($yy)
    {
        $year = (int)$yy;

        if (!is_int($year)) {
            return null;
        }

        if ($year < 100) {
            $year = ($year < 24) ? $year + 2000 : $year + 1900;
            return $this->parseYear($year);
        }


        if ($year > ((int)date("Y")) + 1) {
            return null;
        }

        if ($year < 1800) return null;

        return $year;
    }

    function getYear($Title, $static = false)
    {
        $Obj = new \stdClass();
        $Obj->to = null;

        if (preg_match("/^([0-9]{4}|[0-9]{2})+([\/\-])?([0-9]{4}|[0-9]{2})?/", $Title, $match)) {
            $Obj->title = (isset($match[2])) ? str_replace($match[2], '-', $match[0]) : $match[0];
            $Obj->year = $Obj->from = $this->parseYear(trim($match[1]));
            if (isset($match[3]))
                $Obj->to = $this->parseYear(trim($match[3]));
            return $Obj;
        }

        if (preg_match("/^([0-9]{4}|[0-9]{2})+([\/\-])?([0-9]{4}|[0-9]{2})?[\s]+/", $Title, $match)) {
            $Obj->title = (isset($match[2])) ? str_replace($match[2], '-', $match[0]) : $match[0];
            $Obj->year = $Obj->from = $this->parseYear(trim($match[1]));
            if (isset($match[3]))
                $Obj->to = $this->parseYear(trim($match[3]));
            return $Obj;
        }

        if ($static) return null;


        if (preg_match("/([\s]+[0-9]{4}|[\s]+[0-9]{2})+([\-])?([0-9]{4}|[0-9]{2})?[\s]+/", $Title, $match)) {

            $Obj->title = trim($match[0]);
            $Obj->year = $Obj->from = $this->parseYear(trim($match[1]));

            if (isset($match[3]))
                $Obj->to = $this->parseYear(trim($match[3]));
            return $Obj;
        }

        if (preg_match("/([\s]+[0-9]{4}+)$/", $Title, $match)) {
            $Obj->title = trim($match[0]);
            $Obj->year = $Obj->from = $this->parseYear(trim($match[0]));
            return $Obj;
        }

        return null;
    }


    function ebayProducts()
    {
        ini_set('memory_limit', '-1');

        $ebayProduct = new EbayProducts();
        $ItemsModel = new Item();

        $max = $ItemsModel->max('ebay_item_no') ?? 0;

        Log::notice(" MAX ITEM NUMBER IS : " . $max);

        // $ebayProduct =  $ebayProduct->whereNotNull('EbayItemNumber');

        if ($max)
            $ebayProduct = $ebayProduct->where('EbayItemNumber', '>', (string)$max);
        $ebayProduct = $ebayProduct->orderBy('EbayItemNumber');

        $result = $ebayProduct->options(['allowDiskUse' => true])->take(1000000)->get();
        $lastItemNo = 0;
        $this->override = $this->error = 0;

        $this->total = $result->count();

        Log::warning("TOTAL :" . $this->total . " ROWS RUN");

        foreach ($result as $Item) {
            //$Obj = $this->parseProduct($Item);


            if ($Item->EbayItemNumber == $lastItemNo) {
                $this->override++;
                $this->printPer();
                continue;
            }

            $Obj = new \stdClass();

            $Obj->ebay_item_no = $Item->EbayItemNumber;


            $Obj->item_title = $Item->ItemTitle;
            $Obj->item_images = substr($Item->ItemImage, 0, 65530);
            $Obj->item_type = $Item->Configuration;

            $Shipping = str_replace(['$', 'AU'], "", $Item->Shipping);
            $Obj->shipping_price = is_float((float)$Shipping) ? (float)$Shipping : 0;
            $Obj->sold_price = is_float((float)$Item->SoldPrice) ? (float)$Item->SoldPrice : 0;

            $EndDate = date_parse_from_format("d.m.Y H:i:s", $Item->Ended);

            if ($EndDate['year'] && $EndDate['second'])
                $Obj->ended_at = $EndDate['year'] . '-' . $EndDate['month'] . '-' . $EndDate['day'] .
                    ' ' . $EndDate['hour'] . ":" . $EndDate['minute'] . ":" . $EndDate['second'];

            else
                $this->error++;

            unset($Item->_id);
            unset($Item->EbayItemNumber);
            unset($Item->ItemTitle);
            unset($Item->ItemImage);
            unset($Item->Shipping);
            unset($Item->SoldPrice);
            unset($Item->CreatedAt);
            unset($Item->UpdatedAt);
            unset($Item->Ended);
            unset($Item->EbayItemUrl);

            $Obj->item_info = json_encode($Item);


            try {

                if ($ItemsModel->create((array)$Obj)) {
                    //EbayProducts::where("_id", $Item->_id)->update(['complete' => ($Item->complete) ? $Item->complete++ : 1]);

                    $lastItemNo = $Obj->ebay_item_no;

                    $this->printPer();

                    //Log::info("Ebay Item Save" . json_encode($Obj) );
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }


}
