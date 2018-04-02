<?php

namespace App\Console\Commands;

use App\Network;
use App\Site;
use Illuminate\Console\Command;

class TestGeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:geo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        /*$getIp = geoip()->getLocation('220.246.70.155');
        dd($getIp);
        $isoCode = $getIp['isoCode'];
        echo $isoCode;*/

        $network = \DB::connection('azoffers')->table('networks')->where('id', 30)->first();

        Site::feed($network);


    }
}
