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

class PaymentTatumController extends Controller
{

				public static function apiRates()
			    {
				   $result = Cache::remember('apirates', 180, function () {
			       return json_decode(file_get_contents('https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,ETH,BNB,USDT,ETH,BCH,LTC,DOGE,TRX&tsyms=USD'));
			       });
				   return $result;
			    }

			 		public static function rateDollarBtc() {
			        $value = self::apiRates();
					$price = $value->BTC->USD;
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
			    public static function rateDollarBetshiba() {
			        return '0.04';
			    }

				public static function rateDollarBnb() {
			        $value = self::apiRates();
					$price = $value->BNB->USD;
			        return $price;
			    }
				public static function rateDollarDoge() {
			        $value = self::apiRates();
					$price = $value->DOGE->USD;
			        return $price;
			    }
				public static function rateDollarLtc() {
			        $value = self::apiRates();
					$price = $value->LTC->USD;
			        return $price;
			    }
				public static function rateDollarTron() {
			        $value = self::apiRates();
					$price = $value->TRX->USD;
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



	public function createBETSHIBA($tatumaccount, $index, $apikey) 
	{
		$findbsc = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->where('crypto', '=', 'bsc')
		->first();

		self::createTatumAddress($findbsc->tatum_accountid, $index, $apikey);
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/account/".$tatumaccount."/address?index=".$index,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => [
		    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}

	public function createGAMBLECOIN($tatumaccount, $index, $apikey) 
	{
		$findbsc = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->where('crypto', '=', 'bsc')
		->first();

		self::createTatumAddress($findbsc->tatum_accountid, $index, $apikey);
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/account/".$tatumaccount."/address?index=".$index,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => [
		    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}

	public function createxBLZD($tatumaccount, $index, $apikey) 
	{
		$findbsc = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->where('crypto', '=', 'bsc')
		->first();

		self::createTatumAddress($findbsc->tatum_accountid, $index, $apikey);
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/account/".$tatumaccount."/address?index=".$index,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => [
		    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}
	public static function sendCrypto($apikey, $currency, $amount, $from, $to, $masterpass) 
		{
		$findoperator = DB::table('Apikeys')
		->where('apikey', '=', $apikey)
		->where('type', '=', 'paykey')
		->first();

		if(!$findoperator) {
			return response()->json([
					'status' => 'error',
					'reason' => 'AUTHORIZATION ERROR'
			], 401);
		}

		$findmasterpass = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->where('crypto', '=', $currency)
		->where('masterpass', '=', $masterpass)
		->first();

		$checkwallet = Wallets::where('wallet', $from)->where('apikey', $apikey)->first();
		if(!$checkwallet) {
			return response()->json([
					'status' => 'error',
					'reason' => 'Wallet not found on your account'
			], 401);
		}
		if(!$findmasterpass) {
			return response()->json([
					'status' => 'error',
					'reason' => 'AUTHORIZATION ERROR: Wrong Password'
			], 401);
		}
			/*

		if($currency === 'btc') {
				return response()->json([
					'status' => 'error',
					'reason' => 'Bitcoin unavailable for withdraw automatic yet, please ask support to retrieve funds.'
				], 401);
			$privateKey = self::privateKey('bitcoin', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendTransfer('bitcoin', $from, $privateKey->key, $to, $amount);

			$balance = self::getBal('bitcoin', $from);
			$balance = json_decode($balance);
			$incoming = $balance->incoming;
			$outgoing = $balance->outgoing;
			$currentbalance = (($incoming - $outgoing) * 0.99);


				return response()->json([
					$starttransfer
				], 200);
					}

			*/


		if($currency === 'eth') {
			$privateKey = self::privateKey('ethereum', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendTransfer('ethereum', $from, $privateKey->key, $to, $amount);
				return response()->json([
					$starttransfer
				], 200);
		}

	if($currency === 'rng') {
			$currency = 'bsc';
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendBSC('BSC', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);
			if($txid) {
				sleep(6);
					$getbsc = self::getBSCbyTX($txid->txId);			Log::notice($getbsc);
					$getbsc = json_decode($getbsc);
				  $dollaramount = round(($amount * self::rateDollarBnb()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'txId(RNG base)' => $txid->txId,
					"blockHash(RNG seed)" => $getbsc->blockHash,
					'txn(RNG nonce)' => $getbsc->nonce,
					"transactionIndex" => $getbsc->transactionIndex,
					'proof of RNG to show' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bsc',
					'apikey' => $findoperator->apikey
				], 200);
			} else {

					return response()->json([
					'txId(base)' => $txid->txId,
					"blockNumber(seed)" => $getbsc->blockNumber,
					'txn(nonce)' => $getbsc->nonce,
					"transactionIndex" => $getbsc->blockNumber,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bsc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}

		if($currency === 'trx') {
			$privateKey = self::privateKey('tron', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$mnemonic = $privateKey->key;
			$xpub = $findmasterpass->xpub;
			$accountid = $findmasterpass->tatum_accountid;
			$starttransfer = self::sendTron($mnemonic, $xpub, $accountid, $to, $amount);


			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);

			if($txid) {
				  $dollaramount = round(($amount * self::rateDollarTron()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://tronscan.org/#/transaction/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'tron',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}

		if($currency === 'bch') {
			$mnemonic = $findmasterpass->mnemonic;
			$xpub = $findmasterpass->xpub;
			$accountid = $findmasterpass->tatum_accountid;
			$starttransfer = self::sendBitcoincash($mnemonic, $xpub, $accountid, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);


			if($txid) {
				  $dollaramount = round(($amount * self::rateDollarDoge()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://blockchair.com/dogecoin/transaction/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bch',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}
		if($currency === 'btc') {
			$mnemonic = $findmasterpass->mnemonic;
			$xpub = $findmasterpass->xpub;
						$normalprivkey = self::privateKey('bitcoin', $checkwallet->derivationkey, $mnemonic);

			echo $normalprivkey;
			$accountid = $findmasterpass->tatum_accountid;
			$starttransfer = self::sendBitcoins($mnemonic, $xpub, $accountid, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);


			if($txid) {
				  $dollaramount = round(($amount * self::rateDollarBtc()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://blockchair.com/transaction/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'btc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}


		if($currency === 'ltc') {
			$mnemonic = $findmasterpass->mnemonic;
			$xpub = $findmasterpass->xpub;
			$accountid = $findmasterpass->tatum_accountid;
			$starttransfer = self::sendLitecoin($mnemonic, $xpub, $accountid, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);


			if($txid) {
				  $dollaramount = round(($amount * self::rateDollarDoge()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://blockchair.com/litecoin/transaction/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'ltc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}


		if($currency === 'doge') {
			$mnemonic = $findmasterpass->mnemonic;
			$xpub = $findmasterpass->xpub;
			$accountid = $findmasterpass->tatum_accountid;
			$starttransfer = self::sendDogecoin($mnemonic, $xpub, $accountid, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);


			if($txid) {
				  $dollaramount = round(($amount * self::rateDollarDoge()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://blockchair.com/dogecoin/transaction/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'doge',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}

		if($currency === 'betshiba') {
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			Log::notice($privateKey);
			$privateKey = json_decode($privateKey);

			$starttransfer = self::sendBEP20Token('BETSHIBA', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);

			if($txid->txId) {
				  $dollaramount = round(($amount * self::rateDollarBetshiba()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bsc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}

		if($currency === 'gamblecoin') {
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			Log::notice($privateKey);
					$privateKey = json_decode($privateKey);

			$starttransfer = self::sendBEP20Token('GAMBLECOIN', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);

			if($txid->txId) {
				  $dollaramount = round(($amount * self::rateDollarGamblecoin()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bsc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}


		if($currency === 'bsc') {
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendBSC('BSC', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);
			Log::notice($starttransfer);

			if($txid->txId) {
				  $dollaramount = round(($amount * self::rateDollarBnb()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'bsc',
					'apikey' => $findoperator->apikey
				], 200);
			}
			}
		if($currency === 'game1') {
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendBEP20Token('GAME1', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);

			if($txid->txId) {
				  $dollaramount = round(($amount * self::rateDollarXblzd()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
			}
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'GAME1',
					'apikey' => $findoperator->apikey
				], 200);
		}

		if($currency === 'xblzd') {
			$privateKey = self::privateKey('bsc', $checkwallet->derivationkey, $findmasterpass->mnemonic);
			$privateKey = json_decode($privateKey);
			$starttransfer = self::sendBEP20Token('xBLZD', $from, $privateKey->key, $to, $amount);
			$txid = json_decode($starttransfer);

			if($txid->txId) {
				  $dollaramount = round(($amount * self::rateDollarXblzd()), 2);
					$internalid = DB::table('PaymentTransactions')->count() + 1;
					$transaction = DB::table('PaymentTransactions')->insertGetId(array(
          	'id' => $internalid,
          	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $findoperator->apikey,
           	'amountusd' => $dollaramount,
           	'callbackurl' => 'n/a',
           	'callbackstate' => '2',
           	'callbacktries' => '0',
            'currency' => $currency,
            'txid' => $txid->txId,
          	'external_id' => 'n/a',
            'created_at' => now()
		    			)
			    	); 
			}
				return response()->json([
					'ok' => true,
					'txId' => $txid->txId,
					'explorer' => 'https://bscscan.com/tx/'.$txid->txId,
					'from' => $from,
					'amount' => $amount,
					'to' => $to,
					'currency' => 'xBLZD',
					'apikey' => $findoperator->apikey
				], 200);
		}

	} 
 


	public static function getBal($currency, $address) 
{

$curl = curl_init();
if($currency === 'bitcoin') {
		$url = 'https://api-eu1.tatum.io/v3/bitcoin/address/balance/'.$address;
} elseif($currency === 'ethereum') {
		$url = 'https://api-eu1.tatum.io/v3/ethereum/account/balance/'.$address;

}
curl_setopt_array($curl, [
  CURLOPT_URL => $url,
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

if ($err) {
  echo "cURL Error #:" . $err;
} else {
			$url = 'https://api-eu1.tatum.io/v3/'.$currency.'/transaction';

  return $response;
}

}

	public static function getBSCbyTX($hash) 
{

$curl = curl_init();
		$url = 'https://api-eu1.tatum.io/v3/bsc/transaction/'.$hash;

curl_setopt_array($curl, [
  CURLOPT_URL => $url,
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

if ($err) {
  echo "cURL Error #:" . $err;
} else {
		  return $response;
}


	}


	public static function sendBSC($currency, $from, $privkey, $to, $amount) 
{

	  	$payload = "{\"to\":\"".$to."\",\"currency\":\"".$currency."\",\"amount\":\"".$amount."\",\"digits\":18,\"fromPrivateKey\":\"".$privkey."\",\"fee\":{\"gasLimit\":\"72668\",\"gasPrice\":\"5\"}}";

		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/bsc/transaction",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $payload,
		  CURLOPT_HTTPHEADER => [
		    "content-type: application/json",
	"x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}


	public static function sendBEP20Token($currency, $from, $privkey, $to, $amount) 
{
		if($currency === 'xBLZD') {
			//$count = ($amount * 10000000000);
			$amount = $amount;

	  	$payload = "{\"to\":\"".$to."\",\"amount\":\"".$amount."\",\"contractAddress\":\"0x9a946c3cb16c08334b69ae249690c236ebd5583e\",\"digits\":18,\"fromPrivateKey\":\"".$privkey."\",\"fee\":{\"gasLimit\":\"60668\",\"gasPrice\":\"6\"}}";
		}
		if($currency === 'GAME1') {
			//$count = ($amount * 10000000000);
			$amount = $amount;

	  	$payload = "{\"to\":\"".$to."\",\"amount\":\"".$amount."\",\"contractAddress\":\"0x0e52d24c87a5ca4f37e3ee5e16ef5913fb0cceeb\",\"digits\":18,\"fromPrivateKey\":\"".$privkey."\",\"fee\":{\"gasLimit\":\"1055555\",\"gasPrice\":\"8\"}}";
		}
		if($currency === 'BETSHIBA') {
			//$count = ($amount * 10000000000);
			$amount = $amount;

	  	$payload = "{\"to\":\"".$to."\",\"amount\":\"".$amount."\",\"contractAddress\":\"0x24aff9387eabdec994d7a7049e0c1b2bd4120eeb\",\"digits\":18,\"fromPrivateKey\":\"".$privkey."\",\"fee\":{\"gasLimit\":\"60668\",\"gasPrice\":\"6\"}}";
		}

		if($currency === 'GAMBLECOIN') {
			//$count = ($amount * 10000000000);
			$amount = $amount;

	  	$payload = "{\"to\":\"".$to."\",\"amount\":\"".$amount."\",\"contractAddress\":\"0xca9fcc7c876ff16bd97f7b6b14eda9354c296e9a\",\"digits\":18,\"fromPrivateKey\":\"".$privkey."\",\"fee\":{\"gasLimit\":\"60668\",\"gasPrice\":\"6\"}}";
		}


		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/bsc/bep20/transaction",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $payload,
		  CURLOPT_HTTPHEADER => [
		    "content-type: application/json",
	"x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}

	public static function sendTron($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();


		$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"compliant\":false,\"fee\":\"1\",\"fromPrivateKey\":\"".$mnemonic."\",\"paymentId\":\"1234\",\"senderNote\":\"Sender note\"}";

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/tron/transfer",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;

}
}



	public static function sendBitcoincash($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();

		$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"fee\":\"0.0001\",\"compliant\":false,\"mnemonic\":\"".$mnemonic."\",\"xpub\":\"".$xpub."\",\"paymentId\":\"1234\",\"senderNote\":\"Sender note\"}";


curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bcash/transfer",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;

}
}

	public static function estimateBitcoin($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();

		$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"compliant\":false,\"fee\":\"1\",\"mnemonic\":\"".$mnemonic."\",\"xpub\":\"".$xpub."\"}";



curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/blockchain/estimate",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;
}
}

	public static function sendBitcoins($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();
//18Vcjf1yRLrho49VSCiBKSg7nNoJu5aE9j
	
		$estimatefee = self::estimateBitcoin($mnemonic, $xpub, $accountid, $to, $amount);
		$decode = json_decode($estimatefee, true);
		

		$payload = json_encode( array("senderAccountId" => $accountid, "address" => $to, "amount" => $amount, "fee" => $decode['fast'], "compliant" => false, "attr" => 'string', "mnemonic" => $mnemonic,  "xpub" => $xpub, "paymentId" => '234', "senderNote" => 'senderNote'));

		//$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"compliant\":false,\"fee\":\"".number_format(($decode['fast'] * 2), 6, '.', '')."\",\"multipleAmounts\":[\"".$amount."\"],\"attr\":\"string\",\"mnemonic\":\"".$mnemonic."\",\"xpub\":\"".$xpub."\"}";

		Log::notice($payload);
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bitcoin/transfer",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);  

  //CURLOPT_POSTFIELDS => "{\"senderAccountId\":\"5e68c66581f2ee32bc354087\",\"address\":\"mpTwPdF8up9kidgcAStriUPwRdnE9MRAg7\",\"amount\":\"0.001\",\"compliant\":false,\"fee\":\"0.0005\",\"multipleAmounts\":[\"0.1\"],\"attr\":\"string\",\"mnemonic\":\"urge pulp usage sister evidence arrest palm math please chief egg abuse\",\"xpub\":\"xpub6EsCk1uU6cJzqvP9CdsTiJwT2rF748YkPnhv5Qo8q44DG7nn2vbyt48YRsNSUYS44jFCW9gwvD9kLQu9AuqXpTpM1c5hgg9PsuBLdeNncid\",\"paymentId\":\"1234\",\"senderNote\":\"Sender note\"}",


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;

}
}
	public static function sendLitecoin($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();
//18Vcjf1yRLrho49VSCiBKSg7nNoJu5aE9j

		$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"compliant\":false,\"fee\":\"0.0002\",\"mnemonic\":\"".$mnemonic."\",\"xpub\":\"".$xpub."\",\"paymentId\":\"1234\",\"senderNote\":\"Sender note\"}";


curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/litecoin/transfer",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;

}
}

	public static function sendDogecoin($mnemonic, $xpub, $accountid, $to, $amount) 
{
$curl = curl_init();
//18Vcjf1yRLrho49VSCiBKSg7nNoJu5aE9j

		$payload = "{\"senderAccountId\":\"".$accountid."\",\"address\":\"".$to."\",\"amount\":\"".$amount."\",\"compliant\":false,\"fee\":\"1\",\"mnemonic\":\"".$mnemonic."\",\"xpub\":\"".$xpub."\",\"paymentId\":\"1234\",\"senderNote\":\"Sender note\"}";


curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/dogecoin/transfer",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;

}
}



	public static function sendTransfer($currency, $from, $privkey, $to, $amount) 
	{

	if($currency === 'bitcoin') {
		$balance = self::getBal($currency, $from);
		$balance = json_decode($balance);


		$findapikey = DB::table('Wallets')
		->where('wallet', '=', $from)
		->first();

		$findcallback = DB::table('PaymentOptions')
		->where('apikey', '=', $findapikey->apikey)
		->where('crypto', '=', 'btc')
		->first();

		$newindex = DB::table('Wallets')->count() + 20 + 1;
		$contractaddress = 'no';

			$response = self::createTatumAddress($findcallback->tatum_accountid, $newindex, $findapikey->apikey);
			Log::notice($response);
			$response = json_decode($response);

			DB::table('Wallets')->insertGetId(
				array('wallet' => $response->address, 'label' => $findapikey->label.time(), 'apitype' => 'tatum', 'currency' => $currency, 'derivationkey' => $response->derivationKey, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $findapikey->apikey, 'subscribed' => 1, "contractaddress" => $contractaddress, "privatekey" => '0', 'created_at' => now())
			);



		$incoming = $balance->incoming;
		$outgoing = $balance->outgoing;
		$currentbalance = round((($incoming - $outgoing) * 0.99), 7);
		$totalamount = round(($currentbalance - $amount - 0.00011),7);
		$payload = "{\"fromAddress\":[{\"address\":\"".$from."\",\"privateKey\":\"".$privkey."\"}],\"to\":[{\"address\":\"".$to."\",\"value\":".$amount.", \"address\":\"".$response->address."\",\"value\":".$totalamount."}]}";
		Log::notice($totalamount);

	}



	if($currency === 'ethereum') {
		$balance = self::getBal($currency, $from);
		Log::notice('eth bal'.$balance);
		$balance = json_decode($balance);
		$totalamount = ($balance->balance);
		$payload = "{\"data\":\"Note.\",\"to\":\"".$to."\",\"currency\":\"ETH\",\"fee\":{\"gasLimit\":\"38000\",\"gasPrice\":\"65\"},\"amount\":\"".$amount."\",\"fromPrivateKey\":\"".$privkey."\"}";
}

$curl = curl_init();
		$url = 'https://api-eu1.tatum.io/v3/'.$currency.'/transaction';
		
curl_setopt_array($curl, [
  CURLOPT_URL =>  $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
	"x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  return $response;
}

}
	public static function privateKey($currency, $index, $mnemonic) 
		{

		$curl = curl_init();
		$payload = json_encode( array("index" => json_decode($index, true), "mnemonic" => $mnemonic));
		$url = 'https://api-eu1.tatum.io/v3/'.$currency.'/wallet/priv';
		curl_setopt_array($curl, [
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $payload,
		  CURLOPT_HTTPHEADER => [
		    "content-type: application/json",
			    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
		}


	public static function transferBTC($tatumaccount, $xpub, $amount, $mnemonic, $to) 
	{
		// 1MhpswB4rFP1LUjNrMyvWpwSrXGpnYZdii
		// Route::any('payment/sendCrypto/{apikey}/{currency}/{amount}/{from}/{to}/{masterpass}', 'PaymentController@sendCrypto');
		$curl = curl_init();
			$payload = json_encode( array("senderAccountId" => $tatumaccount, "address" => $to, "amount" => $amount, "compliant" => false, "attr" => 'string', "mnemonic" => $mnemonic,  "xpub" => $xpub, "paymentId" => '234', "senderNote" => 'senderNote'));
			Log::warning($payload);
		curl_setopt_array($curl, [
		CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/bitcoin/transfer",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $payload,
		  CURLOPT_HTTPHEADER => [
		    "content-type: application/json",
			    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {


		 	return $response;
		}
		
	}


	public static function createTatumAddress($tatumaccount, $index, $apikey) 
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api-eu1.tatum.io/v3/offchain/account/".$tatumaccount."/address?index=".$index,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => [
		    "x-api-key: 6eb27976-b51e-4141-b0f9-a89b2b6d8c40_100"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}

	 
	/*			TATUM CREATEWALLET           */
										

	public static function createAddress($apikey, $currency, $label) 
	{


		$findoperator = DB::table('Apikeys')
		->where('apikey', '=', $apikey)
		->where('type', '=', 'paykey')
		->first();

		$findcallback = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->where('crypto', '=', $currency)
		->first();

		if(!$findoperator) {
			return response()->json([
				'status' => 'error',
				'reason' => 'AUTHORIZATION ERROR'
			], 401);
		}

		if($currency === 'bsc' || $currency === 'xblzd' || $currency === 'game1' || $currency === 'betshiba' || $currency === 'gamblecoin' || $currency === 'ltc' || $currency === 'btc' || $currency === 'bch'  || $currency === 'doge' || $currency === 'tron' || $currency === 'trx' || $currency === 'eth') {
			$getindex = DB::table('Wallets')->count() + 15 + 1;
			$contractaddress = 'no';

			if($currency === 'game1') {
			$response = self::createxBLZD($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			$contractaddress = '0x0E52d24c87A5ca4F37E3eE5E16EF5913fb0cCEEB';
			}

			if($currency === 'xblzd') {
			$response = self::createxBLZD($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			$contractaddress = '0x9a946c3cb16c08334b69ae249690c236ebd5583e';
			}

			if($currency === 'gamblecoin') {
			$response = self::createGAMBLECOIN($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			$contractaddress = '0xca9fcc7c876ff16bd97f7b6b14eda9354c296e9a';
			}

			if($currency === 'betshiba') {
			$response = self::createBETSHIBA($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			$contractaddress = '0x41e44D4E1E1Ca445Bf54DCC1AC03b884ce4dD09E';
			}
			if($currency === 'bsc') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'btc') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'eth') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'ltc') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'tron' || $currency === 'trx') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'doge') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}

			if($currency === 'bch') {
			$response = self::createTatumAddress($findcallback->tatum_accountid, $getindex, $apikey);
			$response = json_decode($response);
			}
			
			Log::info(json_encode($response));

			DB::table('Wallets')->insertGetId(
				array('wallet' => $response->address, 'label' => $label, 'apitype' => 'tatum', 'currency' => $currency, 'derivationkey' => $response->derivationKey, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'subscribed' => 1, "contractaddress" => $contractaddress, "privatekey" => '0', 'created_at' => now())
			);

			return response()->json([
					'status' => 'ok',
					'apikey' => $apikey,
					'address' => $response->address,
					'derivationkey' => $response->derivationKey,
					'currency' => $currency,
					'label' => $label,
					'contractaddress' => $contractaddress,
					'subscribed' => 'yes'
			]);
		
		} else {
				return response()->json([
					'status' => 'error',
					'reason' => 'Currency not found'
				], 404);
			}
	}

}

