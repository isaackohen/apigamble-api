<?php

namespace App\Http\Controllers;
use \FurqanSiddiqui\BIP39\BIP39;

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

class PaymentSetupController extends Controller
{


       public function setupSubscription($type, $account) {

        $curl = curl_init();

          if($type === 'ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION') {
          $url = 'https://apigamble.com/api/callback/tatumWebhook';
          }

          if($type === 'ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION') {
          $url = 'https://apigamble.com/api/callback/tatumWebhookPending';
          }   

          if($type === 'TRANSACTION_IN_THE_BLOCK') {
          $url = 'https://apigamble.com/api/callback/tatumWebhookWithdrawal';
          }


      $payload = "{\"type\":\"".$type."\",\"attr\":{\"id\":\"".$account."\",\"url\":\"".$url."\"}}";


        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api-eu1.tatum.io/v3/subscription",
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


       public function setupTatumId($currency, $xpub) {

              $payload = "{\"currency\":\"".$currency."\",\"xpub\":\"".$xpub."\"}";



        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api-eu1.tatum.io/v3/ledger/account",
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

    public function setupCurl($currency) {
        $mnemonic = BIP39::Generate(12);
        $words = $mnemonic->words;
        $wordstogether = $words[0].'_'.$words[1].'_'.$words[2].'_'.$words[3].'_'.$words[4].'_'.$words[5].'_'.$words[6].'_'.$words[7].'_'.$words[8].'_'.$words[9].'_'.$words[10].'_'.$words[11];

          $curl = curl_init();

          curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-eu1.tatum.io/v3/".$currency."/wallet?mnemonic=".$wordstogether,
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

    public function setupBtc($apikey) {



        foreach (PaymentOptions::where('mnemonic', '=', null)->where('apikey', '=', $apikey)->get() as $transaction) {
                
                if($transaction->crypto === 'btc') {
                 $response = self::setupCurl('bitcoin');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('BTC', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'btc')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'eth') {
                 $response = self::setupCurl('ethereum');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('ETH', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'eth')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'ltc') {
                 $response = self::setupCurl('litecoin');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('LTC', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'ltc')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'bsc') {
                 $response = self::setupCurl('bsc');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('BSC', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);



                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bsc')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'xblzd') {
                $getbsc = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bsc')->first();

                $responseTatumId = self::setupTatumId('xBLZD', $getbsc->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'xblzd')->update(['mnemonic' => $getbsc->mnemonic, 'xpub' => $getbsc->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }


                if($transaction->crypto === 'gamblecoin') {
                $getbsc = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bsc')->first();

                $responseTatumId = self::setupTatumId('GAMBLECOIN', $getbsc->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'gamblecoin')->update(['mnemonic' => $getbsc->mnemonic, 'xpub' => $getbsc->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }


                if($transaction->crypto === 'betshiba') {
                $getbsc = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bsc')->first();

                $responseTatumId = self::setupTatumId('BETSHIBA', $getbsc->xpub);
                Log::notice($responseTatumId);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'betshiba')->update(['mnemonic' => $getbsc->mnemonic, 'xpub' => $getbsc->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
/*
             if($transaction->crypto === 'game1') {
                $getbsc = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bsc')->get();

                $responseTatumId = self::setupTatumId('GAME1', $getbsc->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'game1')->update(['mnemonic' => $getbsc->mnemonic, 'xpub' => $getbsc->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
*/


                if($transaction->crypto === 'trx') {
                 $response = self::setupCurl('tron');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('TRON', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'trx')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'bch') {
                 $response = self::setupCurl('bcash');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('BCH', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'bch')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

                if($transaction->crypto === 'doge') {
                 $response = self::setupCurl('dogecoin');
                 $response = json_decode($response);

                if($response->mnemonic) {
                $responseTatumId = self::setupTatumId('DOGE', $response->xpub);
                $responseTatumId = json_decode($responseTatumId);

                $subscribe1 = self::setupSubscription('ACCOUNT_INCOMING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe2 = self::setupSubscription('ACCOUNT_PENDING_BLOCKCHAIN_TRANSACTION', $responseTatumId->id);
                $subscribe3 = self::setupSubscription('TRANSACTION_IN_THE_BLOCK', $responseTatumId->id);

                echo $subscribe1;

                if($subscribe1) {
                  $update = PaymentOptions::where('apikey', $apikey)->where('crypto', 'doge')->update(['mnemonic' => $response->mnemonic, 'xpub' => $response->xpub, 'tatum_accountid' => $responseTatumId->id]);
                 }
                }
                }

         }


    }

}
