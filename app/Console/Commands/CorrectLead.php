<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class CorrectLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correct:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run once time to fill all new fields in network_clicks table';

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

            $oldLeads = DB::connection($site)->table('network_clicks')->join('clicks', 'network_clicks.sub_id', '=', 'clicks.hash_tag')
                ->join('offers', 'clicks.offer_id', '=', 'offers.id')
                ->selectRaw('network_clicks.id as lead_id, network_clicks.network_id as lead_network_id, clicks.id as click_id, offers.id as offer_id, network_clicks.sub_id as lead_sub_id, network_clicks.amount as lead_amount')
                ->whereNull('network_clicks.offer_id')
                //->limit(10)
                ->get();

            foreach ($oldLeads as $oldLead) {
                DB::connection($site)->table('network_clicks')->where('id', $oldLead->lead_id)->update([
                    'offer_id' => $oldLead->offer_id,
                    'click_id' => $oldLead->click_id,
                    'json_data' => json_encode(['subid' => $oldLead->lead_sub_id, 'amount' => $oldLead->lead_amount*2, 'network_id' => $oldLead->lead_network_id])
                ]);
            }
        }

    }
}
