<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Events\Logger as EventLogger;
use Ausumsports\Admin\Models\RouterContents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\Log;

class Admin extends Controller
{
    function index()
    {
        return View("adminViews::index");
    }

    function main()
    {
        return redirect()->route('adminLogin');
    }
}
