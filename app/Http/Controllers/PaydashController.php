<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use neto737\BitGoSDK\BitGoSDK;
use neto737\BitGoSDK\Enum\AddressType;
use neto737\BitGoSDK\Enum\CurrencyCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Response;
use ReflectionClass;
use App\PaymentSecretkeysBitgo;
use App\Apikeys;
use App\PaymentOptions;
use App\Wallets;
use App\PaymentTransactions;
use App\PaymentWithdrawals;
use App\CallbackQueue;

class PaydashController extends Controller
{



	public static function create($apikey, $email, $amount, $label) 
	{


		$findoperator = DB::table('Apikeys')
		->where('apikey', '=', $apikey)
		->where('type', '=', 'creditcard')
		->first();

		$findcallback = DB::table('CreditcardOptions')
		->where('apikey', '=', $apikey)
		->first();

		if(!$findoperator) {
			return response()->json([
				'status' => 'error',
				'reason' => 'AUTHORIZATION ERROR'
			], 401);
		}


    $currency = 'USD';
    $total = $amount;
		$meta = "{\"apikey\":\"".$apikey."\",\"label\":\"".$label."\",\"callback\":\"".$findcallback->callbackurl."\"}";


    $data = array(
        "apiKey"        =>  'c3bdd34f-caea-4776-8cbb-9d809fa80c8b',
        "email"         =>  $email,
        "amount"        =>  $total,
        "webhookURL"    =>  'https://apigamble.com/api/paydash',
        "returnURL"     =>  'https://apigamble.com/creditcard/success',
        "metadata"      =>  $meta,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://paydash.co.uk/api/merchant/create");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($raw);
    $redirectURL = "https://apigamble.com/creditcard/checkout/" . $response->response;

            
			
/*
			DB::table('Wallets')->insertGetId(
				array('wallet' => $response->address, 'label' => $label, 'apitype' => 'tatum', 'currency' => $currency, 'derivationkey' => $response->derivationKey, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'subscribed' => 1, "contractaddress" => $contractaddress, "privatekey" => '0', 'created_at' => now())
			);
*/
			return response()->json([
					'status' => 'ok',
					'url' => $redirectURL,
					'order_id' => $response->response,
					'amount' => $total,
					'meta' => $label,
					'callback_url' => $findcallback->callbackurl,
					'forward_enabled' => $findcallback->forward_enabled
			]);

	}

}

