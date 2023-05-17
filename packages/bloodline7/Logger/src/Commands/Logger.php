<?php

namespace Bloodline7\Logger\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Bloodline7\Logger\PrintPer;


class Logger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get ebay product list save to mongoDB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug("Test Content...... ",   ['test' => true ]);
        Log::info("테스트 내용입니다.... " ,      ['테스트' => true ]);
        Log::notice("--------------------- NOTICE -------------------------");
        Log::warning("--------------------- WARNING -------------------------");
        Log::error("Error Find : On Error");
        Log::alert("Alert ==================== Use This.");
        Log::critical("Critical Error!");
        Log::emergency("emergency emergency  emergency emergency emergency emergency");

        $i = 0;
        $total = 1000;

        $printPer = new PrintPer("PrintPer Test");

        $printPer->init($total);

        while ($i <= $total)
        {

            if(!($i%33)) {

                $printPer->append("Test 33");
                Log::debug("Append at Line " . $i);
            }


            if(!($i%66)) {

                $printPer->append("Test 66");
                Log::warning("Append at Line " . $i);
            }

            if(!($i%99)) {

                $printPer->append("Test 99");
                Log::notice("Append at Line " . $i);
            }

            usleep(10000);

            $printPer->run();

            $i++;
        }



        Log::info("PrintPer Test Complete......");
        return 0;
    }
}
