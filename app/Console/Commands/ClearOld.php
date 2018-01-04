<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class ClearOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old offer which not have lead for a longtime';

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
        DB::beginTransaction();

        $now = Carbon::now()->timestamp;

        try {

            DB::statement("create table temp_offers like offers;");
            DB::statement("ALTER TABLE temp_offers CHANGE id id INT(10) UNSIGNED NOT NULL;");
            DB::statement("INSERT INTO temp_offers select t1.* FROM offers t1 join network_clicks t2 on t1.id = t2.offer_id GROUP BY t1.id;");
            DB::statement("ALTER TABLE temp_offers CHANGE id id INT(10) AUTO_INCREMENT;");
            DB::statement("RENAME TABLE offers TO backup_offers_".$now);
            DB::statement("RENAME TABLE temp_offers TO offers");


            DB::commit();
            flash('success', 'Old Offer not have lead for 1 week clear!');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('error', $e->getMessage());
        }
    }
}
