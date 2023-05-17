<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Crawler\Ebay;
use Ausumsports\Admin\Http\Import;
use Ausumsports\Admin\Http\EbayData;

use Ausumsports\Admin\Models\Brand;
use Ausumsports\Admin\Models\League;
use Ausumsports\Admin\Models\Manufacturer;
use Ausumsports\Admin\Models\Player;
use Ausumsports\Admin\Models\Product;
use Ausumsports\Admin\Models\Mongo\Products;
use Ausumsports\Admin\Models\Roster;
use Ausumsports\Admin\Models\Season;
use Ausumsports\Admin\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Ausumsports\Admin\Models\Sport;
use Ausumsports\Admin\Crawler\CardBoardConnection;
use Ausumsports\Admin\Crawler\SportsCardChecklist;
use Ausumsports\Admin\Crawler\Point130;


class Collection extends Controller
{

    function index()
    {

        return View("adminViews::index", ['sub' => 'collection']);
    }


    function view_crawling()
    {
        return View("adminViews::collection/crawling", ['sub' => 'collection']);
    }


    function crawlingProcess(Request $request)
    {
        set_time_limit(0);
        $crType = $request->crType;
        if (is_array($crType)) {
            foreach ($crType as $type) {

                switch ($type) {

                    case "ebayCardList" :
                        $ebay = new Ebay();
                        $ebay->getCardList();
                        return Ok("Data Crawling....");

                    case "ebayBoxList" :
                        $ebay = new Ebay();
                        $ebay->getBoxList();
                        return Ok("Data Crawling....");

                    case "ebayCaseList" :
                        $ebay = new Ebay();
                        $ebay->getCaseList();
                        return Ok("Data Crawling....");

                    case "ebayProducts" :
                        $ebay = new Ebay();
                        $ebay->getProducts();
                        return Ok("Data Crawling....");


                    case "scc" :
                        $Scc = new SportsCardChecklist();

                        return Ok("Data Crawling....");
                        break;

                    case "Brands" :
                        $cardBoardConnection = new CardBoardConnection();
                        $Result = $cardBoardConnection->brands();
                        return Ok("Data Crawling....");
                        break;

                    case "Product" :
                        $cardBoardConnection = new CardBoardConnection();
                        $Result = $cardBoardConnection->products();
                        return Ok("Data Crawling....");
                        break;

                    case "ProductNew" :
                        $cardBoardConnection = new CardBoardConnection();
                        $Result = $cardBoardConnection->getProduct();
                        return Ok("Data Crawling....");
                        break;


                    case "ProductNew-brand" :
                        $cardBoardConnection = new CardBoardConnection();
                        $Result = $cardBoardConnection->getNewBrand();
                        return Ok("Data Crawling....");
                        break;



                    case "ProductSetSports" :
                        $Result = $this->setSports();
                        break;

                    case "ProductDetail" :
                        $cardBoardConnection = new CardBoardConnection();
                        $Result = $cardBoardConnection->getProductDetail();
                        break;

                    case "checkListMenu" :

                        $point130 = new Point130();
                        $Result = $point130->checklistMenu();
                        break;

                    case "checkListCard" :
                        $point130 = new Point130();
                        $Result = $point130->checklist();
                        break;

                    default :
                        return error("Not Defined Type : " . $type);
                        break;
                }
            }
        } else {

            return Ok("Crawling Area Not Found.");

        }

    }


    function import()
    {
        return View("adminViews::collection/import", ['sub' => 'collection']);
    }

    function importProcess(Request $request)
    {
        set_time_limit(0);

        $import = new Import();

        $crType = $request->crType;


        if (is_array($crType))
        {
            foreach ($crType as $type) {

                switch ($type) {

                    case "cleanUpCheckList" :

                        $Result = $import->checkListCleanUp();

                        break;


                    case "cleanUpEbay" :

                        $Result = $import->ebayCleanUp();
                        break;

                    case "ebayProducts" :
                        $Result = $import->ebayProducts();
                        break;


                    case "itemAnalysis" :

                        $Result = $import->analysis();
                        break;

                    case "productAnalysis" :
                        $Result = $import->productAnalysis();
                        break;

                    case "ebayFileImporting" :

                        $ebayData = new EbayData();

                        $ebayData->save();

                        $ebayData->savefinal();

                        break;


                    default :
                        return error("Not Defined Type : " . $type);
                        break;
                }
            }
        } else {

            return Ok("Crawling Area Not Found.");

        }


        return Ok($Result);

    }



    function setSports()
    {
        $sports = new Sport();
        $Sports = $sports->orderBy('id')->get();
        foreach ($Sports as $Sport) {
            $name = $Sport->sports_name;
            $keyword = $Sport->sports_keyword;


            $products = new Products();
            $products
                ->whereNull("sports")
                ->where("product_title", "LIKE", '%' . $name . '%')
                ->update(['sports' => $name]);

            $keywordArr = explode(',', $keyword);

            foreach ($keywordArr as $keyword) {
                $keyword = trim($keyword);

                if ($keyword) {
                    $products
                        ->whereNull("sports")
                        ->where("product_title", "LIKE", '%' . $keyword . '%')
                        ->update(['sports' => $name]);
                }
            }
        }
    }

    function Sports()
    {
        $Sports = new Sport();
        $data = $Sports->orderBy('id', 'asc')->get();
        return View("adminViews::collection/sports", ['data' => $data, 'sub' => 'collection']);
    }


    function saveSports(Request $request)
    {
        $Sports = new Sport();

        if ($request->id) {
            $Sports->id = $request->id;
        }

        $Sports->sports_name = $request->sports_name;
        $Sports->sports_keyword = $request->sports_keyword;
        $Sports->save();

        return ok($request->sports_name . " was Saved");
    }


    function updateSports($id, Request $request)
    {
        $Sports = Sport::find($id);

        $Sports->sports_name = $request->sports_name;
        $Sports->sports_keyword = $request->sports_keyword;
        $Sports->save();

        return ok($request->sports_name . " was Updated");
    }


     function getManufacturers()
     {
         $Manufacturer = new Manufacturer();
         $data = $Manufacturer->orderBy('id', 'asc')->get();

         $result = [];
         foreach ($data as $Item) {
            $result[$Item->id] = $Item->name;
         }

         return $result;
     }

    function getSports()
    {
        $Sports = new Sport();
        $data = $Sports->orderBy('id', 'asc')->get();

        $result = [];
        foreach ($data as $Item) {
            $result[$Item->id] = $Item->sports_name;
        }

        return $result;
    }

    function getLeagues()
    {
        $League = new League();
        $data = $League->orderBy('id', 'asc')->get();

        $result = [];
        foreach ($data as $Item) {
            $result[$Item->id] = $Item->name;
        }

        return $result;
    }



    function manufacturers()
    {
        $Manufacturer = new Manufacturer();
        $data = $Manufacturer->orderBy('id', 'asc')->get();

        return View("adminViews::collection/manufacturers", ['data' => $data, 'sub' => 'collection']);
    }

    function createManufacturers(Request $request)
    {
        $Manufacturer = new Manufacturer();

        $Manufacturer->name = $request->name;
        $Manufacturer->logo = $request->logo;
        $Manufacturer->countries = $request->countries;
        $Manufacturer->save();
        return Ok("Manufacturers Saved.");
    }


    function updateManufacturers($id, Request $request)
    {
        $Manufacturer = Manufacturer::find($id);

        $Manufacturer->name = $request->name;
        $Manufacturer->logo = $request->logo;
        $Manufacturer->countries = $request->countries;
        $Manufacturer->save();

        return Ok("Manufacturers Updated.");
    }


    function deleteManufacturers($id)
    {
        $Manufacturer = Manufacturer::find($id);
        $Manufacturer->delete();

        return Ok("Manufacturers Deleted.");
    }


    function brands()
    {
        $Brand = new Brand();
        $data = $Brand->orderBy('id', 'asc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/brands');

        return View("adminViews::collection/brands", ['data' => $data, 'sub' => 'collection']);
    }

    function createBrands(Request $request)
    {
        $Brand = new Brand();
        $Brand->name = $request->name;
        $Brand->manufacturer_id = $request->manufacturer_id;

        $Brand->save();

        return Ok("Brands Saved.");
    }


    function updateBrands($id, Request $request)
    {
        $Brand = Brand::find($id);

        $Brand->name = $request->name;
        $Brand->manufacturer_id = $request->manufacturer_id;

        $Brand->save();

        return Ok("Brands Updated.");
    }


    function deleteBrands($id)
    {
        Brand::find($id)->delete();
        return Ok("Brands Deleted.");
    }



    function leagues()
    {
        $League = new League();
        $data = $League->orderBy('sports_id', 'asc')->orderBy('default', 'desc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/leagues');

        return View("adminViews::collection/leagues", ['data' => $data, 'sub' => 'collection']);
    }

    function createLeagues(Request $request): \Illuminate\Http\JsonResponse
    {
        $League = new League();

        $League->sports_id = $request->sports_id;

        $League->title = $request->title;
        $League->code = $request->code;

        $League->region = $request->region;
        $League->logo = $request->logo;
        $League->popularity = $request->popularity;
        $League->default = $request->default;

        $League->save();

        return Ok("League Saved.");
    }


    function updateLeagues($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $League = League::find($id);
        $League->sports_id = $request->sports_id;
        $League->title = $request->title;
        $League->code = $request->code;
        $League->region = $request->region;
        $League->logo = $request->logo;
        $League->popularity = $request->popularity;
        $League->default = $request->default;

        $League->save();

        return Ok("League Updated.");
    }

    function deleteLeagues($id): \Illuminate\Http\JsonResponse
    {
        League::find($id)->delete();
        return Ok("League Deleted.");
    }




    function seasons(Request $request)
    {
        $Season = new Season();
        $Season = $Season->select("seasons.*")->join('leagues', 'league_id', '=', 'leagues.id');

        if($request->sports) {
            $Season = $Season->where('leagues.sports_id', $request->sports);
        }

        if($request->keyword) {
            $Season = $Season->where('season_title', 'like' , '%'.trim($request->keyword).'%');
        }


        $sports = $this->getSports();

        $data = $Season->orderBy('leagues.sports_id', 'asc')->orderBy('leagues.default', 'desc')->orderBy('season_from', 'asc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/seasons');

        return View("adminViews::collection/seasons", ['data' => $data, 'sports'=>$sports, 'sub' => 'collection']);
    }

    function createSeasons(Request $request): \Illuminate\Http\JsonResponse
    {
        $Season = new Season();

        $Season->sports_id = $request->sports_id;

        $Season->title = $request->title;
        $Season->code = $request->code;

        $Season->region = $request->region;
        $Season->logo = $request->logo;
        $Season->popularity = $request->popularity;
        $Season->default = $request->default;

        $Season->save();

        return Ok("Season Saved.");
    }


    function updateSeasons($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $Season = Season::find($id);
        $Season->sports_id = $request->sports_id;
        $Season->title = $request->title;
        $Season->code = $request->code;
        $Season->region = $request->region;
        $Season->logo = $request->logo;
        $Season->popularity = $request->popularity;
        $Season->default = $request->default;

        $Season->save();

        return Ok("Season Updated.");
    }

    function deleteSeasons($id): \Illuminate\Http\JsonResponse
    {
        Season::find($id)->delete();
        return Ok("Season Deleted.");
    }



    function teams(Request $request)
    {
        ini_set('memory_limit', '-1');

        $Team = new Team();
        $Team  = $Team->select("teams.*")->join('leagues', 'league_id', '=', 'leagues.id');

        if($request->sports) {
            $Team = $Team->where('leagues.sports_id', $request->sports);
        }

        if($request->keyword) {
            $Team = $Team->where('team_name', 'like' , '%'.trim($request->keyword).'%');
        }


        $sports = $this->getSports();

        $data = $Team->orderBy('leagues.sports_id', 'asc')->orderBy('leagues.default', 'desc')->orderBy('id', 'asc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/teams');

        return View("adminViews::collection/teams", ['data' => $data, 'sports'=>$sports, 'sub' => 'collection']);
    }

    function createTeams(Request $request): \Illuminate\Http\JsonResponse
    {
        $Team = new Team();

        $Team->title = $request->title;
        $Team->code = $request->code;

        $Team->region = $request->region;
        $Team->logo = $request->logo;
        $Team->popularity = $request->popularity;
        $Team->default = $request->default;

        $Team->save();

        return Ok("Team Saved.");
    }


    function updateTeams($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $Team = Team::find($id);
        $Team->team_name = $request->team_name;
        $Team->team_region = $request->team_region;
        $Team->team_logo = $request->team_logo;
        $Team->save();

        return Ok("Team Updated.");
    }

    function deleteTeams($id): \Illuminate\Http\JsonResponse
    {
        Team::find($id)->delete();
        return Ok("Team Deleted.");
    }



    function players(Request $request)
    {
        ini_set('memory_limit', '-1');
        $Player = new Player();
        if($request->sports) {
            $Player = $Player->where('sports_id', $request->sports);
        }
        if($request->keyword) {
            $Player = $Player->where('full_name', 'like' , '%'.trim($request->keyword).'%');
        }
        $sports = $this->getSports();
        $data = $Player->orderBy('sports_id', 'asc')->orderBy('full_name', 'asc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/players');
        return View("adminViews::collection/players", ['data' => $data, 'sports'=>$sports, 'sub' => 'collection']);
    }

    function createPlayers(Request $request)
    {
        $Player = new Player();

        $Player->sports_id = $request->sports_id;
        $Player->first_name = $request->first_name;
        $Player->last_name = $request->last_name;
        $Player->full_name = $request->full_name;

        $Player->birth_date = $request->birth_date;
        $Player->birth_place = $request->birth_place;

        $Player->familiarity = $request->familiarity;
        $Player->retirements = $request->retirements;
        $Player->affiliation_college = $request->affiliation_college;

        $Player->save();

        return Ok("Player Saved", ['id' => $Player->id]);
    }

    function updatePlayers($id, Request $request)
    {
        $Player = Player::find($id);

        $Player->sports_id = $request->sports_id;
        $Player->first_name = $request->first_name;
        $Player->last_name = $request->last_name;
        $Player->full_name = $request->full_name;
        $Player->birth_date = $request->birth_date;
        $Player->birth_place = $request->birth_place;
        $Player->familiarity = $request->familiarity;
        $Player->retirements = $request->retirements;
        $Player->affiliation_college = $request->affiliation_college;
        $Player->save();
        return Ok("Player Updated", ['id' => $Player->id]);
    }

    function deletePlayers($id)
    {
        $Player = Player::find($id);
        $Player->delete();
        return Ok("Player Deleted", ['id' => $Player->id]);
    }


    function rosters(Request $request)
    {
      //  ini_set('memory_limit', '-1');
        $Roster = new Roster();
        //$Team  = $Team->select("teams.*")->join('leagues', 'league_id', '=', 'leagues.id');
        $Roster = $Roster->select("rosters.*")
            ->join('teams', 'team_id', '=', 'teams.id')
            ->join('players', 'player_id', '=', 'players.id')
            ->join('leagues', 'teams.league_id', '=', 'leagues.id');

        if($request->sports) {
            $Roster = $Roster->where('leagues.sports_id', $request->sports);
        }

        if($request->keyword) {
            $Roster = $Roster->where('players.full_name', 'like' , '%'.trim($request->keyword).'%');
            $Roster = $Roster->orWhere('teams.team_name', 'like' , '%'.trim($request->keyword).'%');
        }


        $data = $Roster->orderBy('leagues.sports_id', 'asc')
            ->orderBy('leagues.default', 'desc')
            ->orderBy('leagues.id', 'asc')
            ->orderBy('teams.team_name', 'asc')
            ->orderBy('player_id', 'asc')
            ->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/rosters');

        $sports = $this->getSports();

        return View("adminViews::collection/rosters", ['data' => $data, 'sports'=>$sports, 'sub' => 'collection']);
    }

    function createRosters(Request $request)
    {
        $Roster = new Roster();

        $Roster->team_id = $request->team_id;
        $Roster->player_id = $request->player_id;
        $Roster->position = $request->position;
        $Roster->stats = json_encode($request->states);
        $Roster->save();

        return Ok("Roster Saved", ['id' => $Roster->id]);
    }

    function updateRosters($id, Request $request)
    {
        $Roster = Roster::find($id);
        $Roster->team_id = $request->team_id;
        $Roster->player_id = $request->player_id;
        $Roster->position = $request->position;
        $Roster->stats = json_encode($request->states);
        $Roster->save();

        return Ok("Roster Updated", ['id' => $Roster->id]);
    }

    function deleteRosters($id)
    {
        $Roster = Roster::find($id);
        $Roster->delete();

        return Ok("Roster Deleted", ['id' => $Roster->id]);
    }


    function products(Request $request)
    {
        $Product = new Product();

        if($request->sports) {
            $Product = $Product->where('sports_id', $request->sports);
        }

        if($request->keyword) {
            $Product = $Product->where('product_title', 'like' , '%'.trim($request->keyword).'%');
        }

        $sports = $this->getSports();
        $data = $Product->orderBy('sports_id', 'asc')->orderBy('product_title', 'asc')->paginate(20)->withPath('/' . config('admin.prefix') . '/collection/products');

        return View("adminViews::collection/products", ['data' => $data, 'sports' => $sports,  'sub' => 'collection']);
    }

    function createProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $Product = new Product();

        $Product->sports_id = $request->sports_id;

        $Product->title = $request->title;
        $Product->code = $request->code;

        $Product->region = $request->region;
        $Product->logo = $request->logo;
        $Product->popularity = $request->popularity;
        $Product->default = $request->default;

        $Product->save();

        return Ok("League Saved.");
    }


    function updateProducts($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $Product = Product::find($id);
        $Product->sports_id = $request->sports_id;
        $Product->title = $request->title;
        $Product->code = $request->code;
        $Product->region = $request->region;
        $Product->logo = $request->logo;
        $Product->popularity = $request->popularity;
        $Product->default = $request->default;

        $Product->save();

        return Ok("League Updated.");
    }

    function deleteProducts($id): \Illuminate\Http\JsonResponse
    {
        Product::find($id)->delete();
        return Ok("League Deleted.");
    }

}
