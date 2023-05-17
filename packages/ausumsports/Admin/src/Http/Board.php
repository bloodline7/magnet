<?php

namespace Ausumsports\Admin\Http;

use App\Http\Controllers\Controller;

use Ausumsports\Admin\Events\Logger as EventLogger;
use Ausumsports\Admin\Models\Board as  BoardModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\Log;

class Board extends Controller
{
    function index()
    {
        $data = BoardModel::orderBy('id', 'desc')->get();
        return View("adminViews::board.boardList", ['data' => $data]);
    }

    function Create()
    {
        return View("adminViews::board.create");
    }

}
