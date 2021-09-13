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
use App\CallbackQueue;

class PaymentController extends Controller
{


    private array $allowedCoins = ['btc', 'bch', 'bsv', 'btg', 'eth', 'dash', 'ltc', 'xrp', 'zec', 'rmg', 'erc', 'omg', 'zrx', 'fun', 'gnt', 'rep', 'bat', 'knc', 'cvc', 'eos', 'qrl', 'nmr', 'pay', 'brd', 'trx', 'tron', 'tbtc', 'tbch', 'tbsv', 'teth', 'tdash', 'tltc', 'txrp', 'tzec', 'trmg', 'terc'];

	/*  
	@@	Generalized BitGo SDK Functions
	*/	
	 
	public function getSDKbtc($walletId) {
		$bitgo = $this->allowCoins('BitGoSDK', env('BITGO_BTC_ACCESSKEY'), CurrencyCode::BITCOIN, false);
		$bitgo->unlockSession('000000');
		$bitgo->accessToken = env('BITGO_BTC_ACCESSKEY');
		$bitgo->walletId = $walletId;
		return $bitgo;
	}

	public function getSDKltc($walletId) {
		$bitgo = $this->allowCoins('BitGoSDK', env('BITGO_LTC_ACCESSKEY'), CurrencyCode::LITECOIN, false);
		$bitgo->unlockSession('000000');
		$bitgo->accessToken = env('BITGO_LTC_ACCESSKEY');
		$bitgo->walletId = $walletId;
		return $bitgo;
	}

	public function getSDKbch($walletId) {
		$bitgo = $this->allowCoins('BitGoSDK', env('BITGO_BCH_ACCESSKEY'), CurrencyCode::BITCOINCASH, false);
		$bitgo->unlockSession('000000');
		$bitgo->accessToken = env('BITGO_BCH_ACCESSKEY');
		$bitgo->walletId = $walletId;
		return $bitgo;
	}

	private function allowCoins(string $instance, $o1, $o2, $o3) {
		$ref = new ReflectionClass('neto737\\BitGoSDK\\'.$instance);
		$o = $ref->newInstanceWithoutConstructor();

		$property = $ref->getProperty('allowedCoins');
		$property->setAccessible(true);
		$property->setValue($o, $this->allowedCoins);

		$o->__construct($o1, $o2, $o3);
		return $o;
	}

        public function updateBalance($currency, $address) {

        if ($currency === 'trx') {
            self::getBalancetrx($address);
        } elseif ($currency === 'eth') {
            self::getBalanceeth($address);
        } elseif ($currency === 'bsc') {
            self::getBalancebsc($address);
        }
    }



    public function sendBscTokenFunction($currency, $contractaddress, $from, $to, $amount) {
        $payload = json_encode(array(
            "apikey"      => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb",
            "password" => "aLgKfdaQHw2GTYA8",
            "contractaddress" => $contractaddress,
            "from" => $from,
            "to" => $to,
            "amount" => $amount));

        $churl = 'https://eu.bsc.chaingateway.io/v1/sendToken';

        $chpost = curl_init($churl);

        # Setup request to send json via POST. This is where all parameters should be entered.
        curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

        # Return response instead of printing.
        curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

        # Send request.
        $result = curl_exec($chpost);
        curl_close($chpost);

		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)

		if($resultdecoded["ok"] === true) {
		$dollaramount = round(($amount * self::rateDollarXblzd()), 2);
		$getwallet = Wallets::where('wallet', $from)->where('contractaddress', $contractaddress)->first();
					
		Wallets::where('wallet', $from)->where('currency', $currency)->update(['balance' => round($getwallet->tokenbalance - $amount, 7)]);

		$transaction = DB::table('PaymentTransactions')->insertGetId(array(
        	'id' => DB::table('PaymentTransactions')->count() + 1,
        	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $getwallet->apikey,
            'callbackurl' => $getwallet->callbackurl, 
         	'amountusd' => $dollaramount,
            'currency' => $currency,
            'txid' => $resultdecoded["txid"],
        	'external_id' => $resultdecoded["txid"],
            'subscribed' => '0',
            'created_at' => now()
		));
		} 

		return $resultdecoded;

		}



    public function sendCryptoFunction($currency, $from, $to, $amount) {

        if($currency === 'bsc') {
        	$url = 'https://eu.bsc.chaingateway.io/v1/sendBinancecoin';
        } elseif($currency === 'eth') {
        	$url = 'https://eu.eth.chaingateway.io/v1/sendEthereum';
        }

        if($currency === 'trx') {
             $url = 'https://eu.trx.chaingateway.io/v1/sendTron';
       		$getprivkey = Wallets::where('wallet', $from)->where('currency', 'trx')->first();
       		$privatekey = $getprivkey->privatekey; 
       		$payload = json_encode(array(
            "apikey"      => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb",
            "password" => "aLgKfdaQHw2GTYA8",
            "privatekey" => $privatekey,
            "to" => $to,
            "amount" => $amount));

       		Log::notice($amount);
       	} else {
    	$payload = json_encode(array(
        "apikey"      => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb",
        "password" => "aLgKfdaQHw2GTYA8",
        "from" => $from,
        "to" => $to,
        "amount" => $amount));
		}

        $churl = $url;

        $chpost = curl_init($churl);

        # Setup request to send json via POST. This is where all parameters should be entered.
        curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

        # Return response instead of printing.
        curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

        # Send request.
        $result = curl_exec($chpost);
        curl_close($chpost);

		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)

		if($resultdecoded["ok"] === true) {
		$dollaramount = round(($amount * self::rateDollarXblzd()), 2);
		$getwallet = Wallets::where('wallet', $from)->where('currency', $currency)->first();
					
		Wallets::where('wallet', $from)->where('currency', $currency)->update(['balance' => round($getwallet->balance - $amount, 7)]);

		$transaction = DB::table('PaymentTransactions')->insertGetId(array(
        	'id' => DB::table('PaymentTransactions')->count() + 1,
        	'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'apikey' => $getwallet->apikey,
            'callbackurl' => $getwallet->callbackurl, 
         	'amountusd' => $dollaramount,
            'currency' => $currency,
            'txid' => $resultdecoded["txid"],
        	'external_id' => $resultdecoded["txid"],
            'subscribed' => '0',
            'created_at' => now()
		));
		} 

		return $result;

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
		 
		if($currency === 'bsc' || $currency === 'eth' || $currency === 'trx') {
			self::updateBalance($currency, $from);

			if($amount > $checkwallet->balance) {
			return response()->json([
					'status' => 'error',
					'balance' => $checkwallet->balance,
					'reason' => 'Insufficient balance. Call for getBalance to refresh the balance if you believe this is incorrect.'
			], 301);

			} else {
				$sendcurrency = self::sendCryptoFunction($currency, $from, $to, $amount);

				return response()->json($sendcurrency);
			}
		}
	}


	public static function sendToken($apikey, $currency, $amount, $from, $to, $masterpass) 
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
		 
		if($currency === 'betshiba') {
			if($amount > $checkwallet->tokenbalance) {

			return response()->json([
					'status' => 'error',
					'tokenbalance' => $checkwallet->tokenbalance,
					'reason' => 'Insufficient token balance. Call for getBalance to refresh the balance if you believe this is incorrect.'
			], 301);

			} else {
				$contractaddress = '0x41e44d4e1e1ca445bf54dcc1ac03b884ce4dd09e';
				$sendxblzdtoken = self::sendBscTokenFunction($currency, $contractaddress, $from, $to, $amount);

				return response()->json($sendxblzdtoken);
			}
		}
	}

	/*  @@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ 
					USD PRICES           
	/*	@@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ */

	public static function apiRates()
	{
	   $result = Cache::remember('apirates', 180, function () {
	   return json_decode(file_get_contents('https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,ETH,BNB,USDT,ETH,BCH,LTC,DOGE,XMR,TRX&tsyms=USD'));
	   });
	   return $result;
	}

	public static function rateDollarLtc() {
		$value = self::apiRates();
		$price = $value->LTC->USD;
		return $price;
	}

	public static function rateDollarXblzd() {
        try {
            if (!Cache::has('conversions: xblzd'))
                Cache::put('conversions: xblzd', file_get_contents("https://api.coingecko.com/api/v3/coins/blizzard?localization=false&market_data=true"), now()->addHours(1));
            $json = json_decode(Cache::get('conversions: xblzd'));
            return $json->market_data->current_price->usd;
        } catch (\Exception $e) {
            return -1;
        }
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

	/*  @@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ 
				 LITECOIN FUNCTIONS           
	/*	@@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ */

	public function getClientLTC() {
		$bitgo = $this->allowCoins('BitGoExpress', 'localhost', 3080, CurrencyCode::LITECOIN);
		$bitgo->walletId = env('BITGO_LTC_WALLET');
		$bitgo->accessToken = env('BITGO_LTC_ACCESSKEY');
		return $bitgo;
	}

	public function bitgoWebhookSDKltc($walletId = null) {
		$bitgo = $this->allowCoins('BitGoSDK', env('BITGO_LTC_ACCESSKEY'), CurrencyCode::LITECOIN, false);
		$bitgo->unlockSession('000000');
		$bitgo->accessToken = env('BITGO_LTC_ACCESSKEY');
		$bitgo->walletId = $walletId;
		return $bitgo;
	}

	public function bitgoWebhookltc() {
		$sdk = $this->bitgoWebhookSDKltc();
		$payload = $sdk->getWebhookPayload();
		$result = $this->processltc();
		return Response::json(is_array($result) ? $result : []);
	} 

	public function sendLTC(string $from, string $to, float $sum) {
		$sendTransaction = $this->getClientLTC()->sendTransaction($to, BitGoSDK::toSatoshi($sum), env('BITGO_LTC_MASTERPASSWORD'));
		return $sendTransaction;
	}
 
	public function processltc(string $wallet = null) {
		$sdk = $this->bitgoWebhookSDKltc();
		$payload = $sdk->getWebhookPayload();
		$txDetails = $this->bitgoWebhookSDKltc($payload['wallet'])->getWalletTransaction($payload['hash']);

		if (isset($txDetails['error'])) return;

			$to = null;
			$value = null;

			foreach ($txDetails['outputs'] as $output) {
				if(isset($output['wallet'])) {
					$to = $output['address'];
					$value = BitGoSDK::toBTC($output['value']);
					break;
				}
			}

			$from = $txDetails['inputs']['0']['address'];
			if(!$to) return;
			$this->acceptBitGoJSLTC($txDetails['confirmations'], $to, $from, $txDetails['blockHash'], $value);
	}
 
	protected function acceptBitGoJSLTC(int $confirmations, string $to, $from, string $txid, float $sum) {
		$checkwallet = Wallets::where('wallet', $to)->first();

		if($checkwallet == null) return false;
		if($confirmations == '0') return false;

		$transaction = PaymentTransactions::where('txid', $txid)->first();

		if($transaction == null) {
			$currency = 'ltc';

			$transaction = DB::table('PaymentTransactions')->insertGetId(array(
				'id' => DB::table('PaymentTransactions')->count() + 1,
				'from' => $from,
				'to' => $to,
				'amount' => $sum,
				'apikey' => $checkwallet->apikey,
				'callbackurl' => $checkwallet->callbackurl,
				'amountusd' => round(($sum * self::rateDollarLtc()), 2),
				'currency' => 'ltc',
				'txid' => $txid,
				'subscribed' => '1',
				'created_at' => now()
			)
		);
		}
		return true;
	}

	/*  @@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ 
				 CREATEWALLET FUNCTIONS           
	/*	@@ @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ @@ */

	function createChaingatewayAddress($currency) {
		$chaingatenewaddress = curl_init();
		  curl_setopt_array($chaingatenewaddress, array(
		  CURLOPT_URL => 'https://eu.'.$currency.'.chaingateway.io/v1/newAddress',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_POSTFIELDS => '{"password": "aLgKfdaQHw2GTYA8"}',
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb'
		  ),
		));

		$chaingateresponse = curl_exec($chaingatenewaddress);
		curl_close($chaingatenewaddress);
		return $chaingateresponse;
	}


	public function getBalancebsc($address) {
		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", "binancecoinaddress" => $address) );
		$churl = 'https://eu.bsc.chaingateway.io/v1/getBinancecoinBalance';
		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)
		if($resultdecoded["ok"] === true) {

		$getwallet = Wallets::where('wallet', $address)->first();

		if($getwallet) {
			$getbsc = $getwallet->balance;
		    Wallets::where('wallet', $address)->update(['balance' => round($resultdecoded['balance'], 7), 'updated_at' => now()]);
			}
			return response()->json(['status' => 'ok', 'balance' => $resultdecoded["balance"]]);
		} else {
			return response()->json(['status' => 'error'], 300);
		}
	}


	public function getBalancetrx($address) {
		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", "tronaddress" => $address));
		$churl = 'https://eu.trx.chaingateway.io/v1/getTronBalance';
		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)
		if($resultdecoded["ok"] === true) {

		$getwallet = Wallets::where('wallet', $address)->first();

		if($getwallet) {
			$getbsc = $getwallet->balance;
		    Wallets::where('wallet', $address)->where('currency', 'trx')->update(['balance' => round($resultdecoded['balance'], 7), 'updated_at' => now()]);
			}
			return response()->json(['status' => 'ok', 'balance' => $resultdecoded["balance"]]);
		} else {
			return response()->json(['status' => 'error'], 300);
		}
	}



	public function getBalanceeth($address) {
		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", "ethereumaddress" => $address));
		$churl = 'https://eu.eth.chaingateway.io/v1/getEthereumBalance';
		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)
		if($resultdecoded["ok"] === true) {

		$getwallet = Wallets::where('wallet', $address)->first();

		if($getwallet) {
			$getbsc = $getwallet->balance;
		    Wallets::where('wallet', $address)->where('currency', 'eth')->update(['balance' => round($resultdecoded['balance'], 7), 'updated_at' => now()]);
			}
			return response()->json(['status' => 'ok', 'balance' => $resultdecoded["balance"]]);
		} else {
			return response()->json(['status' => 'error'], 300);
		}
	}



	public function getBalanceTokenbsc($currency, $address) {
		if($currency === 'betshiba') {
		$tokencontract = '0x41e44d4e1e1ca445bf54dcc1ac03b884ce4dd09e';

		} else {
			return response()->json(['status' => 'error', 'reason' => 'Token not added on API'], 300);
		}

		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", "binancecoinaddress" => $address, "contractaddress" => $tokencontract) );
		$churl = 'https://eu.bsc.chaingateway.io/v1/getTokenBalance';
		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);

		# Print status of request (should be true if it worked)
		if($resultdecoded["ok"] === true) {
		try {
		self::getBalancebsc($address);
		$getwallet = Wallets::where('wallet', $address)->first();

		if($getwallet) {
			$getbsc = $getwallet->balance;
		    Wallets::where('wallet', $address)->where('currency', $currency)->update(['tokenbalance' => round($resultdecoded['balance'], 7), 'updated_at' => now()]);

			return response()->json(['status' => 'ok', 'tokenbalance' => $resultdecoded['balance'], 'bscbalance' => $getbsc]);
			} else {
				return response()->json(['status' => 'ok', 'tokenbalance' => $resultdecoded['balance']]);
			}

			} catch (\Exception $e) {
				return response()->json(['status' => 'ok', 'tokenbalance' => $resultdecoded['balance']]);
	        }

		} else {
			return response()->json(['status' => 'error'], 300);
		}
	}


	function startSubscribe($currency, $address, $currencyaddress, $callbackurl) {
		$envcallbackurl = env('CHAINGATE_CALLBACK_URL');
		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", $currencyaddress => $address, "url" => $envcallbackurl) );
		$churl = 'https://eu.'.$currency.'.chaingateway.io/v1/subscribeAddress';
		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)
		return $resultdecoded["ok"];
	}

	function startTokenSubscribe($currency, $address, $currencyaddress, $contractaddress, $callbackurl) {
		$envcallbackurl = env('CHAINGATE_CALLBACK_URL');
		$payload = json_encode(array("apikey" => "51eefdbc3ea71ec28518d88aaf00070d5635f9bb", $currencyaddress => $address, "contractaddress" => $contractaddress, "url" => $envcallbackurl) );
		$churl = 'https://eu.'.$currency.'.chaingateway.io/v1/subscribeAddress';

		try {
			$startsub = self::startSubscribe('bsc', $address, $currencyaddress, $envcallbackurl);
		} catch (\Exception $e) {
            return -1;
        }

		$chpost = curl_init($churl);

		# Setup request to send json via POST. This is where all parameters should be entered.
		curl_setopt( $chpost, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $chpost, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: 51eefdbc3ea71ec28518d88aaf00070d5635f9bb"));

		# Return response instead of printing.
		curl_setopt( $chpost, CURLOPT_RETURNTRANSFER, true );

		# Send request.
		$result = curl_exec($chpost);
		curl_close($chpost);

		# Decode the received JSON string
		$resultdecoded = json_decode($result, true);
		# Print status of request (should be true if it worked)
		return $resultdecoded["ok"];
	}


	/*  
	@		CHAINGATEWAY CREATEWALLET           
	/*										*/

	public function createWalletAddress($apikey, $currency, $label) 
	{

		if (strlen($apikey) > 24 || strlen($currency) > 24 || strlen($label) > 50) {
			return redirect('/');
		}
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

		if($currency === 'btc' || $currency === 'ltc' || $currency === 'bch') {
			if($currency === 'ltc') {
				$sdk = $this->getSDKltc(env('BITGO_LTC_WALLET'));
			}
			if($currency === 'btc') {
				$sdk = $this->getSDKbtc(env('BITGO_BTC_WALLET'));
			}
			if($currency === 'bch') {
				$sdk = $this->getSDKbch(env('BITGO_BCH_WALLET'));
			}

			$response = $sdk->createWalletAddress(0, $apikey.'-'.$label);
			$webhooks = $sdk->listWalletWebhooks();
			if(!isset($webhooks['webhooks']) || count($webhooks['webhooks']) == 0) $sdk->addWalletWebhook(secure_url('/callback/bitgoWebhook'.$currency), 'transfer', 0);
			DB::table('Wallets')->insertGetId(
				array('wallet' => $response['address'], 'label' => $label, 'currency' => $currency, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'subscribed' => 1, "contractaddress" => "no", "privatekey" => '0', 'created_at' => now())
			);

			return response()->json([
					'status' => 'ok',
					'address' => $response['address'],
					'currency' => $currency,
					'label' => $label,
					'subscribed' => 'yes'
			]);
		
		} elseif($currency === 'eth' || $currency === 'trx' || $currency === 'bsc') {

			$requestchaingateway = self::createChaingatewayAddress($currency);
			$subscribed = '0';

			if($currency === 'eth') {
				$getaddress = json_decode($requestchaingateway);
				$address = $getaddress->ethereumaddress;

					DB::table('Wallets')->insertGetId(
						array('wallet' => $address, 'label' => $label, 'currency' => $currency, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'callbackurl' => $findoperator->callbackurl, 'subscribed' => 0, "contractaddress" => "no", "privatekey" => '0', 'created_at' => now())
					);

				$currencyaddress = 'ethereumaddress';
			}

			if($currency === 'trx') {
				$getaddress = json_decode($requestchaingateway);

				DB::table('Wallets')->insertGetId(
					array('wallet' => $getaddress->address, 'label' => $label, 'currency' => $currency, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'callbackurl' => $findoperator->callbackurl, 'subscribed' => 0, "contractaddress" => "no", "privatekey" => $getaddress->privatekey, 'created_at' => now())
				);
				$address = $getaddress->address;
				$currencyaddress = 'tronaddress';
			}

			if($currency === 'bsc') {
				$getaddress = json_decode($requestchaingateway);
				$address = $getaddress->binancecoinaddress;
				$currencyaddress = 'binancecoinaddress';

				DB::table('Wallets')->insertGetId(
					array('wallet' => $address, 'label' => $label, 'currency' => $currency, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'callbackurl' => $findoperator->callbackurl, 'subscribed' => 0, "contractaddress" => "no", "privatekey" => '0', 'created_at' => now())
				);
			}

			if(self::startSubscribe($currency, $address, $currencyaddress, $findoperator->callbackurl) == true) {
				Wallets::where('wallet', $address)->where('apikey', $apikey)->update(['subscribed' => '1']);
				$subscribed = '1';
			}

			return response()->json([
					'status' => 'ok',
					'address' => $address,
					'currency' => $currency,
					'label' => $label,
					'subscribed' => $subscribed,
			]);

		} else {
				return response()->json([
					'status' => 'error',
					'reason' => 'Currency not found'
				], 404);
			}
	}



	public function createTokenAddress($apikey, $currency, $token, $label) 
	{ 
		$callbackurl = env('CHAINGATE_TOKENCALLBACK_URL');

		$findoperator = DB::table('Apikeys')
		->where('apikey', '=', $apikey)
		->where('type', '=', 'paykey')
		->first();

		$findcallback = DB::table('PaymentOptions')
		->where('apikey', '=', $apikey)
		->first();


		if(!$findoperator) {
			return response()->json([
				'status' => 'error',
				'reason' => 'AUTHORIZATION ERROR'
			], 401);
		}

		if($token === 'betshiba') {
			$requestchaingateway = self::createChaingatewayAddress('bsc');
			$currencyaddress = 'binancecoinaddress';
						Log::notice($requestchaingateway);

			$getaddress = json_decode($requestchaingateway);
			$tokencontract = '0x41e44d4e1e1ca445bf54dcc1ac03b884ce4dd09e';
			$address = $getaddress->$currencyaddress;
			DB::table('Wallets')->insertGetId(
				array('wallet' => $getaddress->$currencyaddress, 'label' => $label, 'currency' => $token, 'balance' => "0", 'tokenbalance' => "0", 'apikey' => $apikey, 'subscribed' => 0, "contractaddress" => $tokencontract, 'created_at' => now())
			);
			$subscribed = '0';
			$tokensubscribe = self::startTokenSubscribe('bsc', $address, 'binancecoinaddress', $tokencontract, $callbackurl);

			if($tokensubscribe == true) {
				Wallets::where('wallet', $address)->where('apikey', $apikey)->update(['subscribed' => '1']);
				$subscribed = '1';
			}

			return response()->json([
					'status' => 'ok',
					'address' => $address, 
					'token' => $token,
					'tokenchain' => $currency,
					'tokencontract' => $tokencontract,
					'label' => $label,
					'subscribed' => $subscribed
			]);

		} else {
			return response()->json([
				'status' => 'error',
				'reason' => 'Token not authorized'
			], 401);
		}
	}
}

