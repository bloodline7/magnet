<?php

use Illuminate\Support\Facades\Log;
use Psy\Util\Json;
use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Bramus\Ansi\Writers\BufferWriter;

if (!function_exists('emit'))
{
    function emit($message, $channel = "")
    {
        $ansi = new Ansi(new BufferWriter());

        if($channel && is_int($channel))
        {
            for($i=0; $i<$channel; $i++)
            {
                $ansi = $ansi->cuu()->el(EL::ALL)->cursorBack(9999);
            }

            $channel = "";
        }


        $message = $ansi->color(SGR::COLOR_FG_GREEN_BRIGHT)
            ->text($message)
            ->get();


        if(app()->runningInConsole())
        {
            echo $message ."\n\r";
        }
        else
        broadcast(new \Ausumsports\Admin\Events\Personal($message , $channel  ));
    }
};

if (!function_exists('ok')) {
    function ok($message, $append = "")
    {

        $Result = [
            'success' => true,
            'message' => $message
        ];

        if ($append) {
            if (is_array($append))
                $Result = array_merge($Result, $append);
            else
                $Result = array_merge($Result, ['PK' => $append]);

        }

        return response()->json($Result);
    }
}


if (!function_exists('html')) {
    function html($View, $Data = [])
    {
        return response()->view($View, $Data, 200)->header('Content-Type', 'text/html');
    }

}


if (!function_exists('script')) {
    function script($View, $Data = [])
    {
        $View = "<script>" . $View . "</script>";

        return response()->make($View)->header('Content-Type', 'text/html');
    }

}

function sasset($file, $secure = true)
{
    $version = md5(filemtime($file)); //The MD5 is optional.
    return asset($file .'?'.$version, $secure);
}



if (!function_exists('error')) {
    function error($message, $code = 500)
    {

        $Result = [
            'success' => false,
            'message' => $message
        ];

        return response()->json($Result, $code);
    }
}



if (!function_exists('code')) {
    function code($group, $value = null)
    {

        $System = new Ausumsports\Admin\Http\System();
        return $System->code($group, $value);
    }
}


if (!function_exists('getConfig')) {

    function getConfig($Key, $cache = true)
    {
        return AusumSports\Admin\Http\System::getConfig($Key, $cache);
    }

}
