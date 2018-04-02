<?php

namespace App\Console\Commands;

use Carbon\Carbon;
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

        $config = env('SITE_NAME');

        if ($config) {

            $this->line('Config='.$config);

            $time = Carbon::now()->subDays(5)->toDateTimeString();
            foreach (config('site.'.$config) as $site) {

                $networks = \DB::connection($site)->table('networks')->get();
                foreach ($networks as $network) {

                    try {
                        \DB::connection($site)->beginTransaction();

                        \DB::connection($site)->statement("delete from offers where network_id=".$network->id." AND id not in (select offer_id from network_clicks) and created_at < '$time' limit 1000");

                        //\DB::connection($site)->statement("delete from clicks where offer_id not in (select id from offers)");
                        \DB::connection($site)->commit();
                    } catch (\Exception $e) {
                        \DB::connection($site)->rollBack();
                        $this->line($e->getMessage());
                    }

                }
            }
        } else {
            $this->line('Can not get config variable');
        }

    }
}
