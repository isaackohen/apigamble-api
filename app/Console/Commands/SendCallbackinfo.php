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
use App\GameOptions;
use App\Apikeys;

class SendCallbackinfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CallBackinfo:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CallBackinfo';

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

        foreach (GameOptions::where('callbackurl', '!=', 'example')->where('operator', '!=', 'example')->get() as $gameoption) {
                    
                try {
                  //  $getactive = DB::table('Apikeys')->where('apikey', '=', $gameoption->apikey)->first();
                  //  $active = $gameoption->getactive;
                    $operator = $gameoption->operator;
                    $apikey = $gameoption->apikey;
                    $home_url = $gameoption->operatorurl;
                    $livecasino_prefix = $gameoption->livecasino_prefix;
                    $slots_prefix = $gameoption->slots_prefix;
                    $type = 'slots';
                    $evoplayprefix = $gameoption->evoplay_prefix;
                    $casinoid = $gameoption->id;

                        $geturlbase = $gameoption->callbackurl;
                        $first = str_replace("/", "%2F", $geturlbase);
                        $second = str_replace(":", "%3A", $first);

                        $urlbaseslash = substr($geturlbase,-1); // Check if last digit in URL ends with '/'

                        if($urlbaseslash != '/') {
                           $callback_urlbase = $second.'/';
                        } else {
                        $callback_urlbase = $second;
                        }

        $sendoff = 'http://slots.apigamble.com/api/operator/apigamble/create?operator='.$operator.'&type=slots&apikey='.$apikey.'&system=apigamble&casinoid='.$casinoid.'&home_url='.$home_url.'&evoplayprefix='.$evoplayprefix.'&base='.$callback_urlbase.'&slotsprefix='.$slots_prefix.'&livecasinoprefix='.$livecasino_prefix;
                    # Define function endpoint
                    $url = $sendoff;
                    $ch = curl_init($url);

                    # Setup request to send json via POST. This is where all parameters should be entered.
                    $payload = json_encode( array("status" => "ok"));
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
                    curl_setopt( $ch,   CURLOPT_MAXREDIRS, 10);

                    # Return response instead of printing.
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
                    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );

                    # Send request. 
                    $result = curl_exec($ch);
                    curl_close($ch);


                    
                          } catch (\Exception $exception) {
                                    }



        }
    }
}
