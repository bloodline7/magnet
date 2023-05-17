<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Events\Logger as EventLogger;
use Ausumsports\Admin\Models\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Console extends Controller
{

    function console($param)
    {
        //$param = $request->route()->parameters();
        Log::info("Console Command:".$param);
        Artisan::call($param, $this->getCommandOptions($param));
         return Artisan::output();
      //  broadcast(new EventLogger($output));
    }

    function getCommandOptions($command): array
    {
        switch ($command) {
            case   'route:list' :
                return ['--ansi' => true, '--compact' => true];
            default :
                return ['--ansi' => true];

        }
    }
}
