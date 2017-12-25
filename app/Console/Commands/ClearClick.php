<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearClick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:click';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old Click which not have offer id';

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
            \DB::connection($site)->statement("delete from clicks where offer_id not in (select id from offers)");
        }
    }
}
