<?php

namespace App\Console\Commands;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\PaymentTransactions;
use App\Wallets;
use App\PaymentOptions;

class PaymentBalancesUpdate extends Command
{

    public static function floattostr($val)
        {
            preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
            return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
        }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PaymentBalancesUpdate:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PaymentBalancesUpdate';

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


    public function handle() {


        foreach (PaymentOptions::where('tatum_accountid', '!=', null)->get() as $wallet) {
                  
                          try {
  


            $curl = curl_init();
            curl_setopt_array($curl, [
              CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account/".$wallet->tatum_accountid."/balance",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => [
                 "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
              ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
                $response = json_decode($response);


                if($wallet->crypto === 'xblzd' || $wallet->crypto === 'game1') {
                                                $amount = self::floattostr($response->availableBalance / 10000000000);
                                                                       $wallet->update(['balance' => $amount]);

                } else {
                       $wallet->update(['balance' => number_format($response->availableBalance, 7, '.', '')]);
                    }
            
                            } catch (\Exception $exception) {

                                        }



        



        }
    }
}
