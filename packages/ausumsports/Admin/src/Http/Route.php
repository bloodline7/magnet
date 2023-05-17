<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Models\Router;
use Ausumsports\Admin\Models\RouterContents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\Log;

class Route extends Controller
{
    function routing(Request $request)
    {


        if (!env('DB_DATABASE')) return false;


        try {
            $router = app()->make('router');
            $Data = Router::where(['use' => 1, 'area' => 'admin'])->get();

            //Log::info($request->getUri());

            $controllerBase = config('admin.controller_base');
            $controller = $controllerBase . 'Admin';

            foreach ($Data as $data) {
                $controllerName = preg_match("/@/", $data->controller) ? $controllerBase . $data->controller : $controller . '@' . $data->controller;

                $router = $router->middleware('Admin');

                switch ($data->method) {
                    case 'GET' :
                        $router->get($data->path, $controllerName);
                        break;
                    case 'PUT' :
                        $router->put($data->path, $controllerName);
                        break;

                    case 'POST' :
                        $router->post($data->path, function (\Illuminate\Http\Request $request) use ($controllerName, $data) {
                            $param = $request->route()->parameters();
                            return app()->call($controllerName, $param);
                        });
                        break;

                    case 'DELETE' :
                        $router->delete($data->path, function (\Illuminate\Http\Request $request) use ($controllerName, $data) {
                            $param = $request->route()->parameters();
                            return app()->call($controllerName, $param);
                        });
                        break;
                }
            }

        } catch (Exception $exception) {

            return error($exception->getMessage(), $exception->getCode());
        }

    }


    function router(Request $request)
    {

        $data = Router::where('area', 'admin')->orderBy('path', 'asc')->orderBy('method', 'asc')->paginate(150)
            ->withPath('/' . config('admin.prefix') . '/system/router');

        return View("adminViews::system/router", ['data' => $data, 'sub' => 'system']);
    }

    function routerFront(Request $request)
    {

        $data = Router::where('area', 'front')->orderBy('path', 'asc')->orderBy('method', 'asc')->paginate(150)
            ->withPath('/' . config('admin.prefix') . '/system/router-f');

        return View("adminViews::system/router", ['data' => $data, 'sub' => 'system']);
    }

    function routerContent(Request $request)
    {

        $data = Router::where('area', 'front')->where('method', 'GET')->orderBy('path', 'asc')->paginate(15)
            ->withPath('/' . config('admin.prefix') . '/system/router-content');

        return View("adminViews::system/router-content", ['data' => $data, 'sub' => 'system']);
    }


    function routerContentEditor($id)
    {

        $data = Router::find($id);

        return View("adminViews::system/router-content-editor", ['data' => $data, 'sub' => 'system']);
    }


    function routerContentUpdate($id, Request $request)
    {
        $content = $request->post('content');
        RouterContents::updateOrCreate(
            ['id' => $id],
            ['use' => $request->use, 'content' => $content]
        );

        return ok('Save Complete', $id);
    }


    function routerFrontCreate(Request $request): \Illuminate\Http\JsonResponse
    {

        $router = new Router();
        $router->area = "front";
        $router->method = $request->get('method');
        $router->path = $request->path;
        $router->controller = $request->controller;
        $router->title = $request->title;
        $router->use = $request->use;

        $router->save();

        return ok('Create Complete', $router->id);
    }


    function routerCreate(Request $request): \Illuminate\Http\JsonResponse
    {

        $router = new Router();
        $router->method = $request->get('method');
        $router->path = $request->path;
        $router->controller = $request->controller;
        $router->title = $request->title;
        $router->use = $request->use;

        $router->save();

        /*
        $content = new RouterContents();
        $content->id = $router->id;
        $content->use = $request->use;

        if ($request->get('content'))
            $content->content = $request->get('content');

        $content->save();*/

        return ok('Create Complete', $router->id);
    }

    function routerUpdate($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $router = Router::find($id);
        $router->method = $request->get('method');
        $router->path = $request->path;
        $router->controller = $request->controller;
        $router->title = $request->title;
        $router->use = $request->use;
        $router->save();

        return ok('Update Complete', $id);

    }

    function routerDelete($id): \Illuminate\Http\JsonResponse
    {
        Router::find($id)->delete();
        return ok('Remove Complete', $id);

    }

}
