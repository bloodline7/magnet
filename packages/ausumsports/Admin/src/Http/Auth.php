<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Events\Logger as EventLogger;
use Ausumsports\Admin\Models\Admin;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Goutte\Client;

class Auth extends Controller
{

    /**
     * @throws Exception
     */
    function auth(Request $request)
    {
        $channel = $request->get('channel_name');

        return ['channel'=> $channel];


        Log::warning($request->all());
        Log::warning(auth()->user()->id);


        $userId = $request->user()->id;

          if(!$userId)
            return error("User Not Login");

        if(!$channel)
            return error("Channel Name Not Found");

        $userNo = substr($channel, strrpos($channel, '.')+1);
        if(!$userNo)
            throw new Exception("No User Code of Private Channel :". $channel);


        if($userNo != $userId)
            throw new Exception("User Code and User ID not Matched :". $channel . ' != ' . $userId);

        return ['name'=> $request->user()->name, 'email' => $request->user()->email];
    }

    function console($param)
    {
        //$param = $request->route()->parameters();
        //Log::info("Console Command", $param);
        //Artisan::call($param, $this->getCommandOptions($param));

        return Artisan::output();
        //  broadcast(new EventLogger($output));
    }

    function getCommandOptions($command): array
    {
        switch ($command) {
            case   'route:list' :
                return ['--ansi' => true, '--compact' => true ];
            default :
                return ['--ansi' => true, '--auth' => true];

        }
    }


    function message(Request $request)
    {

        $msg = $request->msg;
        emit(auth()->user()->email ." : " . $msg);

    }

    function authAny(Request $request)
    {
        return ['name'=> "GUEST", 'email' => ""];
    }


    function login()
    {
        return View("adminViews::login");
    }


    function register()
    {
        return View("adminViews::system/register");
    }


    function adminInfo($id)
    {
        $admin = Admin::find($id);
        return View("adminViews::system/adminInfo", ['data' => $admin ]);
    }


    function adminUpdate(Request $request, $id)
    {
        $admin = Admin::find($id);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if($request->password)
        $admin->password = bcrypt($request->password);
        $admin->save();

        return ok('Update Complete', $admin->id);
    }


    function adminRemove($id)
    {
        $data = Admin::find($id);
        $data->delete();
        return ok('Remove Complete', $data->id);
    }




    function adminCreate(Request $request): \Illuminate\Http\JsonResponse
    {

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);

        $admin->save();
        return ok('Create Complete', $admin->id);
    }


    public function logout()
    {
        try {
            auth()->logout();
        }
        catch (\Exception $e)
        {
            Log::warning($e->getMessage());
            return error("Logout Failed");
        }

        return ok('Logout Complete');

    }


    public function AdminLogin(Request $request)
    {

        $Model = Admin::where('email', $request->email);
        $Data = $Model->first();

        if(!$Data)
            return error('Please Check your Email');

        if(!Hash::check($request->password, $Data->password))
            return error('Please check  Email  and Password again');

        auth()->loginUsingId($Data->id);

        return ok('Login Success:' . $Data->id , ['id' => $Data->id]);
    }


}
