<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;
use Ausumsports\Admin\Models\Product as ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Ausumsports\Admin\Models\Sport;


class Product extends Controller
{

    function index() {

        return View("adminViews::index", ['sub' => 'product']);
    }


    function getSports()
    {
        $Sports =  new Sport();
        $data = $Sports->orderBy('id', 'asc')->paginate(12);
        return View("adminViews::product/sports", [ 'data' => $data, 'sub' => 'product']);
    }


    function saveSports(Request $request)
    {
        $Sports =  new Sport();

        if($request->id)
        {
            $Sports->id = $request->id;
        }

        $Sports->sports_name = $request->sports_name;
        $Sports->save();

        return ok($request->sports_name . " was Saved");
    }



    function list(Request $request) {

        DB::enableQueryLog();

        $Product = new ProductModel();


        if($keyword = $request->keyword)
        {

            $search = $request->search;

            $where = 0;

            foreach ($search as $key) {

                switch ($key)
                {
                    case 'name' :
                        $Product  = $Product->orWhere('name', 'like', "%$keyword%");
                        break;
                    case 'brand' :
                        $Product  = $Product->orWhereHas('brand', function ($query) use ($keyword) {
                            return $query->Where('name', 'like', "%$keyword%");
                        });
                        break;
                    case 'category' :

                        $Product  = $Product->orWhereHas('cates', function ($query) use ($keyword)
                        {
                            return $query->Where('name', 'like', "%$keyword%");
                        });
                        break;
                    case 'description' :
                        set_time_limit(0);

                        $Product  = $Product->orWhereHas('attributes', function ($query) use ($keyword) {
                            return $query->whereRaw("description like ?", ['%'.$keyword.'%']);
                        });

                        break;
                    default :
                        //  $where++;
                        Log::warning($key);
                        break;
                }

            }



            /*
                            $Product = $Product->where(function ($query) use ($search, $keyword) {
                                    foreach ($search as $key) {
                                        $query->orWhere($key, "like', "%$keyword%");
                                    }
                                });
            */

            if ($startDate = $request->input('startDate')) {
                $Product = $Product->where("updated_at", ">=", date("Y-m-d 00:00:00", strtotime($startDate)));
            }

            if ($endDate = $request->input('endDate')) {
                $Product = $Product->where("updated_at", "<=", date("Y-m-d 23:59:59", strtotime($endDate)));
            }
        }



        $data = $Product->orderBy('id', 'desc')->paginate(12);

        Log::info(DB::getQueryLog());


        $data->withPath(strtok($_SERVER["REQUEST_URI"], '?'));

        return View("adminViews::product/list", [ 'data' => $data, 'sub' => 'product']);
    }

    function view($id) {

        $Product = ProductModel::find($id);
        return View("adminViews::product/view", [ 'data' => $Product, 'sub' => 'product']);
    }

    function brand() {
        return View("adminViews::product/brand", ['sub' => 'product']);
    }

    function brandEditor($id = null) {
        return View("adminViews::product/brandEditor", ['sub' => 'product']);
    }

    function category() {
        return View("adminViews::product/category", ['sub' => 'product']);
    }

    function addToCart(Request $request)
    {
        $cart = new \Ausumsports\Admin\Models\Cart();

        $cart->admin_id = $request->user()->id;
        $cart->product_id = $request->product_id;

        $cart->save();

        return ok($cart->product->name . " was added to cart");
    }

    function product() {
        return redirect( '/'.config('admin.prefix').'/product/crawling');
    }

    function productCrawling() {

        return View("adminViews::product/crawring");
    }


    function crawling($id, Request $request)
    {

        $class = null;
        $keyword = $request->get('keyword');



        try {



            Log::alert("Search Keyword : $keyword");

            switch ($id)
            {
                case 'dacardworld' :

                    $class = new \Ausumsports\Admin\Crawler\DaCardWorld();
                    break;

                case 'blowoutcards1' :

                    $class = new \Ausumsports\Admin\Crawler\BlowoutCards();

                    break;

                case 'steelcitycollectibles' :

                    $class = new \Ausumsports\Admin\Crawler\SteelCity();
                    break;

                case 'chicagolandsportscards' :

                    $class = new \Ausumsports\Admin\Crawler\ChicagoLand();
                    break;




                default :
                    return;

                    // $class = new \Ausumsports\Admin\Crawler\DaCardWorld();
                    break;

            }

            if($class)
            {
                Log::alert('Class Found');

                $result = $class->search($keyword);

                return Ok($result) ;
            }


        }
        catch (\Exception $e)
        {

            Log::error($e->getMessage());;

        }

    }


}
