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

class PaymentCallbackQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PaymentCallbackQueue:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment Queue Callback send';

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
        $yday = Carbon::yesterday()->timestamp; 
        $index = 0;

        foreach (PaymentTransactions::where('callbackstate', '=', '0')->get() as $transaction) {
                    
                    $paymentid = $transaction->id;
                    $txId = $transaction->txid;
                    $event = "deposit";
                    $from = $transaction->from;
                    $to = $transaction->to;
                    $amount = $transaction->amount;
                    $dollaramount = $transaction->amountusd;
                    $currency = $transaction->currency;
                    $timestamp = $transaction->created_at;
                    $callbackurl = $transaction->callbackurl;

                    if($transaction->callbacktries > 10) {
                        PaymentTransactions::where('txId', $txId)->update(['callbackstate' => '10']);
                    }

        try {



                    # Define function endpoint
                    $url = $callbackurl;
                    $ch = curl_init($url);

                    # Setup request to send json via POST. This is where all parameters should be entered.
                    $payload = json_encode( array("id" => $paymentid, "event" => $event, "txId" => $txId, "from" => $from, "to" => $to, "amount" => $amount, "currency" => $currency, "timestamp" => $timestamp));
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

                    # Decode the received JSON string
                    $resultdecoded = json_decode($result, true);

                    # Print status of request (should be true if it worked)
                    $gettransaction = DB::table('PaymentTransactions')->where('txid', '=', $txId)->first();

                    PaymentTransactions::where('txId', $txId)->update(['callbacktries' => ($gettransaction->callbacktries + 1)]);

                    if($resultdecoded["ok"] === true) {
                        PaymentTransactions::where('txId', $txId)->update(['callbackstate' => '1']);
                    }                       

                    
                            } catch (\Exception $exception) {

                                        }



        }
    }
}
