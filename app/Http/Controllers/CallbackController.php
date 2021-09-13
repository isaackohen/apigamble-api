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
use App\PaymentForwardQueue;
use App\PaymentOptions;


use App\Wallets;
use App\PaymentTransactions;
use App\CallbackQueue;

class CallbackController extends Controller
{ 

				public static function apiRates()
			    {
				   $result = Cache::remember('apirates', 180, function () {
			       return json_decode(file_get_contents('https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,ETH,BNB,USDT,ETH,BCH,LTC,DOGE,TRX&tsyms=USD'));
			       });
				   return $result;
			    }

				public static function rateDollarLtc() {
			        $value = self::apiRates();
					$price = $value->LTC->USD;
			        return $price;
			    }

			    public static function rateDollarxBLZD() {
			        try {
			            if (!Cache::has('conversions: xblzd'))
			                Cache::put('conversions: xblzd', file_get_contents("https://api.coingecko.com/api/v3/coins/blizzard?localization=false&market_data=true"), now()->addHours(1));
			            $json = json_decode(Cache::get('conversions: xblzd'));
			            return $json->market_data->current_price->usd;
			        } catch (\Exception $e) {
			            return -1;
			        }
			    }

			    public static function rateDollarTrx() {
			        $value = self::apiRates();
			        $price = $value->TRX->USD;
			        return $price;
			    }

			    public static function rateDollarBtc() {
			        $value = self::apiRates();
			        $price = $value->BTC->USD;
			        return $price;
			    }

				public static function rateDollarBnb() {
			        $value = self::apiRates();
					$price = $value->BNB->USD;
			        return $price;
			    }

			    public static function rateDollarBch() {
			        $value = self::apiRates();
			        $price = $value->BCH->USD;
			        return $price;
			    }

			    public static function rateDollarEth() {
			        $value = self::apiRates();
			        $price = $value->ETH->USD;
			        return $price;
			    }


			    public function callbackTester(Request $request) 
			    {
			    		Log::notice($request);

				    	return response()->json([
							'ok' => true
		           		], 200);	
			    }


				    public function sendCallbackDeposit($paymentid, $event, $currency, $type, $address, $amount, $timestamp, $callbackurl)
				    {

					# Define function endpoint
					$url = $callbackurl;
					$ch = curl_init($url);

					# Setup request to send json via POST. This is where all parameters should be entered.
					$payload = json_encode( array("paymentid" => $paymentid, "event" => $event, "address" => $address, "type" => $type, "currency" => $currency, "amount" => $amount, "timestamp" => $timestamp) );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
					curl_setopt( $ch,	CURLOPT_MAXREDIRS, 10);

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
					Log::notice($result);
					Log::notice($url);
					Log::notice($payload);

					
					return $resultdecoded["ok"];
				    }



				public function paymentDeposits(Request $request) 
			    {

			    Log::debug($request);

			    		if($request->action === 'deposit') {
			    			if(!$request->contractaddress) {

				    			if($request->binancecoinaddress) {
				    				$address = $request->binancecoinaddress;
				    				$checktoken = Wallets::where('wallet', $address)->first();
				    				$currency = $checktoken->currency;
				    				$dollaramount = round(($request->amount * self::rateDollarBnb()), 2);
				    			}
			    			if($request->type === 'TRX') {
				    				$currency = 'trx';
				    				$address = $request->tronaddress;
				    				$dollaramount = round(($request->amount * self::rateDollarTrx()), 2);
				    				Log::info($address);
			    			}

			    			if($request->ethereumaddress) {
				    				$currency = 'eth';
				    				$address = $request->ethereumaddress;
				    				$dollaramount = round(($request->amount * self::rateDollarEth()), 2);
			    			}
			    			} 

				  			   if($request->binancecoinaddress and $request->type === 'BEP-20') {
						  			   		if($request->contractaddress === '0x9a946c3cb16c08334b69ae249690c236ebd5583e') {
						    				$currency = 'xblzd';
						    				$address = $request->binancecoinaddress;
						    				$dollaramount = round(($request->amount * self::rateDollarXblzd()), 2);
				    					}
									}  	

			    				$checkwallet = Wallets::where('wallet', $address)->where('currency', $currency)->first();
			    				
			    				if($checkwallet == null) return false; //add to failed callback log, means wallet is currently not assigned to an apikey



			    				$checktransactions = PaymentTransactions::where('to', $address)->where('external_id', $request->id)->first();

			    				//Check for duplicates

			    				if(!$checktransactions) {

			    				if($checkwallet->currency === 'xblzd'){
				    				if($request->type === 'ETH') {
				    				Wallets::where('wallet', $address)->where('currency', $currency)->update(['balance' => round($checkwallet->balance + $request->amount, 7), 'updated_at' => now()]);
				    				} else {
				    				Wallets::where('wallet', $address)->where('currency', $currency)->update(['tokenbalance' => round($checkwallet->tokenbalance + $request->amount, 7), 'updated_at' => now()]);
				    				}
				    				//More wallet types later

				    				} else {
				    					Wallets::where('wallet', $address)->where('currency', $currency)->update(['balance' => round($checkwallet->balance + $request->amount, 7), 'updated_at' => now()]);
				    				}

				    				$internalid = DB::table('PaymentTransactions')->count() + 1;
				    				$transaction = DB::table('PaymentTransactions')->insertGetId(array(
						            	'id' => $internalid,
						            	'from' => 'n/a',
						                'to' => $address,
						                'amount' => $request->amount,
						                'apikey' => $checkwallet->apikey,
						                'callbackurl' => $checkwallet->callbackurl, 
						             	'amountusd' => $dollaramount,
						                'currency' => $currency,
						                'txid' => 'n/a',
						            	'external_id' => $request->id,
						                'subscribed' => '0',
						                'created_at' => now()
					    			)
							    ); 


			    				$getcallbackurl = PaymentOptions::where('apikey', $checkwallet->apikey)->first();



							    //Getting ready for event!

			    				$callbackqueue = DB::table('CallbackQueue')->insertGetId(array(
						            	'id' => DB::table('CallbackQueue')->count() + 1,
						            	'internal_id' => $internalid,
						            	'external_id' => $request->id,
						            	'action' => $request->action,
						            	'timestamp' => $request->timestamp,
						            	'currency' => $currency,
						            	'type' => $request->type,
						            	'address' => $address,
						            	'amount' => $request->amount,
						            	'callbackurl' => $getcallbackurl->callbackurl,
						            	'queue_state' => '0',
						            	'queue_tries' => '0',
						          		'created_at' => now()
						    		)
				    			);

			    				//Ready for lift off, tries to send deposit callback to user's url once, else will be in queue

			    					$sendtocallbackurl = self::sendCallbackDeposit($request->id, "deposit", $currency, $request->type, $address, $request->amount, $request->timestamp, $getcallbackurl->callbackurl);
			    					if($sendtocallbackurl === true) {
				    					CallbackQueue::where('address', $address)->where('currency', $currency)->update(['queue_state' => '1']);

				    					//add additional parameters, like forwarding 
			    				} }

			    				//Okidoki, no more callbacks needed!
							    	return response()->json([
										'ok' => true
					           		], 200);	
					    		}					           
							}				

}
