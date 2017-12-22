<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class DropKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drop:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop foreign key mysql';

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

        foreach (config('site.list') as $site) {
            \DB::connection($site)->statement("ALTER TABLE clicks DROP FOREIGN KEY clicks_offer_id_foreign");
            \DB::connection($site)->statement("ALTER TABLE clicks DROP FOREIGN KEY clicks_user_id_foreign");
        }

    }
}
