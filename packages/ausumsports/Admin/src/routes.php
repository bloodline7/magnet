<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


$prefix = config('admin.prefix');

//date_default_timezone_set(getConfig('admin_timezone'));

$controllerBase = config('admin.controller_base');


Route::group(['prefix' => $prefix, 'middleware' => ['web', 'Admin']], function () use ($controllerBase) {

    Route::get('/', $controllerBase . 'Admin@index');

    Route::post('/auth', $controllerBase . 'Auth@auth')->withoutMiddleware('Admin');
    Route::get('/system/console/{command}', $controllerBase . 'Console@console')->withoutMiddleware('Admin');
    Route::post('/message', $controllerBase . 'Auth@message');
    Route::get('/login', $controllerBase . 'Auth@login')->withoutMiddleware('Admin')->name('adminLogin');
    Route::post('/login', $controllerBase . 'Auth@AdminLogin')->withoutMiddleware(['Admin'])->name('adminLoginProcess');
    Route::get('/logout', $controllerBase . 'Auth@logout')->name('adminLogout');

    Route::get('/register', $controllerBase . 'Auth@register');
    Route::post('/register', $controllerBase . 'Auth@adminCreate');



    // Router

    Route::get('/system', $controllerBase . 'System@index');
    Route::get('/system/router', $controllerBase . 'Route@router');
    Route::put('/system/router', $controllerBase . 'Route@routerCreate');
    //Route::post('/system/router', $controllerBase . 'Route@routerUpdate');

    Route::get('/system/router-f', $controllerBase . 'Route@routerFront');
    Route::put('/system/router-f', $controllerBase . 'Route@routerFrontCreate');

    //Route::post('/system/router-f', $controllerBase . 'Route@routerUpdate');

    Route::get('/system/router-content', $controllerBase . 'Route@routerContent');
    Route::get('/system/router-content/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerContentEditor', ['id' => $id]);
    });


    Route::post('/system/router-content/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerContentUpdate', ['id' => $id]);
    });

    Route::delete('/system/router-content/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerContentDelete', ['id' => $id]);
    });


    Route::post('/system/router/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerUpdate', ['id' => $id]);
    });

    Route::post('/system/router-f/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerUpdate', ['id' => $id]);
    });

    Route::delete('/system/router/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerDelete', ['id' => $id]);
    });

    Route::delete('/system/router-f/{id}', function ($id) use ($controllerBase) {
        return app()->call($controllerBase . 'Route@routerDelete', ['id' => $id]);
    });

        return app()->call($controllerBase . 'Route@routing');
});
