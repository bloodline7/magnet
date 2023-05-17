<?php

namespace Bloodline7\Logger;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use  Blooodline7\Logger\Events\Personal;
use Bramus\Ansi\Writers\BufferWriter;
use Illuminate\Support\Facades\Log;
use Predis\Client;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL;

class PrintPer
{

    public array $message;
    public int $total;
    public int $success;
    /**
     * @var mixed|string
     */
    private $title;
    private $start;
    private Client $redis;
    private Ansi $ansi;

    public function __construct($title="")
    {
        $this->ansi =  new Ansi(new BufferWriter());

        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => env("REDIS_HOST", '127.0.0.1'),
            'port'   => env("REDIS_PORT", 6379),
        ]);


        $this->title = $title;

        $this->init();
    }

    public function init($total=1000)
    {
        $this->start = microtime(true);
        $this->message = [];
        $this->success = 0;
        $this->total = $total;
    }

    function getEraseCode(): string
    {

        $return = "";

        try {
            if($line = $this->redis->get('printPercentLine')) {

                for($i=0; $i<$line; $i++) {
                    $return .= $this->ansi->cuu()->el(EL::ALL)->get();
                }
            }
        }
        catch (\Exception $exception)
        {
            return '';
        }

        return $return;
    }


    function append($Key, $Message = '')
    {
        if ($Message)
            $this->message[$Key] = $Message;
        else
            $this->message[$Key] = (isset($this->message[$Key])) ? $this->message[$Key] + 1 : 1;
    }

    function getPercent(): float|int
    {
        if($this->total)
        {
           $per = $this->success / $this->total;
           return ($per < 1) ? $per : 1;
        }
        return 0;
    }

    public function strPad($strVal, $len=10, $space=' '): string
    {

        $valLen = mb_strLen($strVal);
        $startBlank = str_pad( '', ceil( ($len - $valLen) / 2 ) , $space);
        $endBlank = str_pad( '', $len - (mb_strLen($startBlank) + $valLen) , $space);

        return $startBlank . $strVal . $endBlank;
    }


    function getProgress($max=100): string
    {


        $Percent = $this->getPercent();


        $len = floor($Percent * $max);

        $progress = $this->ansi->color(SGR::COLOR_FG_GREEN_BRIGHT)->text(str_repeat("■", $len))->reset()->get();
      //  $progress = str_pad("|", $len);

       // Log::info($progress);

        $progress .= $this->ansi->color(SGR::COLOR_FG_BLACK_BRIGHT)->text(str_repeat("□", $max - $len))->reset()->get();

        $Percent = floor($Percent * 10000) / 100;

        $Num = "[" . str_pad(" ", 6 - strlen($Percent) ) . $Percent . "%]";
        $progress .= " " .$this->ansi->color(SGR::COLOR_FG_YELLOW_BRIGHT)->text( $Num )->reset()->get();
        return $progress;
    }

    function getRunningTime()
    {
        $time = microtime(true) - $this->start;
        $hour = floor($time/3600);
        $Spend = "Running : " . date("[".$hour.":i:s]", $time);

        return $this->ansi->color(SGR::COLOR_FG_CYAN_BRIGHT)->text( $Spend )->get();
    }

    function getRemainingTime()
    {
        $time = microtime(true) - $this->start;

        if(!$time) return;
        if(!$this->success) return;

        $sps = round($this->success / $time  * 1000) / 1000 ;

        $remain = (($this->total / $this->success) * $time) - $time;

        $hour = floor($remain/3600);
        $Spend = "Remaining : " . date($hour.":i:s ", $remain) . "Left    ($sps/Sec)";

        return $this->ansi->color(SGR::COLOR_FG_RED_BRIGHT)->text( $Spend )->reset()->get();
    }


    function run($success=true)
    {
        if($success) $this->success++;

        $Message = $this->printPer();


        if(app()->runningInConsole())
            echo $Message ."\n\r";
        else
            broadcast(new Personal($Message , 'admin'));

    }

    function getMessage($line=2): string
    {
        $message = "";

        if(sizeof($this->message))
        {
            foreach ($this->message as $Key => $Value) {
                $message .= $this->ansi->color([SGR::COLOR_BG_YELLOW, SGR::COLOR_FG_WHITE_BRIGHT])->text(' ' .$Key. ' ')->reset()->get(). ":";
                $value =  (is_int($Value)) ? number_format($Value) : $Value;
                $message .=  $this->ansi->color(SGR::COLOR_FG_CYAN_BRIGHT)->text( $value )->reset()->get();
                $message .= "  ";
            }

            $line++;
        }

        if($this->total === $this->success) $line = 0;

        try {
            $this->redis->set('printPercentLine', $line);
            return $message;
        }
        catch (\Exception $exception) {
            return $message;
        }
    }


    function getTotal()
    {

        $result = $this->ansi->color([SGR::COLOR_BG_CYAN, SGR::COLOR_FG_WHITE_BRIGHT])->text(date("[Y-m-d H:i:s]"))->reset()->get();

        if($this->title)
            $result .= " ".$this->ansi->color([SGR::COLOR_BG_GREEN, SGR::COLOR_FG_WHITE_BRIGHT])->text(' ' .$this->title. ' ')->reset()->get();


        $result .= " ".$this->ansi->color([SGR::COLOR_FG_GREEN_BRIGHT])->text( number_format($this->success))->reset()->get();
        $result .= "/".$this->ansi->color([SGR::COLOR_FG_WHITE_BRIGHT])->text( number_format($this->total))->reset()->get();


        return $result;
    }

    function printPer(): string
    {
        $line = $this->getEraseCode();
        $line .= $this->getTotal() . "  " . $this->getRunningTime() . "  " . $this->getRemainingTime();
        $line .= "\r\n" . $this->getProgress();
        $line .= "\r\n" . $this->getMessage(2);

        return $line;
    }
}
