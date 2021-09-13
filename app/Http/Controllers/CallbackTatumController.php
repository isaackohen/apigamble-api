<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Response;
use ReflectionClass;

use App\Apikeys;
use App\PaymentForwardQueue;
use App\PaymentOptions;
use App\Wallets;
use App\PaymentTransactions;
use App\CallbackQueue;

class CallbackTatumController extends Controller
{ 

public static function floattostr($val)
{
    preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
    return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
}

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
			    public static function rateDollarBetshiba() {
			        return '0.04';
			    }

			    public static function rateDollarDoge() {
			        $value = self::apiRates();
			        $price = $value->DOGE->USD;
			        return $price;
			    }

			    public static function rateDollarGame1() {
			        $value = self::apiRates();
			        $price = $value->USDT->USD;
			        return $price;
			    }

			    public static function rateDollarGamblecoin() {
			        $value = self::apiRates();
			        $price = $value->USDT->USD;
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


				    public function sendCallbackDeposit($paymentid, $txId, $event, $from, $to, $amount, $dollaramount, $currency, $timestamp, $callbackurl)
				    {

        			try {

					# Define function endpoint
					$url = $callbackurl;
					$ch = curl_init($url);

					# Setup request to send json via POST. This is where all parameters should be entered.
					$payload = json_encode( array("id" => $paymentid, "event" => $event, "txId" => $txId, "from" => $from, "to" => $to, "amount" => $amount, "currency" => strtolower($currency), "timestamp" => $timestamp));
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
			    	$gettransaction = DB::table('PaymentTransactions')->where('txid', '=', $txId)->first();

				   	PaymentTransactions::where('txId', $txId)->update(['callbacktries' => ($gettransaction->callbacktries + 1)]);
					if($resultdecoded["ok"] === true) {
						PaymentTransactions::where('txId', $txId)->update(['callbackstate' => '1']);
					}    	 
							return $result;
				
                        } catch (\Exception $exception) {
                        	return;
                   		}

				    }



				    public function sendCallbackPending($txId, $event, $to, $amount, $currency, $callbackurl)
				    {

        			try {

					# Define function endpoint
					$url = $callbackurl;
					$ch = curl_init($url);

					# Setup request to send json via POST. This is where all parameters should be entered.
					$payload = json_encode( array("id" => $paymentid, "event" => $event, "txId" => $txId, "from" => $from, "to" => $to, "amount" => $amount, "currency" => strtolower($currency), "timestamp" => $timestamp));
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

					return $result;
				
                        } catch (\Exception $exception) {
                        	return;
                   		}

				    }


				public function pending(Request $request) 
			    {
			    	Log::debug('Pending: '.$request);

				    $findwallet = DB::table('Wallets')
					->where('wallet', '=', $request->to)
					->first();

				    $findtx = DB::table('PaymentTransactions')
					->where('txid', '=', $request->txId)
					->first();


				    $findoptions = DB::table('PaymentOptions')
					->where('apikey', '=', $findwallet->apikey)
					->where('crypto', '=', strtolower($request->currency))
					->first();

			    	$sendtocallbackurl = self::sendCallbackPending($request->txId, "pending", $request->to, $request->amount, $request->currency, $findoptions->callbackurl.'/pending');


			    }

				public function withdrawal(Request $request) 
			    {
			    	Log::debug('Withdrawal: '.$request);
			    }

				public function tatumWebhook(Request $request) 
			    {
			    	Log::debug($request);
					
					
				    $findtx = DB::table('PaymentTransactions')
					->where('txid', '=', $request->txId)
					->first();
					
					if($request->has('withdrawalId')){
						$withdraw = true;
						$address_wallet = $findtx->from;
						$from = $findtx->from;
						$to = $findtx->to;
					} else {
						$withdraw = false;
						$address_wallet = $request->to;
						$from = $request->from;
						$to = $request->to;
					}

				    $findwallet = DB::table('Wallets')
					->where('wallet', '=', $address_wallet)
					->first();

					if($request->currency === 'TRON') {
						$currentcurrency = 'TRX';
						if($findtx->txid === $request->txId) {
							return;
						}
					}	else {
						$currentcurrency = $request->currency;
						if($findtx->txid === $request->txId) {
							return;
						}
					}

				    $findoptions = DB::table('PaymentOptions')
					->where('apikey', '=', $findwallet->apikey)
					->where('crypto', '=', strtolower($currentcurrency))
					->first();
					
					if($findtx && ($withdraw == false)){
						return;	
					}

					if($request->currency === 'xBLZD') {
					    if($request->amount === '0') {
					    	return;
					    }
						    $amount = self::floattostr($request->amount / 10000000000);
							$dollaramount = round(($amount * self::rateDollarXblzd()), 2);
					}

					if($request->currency === 'GAME1') {
					    if($request->amount === '0') {
					    	return;
					    }
						    $amount = self::floattostr($request->amount / 10000000000);
							$dollaramount = round(($amount * self::rateDollarGame1()), 2);
					}
					if($request->currency === 'BETSHIBA') {
					    if($request->amount === '0') {
					    	return;
					    }
						    $amount = self::floattostr($request->amount / 10000000000);
							$dollaramount = round(($amount * self::rateDollarBetshiba()), 2);
					}	
					if($request->currency === 'GAMBLECOIN') {
					    if($request->amount === '0') {
					    	return;
					    }
						    $amount = self::floattostr($request->amount / 10000000000);
							$dollaramount = round(($amount * self::rateDollarGamblecoin()), 2);
					}	

					if($request->currency === 'BSC') {
					    if($request->amount === '0') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarBnb()), 2);
					}

					if($request->currency === 'BTC') {
					    if($request->amount === '0') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarBtc()), 2);
					}

					if($request->currency === 'ETH') {
					    if($request->amount === '0') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarEth()), 2);
					}

					if($request->currency === 'TRON') {
					    if($request->amount < '0.1') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarTrx()), 2);
					}

					if($request->currency === 'BCH') {
					    if($request->amount === '0') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarBch()), 2);
					}
					if($request->currency === 'DOGE') {
					    if($request->amount < '0.00001') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarDoge()), 2);
					}
					if($request->currency === 'LTC') {
					    if($request->amount < '0.00001') {
					    	return;
					    }
							$amount = $request->amount;
							$dollaramount = round(($amount * self::rateDollarLtc()), 2);
					}

					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
			            	'id' => $internalid,
			            	'from' => $from,
			                'to' => $to,
			                'amount' => $amount,
			                'apikey' => $findwallet->apikey,
			             	'amountusd' => $dollaramount,
			             	'callbackurl' => $findoptions->callbackurl,
			             	'callbackstate' => '0',
			             	'callbacktries' => '0',
			                'currency' => strtolower($request->currency),
			                'txid' => $request->txId,
			            	'external_id' => $request->accountId,
			                'created_at' => now()
		    			)
			    	); 


					if($request->currency === 'xBLZD'){
						if($withdraw == false){
			    			Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance + $amount, 7), 'updated_at' => now()]);
						} else {
							Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance - $amount, 7), 'updated_at' => now()]);
						}
					}	
					elseif($request->currency === 'GAME1'){
						if($withdraw == false){
			    			Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance + $amount, 7), 'updated_at' => now()]);
						} else {
							Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance - $amount, 7), 'updated_at' => now()]);
						}
					}	
					elseif($request->currency === 'GAMBLECOIN'){
						if($withdraw == false){
			    			Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance + $amount, 7), 'updated_at' => now()]);
						} else {
							Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance - $amount, 7), 'updated_at' => now()]);
						}
					}	
					elseif($request->currency === 'BETSHIBA'){
						if($withdraw == false){
			    			Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance + $amount, 7), 'updated_at' => now()]);
						} else {
							Wallets::where('wallet', $address_wallet)->update(['tokenbalance' => round($findwallet->tokenbalance - $amount, 7), 'updated_at' => now()]);
						}
					}	
					else {
						if($withdraw == false){
			    			Wallets::where('wallet', $address_wallet)->update(['balance' => round($findwallet->balance + $amount, 7), 'updated_at' => now()]);
						} else {
							Wallets::where('wallet', $address_wallet)->update(['balance' => round($findwallet->balance - $amount, 7), 'updated_at' => now()]);
						}
					}

				    				
					//Ready for lift off, tries to send deposit callback to user's url once, else will be in queue
					if($withdraw == false){
						$sendtocallbackurl = self::sendCallbackDeposit($internalid, $request->txId, "deposit", $from, $to, $amount, $dollaramount, $currentcurrency, now(), $findoptions->callbackurl);
					}

		    	return response()->json([
					'ok' => true
	       		], 200);	

				}
			   /*

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
							*/			

}
