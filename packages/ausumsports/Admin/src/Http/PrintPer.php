<?php

namespace Ausumsports\Admin\Http;
use Illuminate\Support\Facades\Log;

class PrintPer
{

    public $message;
    public $total;
    public $success;
    /**
     * @var mixed|string
     */
    private $emitMessage;
    private $start;

    public function __construct($emitMessage="")
    {

        $this->emitMessage = $emitMessage;
        $this->start = microtime(true);

        $this->message = [];
        $this->total = $this->success = 0;

    }

    function append($Key, $Message = '')
    {
        if ($Message)
            $this->message[$Key] = $Message;
        else
            $this->message[$Key] = (isset($this->message[$Key])) ? $this->message[$Key] + 1 : 1;
    }

    function printPer($back = 2, $success=true)
    {
        if($success)
         $this->success++;

        $time = microtime(true) - $this->start;

        $Spend = date("[H:i:s]", $time);

        $per = round(($this->success / $this->total) * 10000) / 100;
        emit($this->emitMessage."   " . number_format($this->success) . '/' . number_format($this->total) . ' Complete. [' . $per . '%] ' . $Spend, $back);
        $message = '';

        foreach ($this->message as $Key => $Value) {
            $message .= $Key . " : ";
            $message .= (is_int($Value)) ? number_format($Value) : $Value;
            $message .= "     ";
        }
        Log::notice($message);
    }
}
