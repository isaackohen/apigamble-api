<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

use App\withdrawAndDeposit;

class RelayController extends Controller
{


				public function withdrawAndDeposit($operator, $user, $withdraw, $deposit, $currency, $gameid, $txid)
				   {
		    	$combined = $operator.$user.$withdraw.$deposit.$currency.$gameid;
		    	
					$transaction = DB::table('withdrawAndDeposit')->insertGetId(array(
			            	'operator' => $operator,
			            	'user' => $user,
			            	'withdraw' => $withdraw,
			            	'deposit' => $deposit,

			            	'currency' => $currency,
			            	'gameid' => $gameid,
			            	'txid' => $txid,

			                'created_at' => now()
		    			)
			    	); 

			return response()->json([
					'status' => 'ok'
			], 200);
				}


		        public function getBalancePayload($playerid, $game, $sessionAlternativeId, $id, $callbackurl)
		        {

		        # Define function endpoint
		        $url = $callbackurl;
		        $ch = curl_init($url);


		        # Setup request to send json via POST. This is where all parameters should be entered.
		        $payload = json_encode(array("jsonrpc" => '2.0', "method" => 'getBalance', "params" => array('callerId' => 742, 'playerName' => $playerid,'currency' => 'USD', 'gameId' => $game, 'sessionAlternativeId' => $sessionAlternativeId), 'id' => $id));
		        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		        curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
		        curl_setopt( $ch,   CURLOPT_MAXREDIRS, 10);

		        # Return response instead of printing.
		        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );

		        # Send request.
		        $result = curl_exec($ch);
		        curl_close($ch);

		        # Decode the received JSON string
		        $resultdecoded = json_decode($result, true);
		        # Print status of request (should be true if it worked)
		       	Log::warning($payload);
		        Log::warning($result);
		        Log::warning($url);

		        
		        return $result;

        }


        public function getBalanceTest(Request $request)
        {

                $content = json_decode($request->getContent());

                $getBalancePayload = self::getBalancePayload($content->params->playerName, $content->params->gameId, $content->params->sessionAlternativeId, $content->id, 'https://mammothdev.cazinodev.com/api/d7LvQTDc7ZHUNmHFd7LvQTDc7ZHUNmHF');
                return $getBalancePayload;
        }



	public function c27endpoint(Request $request) 
	{

		    Log::warning($request);
		    Log::warning($request->id);

			$url = 'http://51.89.32.235/api/d7LvQTDc7ZHUNmHFd7LvQTDc7ZHUNmHF';
			$game = 'starburst_touch';
			$userId = '60ebdc64d6ab9b08ae2e0902-xblzd';
			$session = time().'_60ebdc64d6ab9b08ae2e0902_xblzd';
			$getanswer = self::getBalancePayload($userId, $game, $session, $request->id, $url);

	        Log::warning($getanswer);
	      	header('Access-Control-Allow-Origin: *');
			header('Content-type: application/json');
			return Response::json($getanswer);


/*
            return response()->json([
                'jsonrpc' => "2.0",
                'result' => ([
                'balance' =>  '44',
            ]),
                'id' => '22'
            ])->header('Content-Type', 'application/json/plain');
            */

	}


}

