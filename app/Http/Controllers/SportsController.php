<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Response;

use App\Sessions;
use App\Apikeys;
use App\Games;
use App\Players;
use App\Sports;

class SportsController extends Controller
{

		    public function getBwinInplay($sport_id)
		    {
		    	if($sport_id === 'all') { $url = 'https://api.b365api.com/v1/bwin/inplay?token=91157-8Gl4g5nOw9IqPB'; }
		    	else { $url = 'https://api.b365api.com/v1/bwin/inplay?token=91157-8Gl4g5nOw9IqPB&sport_id='.$sport_id; }
		    	$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				if ($data === false) {
				    $info = curl_getinfo($ch);
				    curl_close($ch);
				    die('Error occured during curl exec. Add. info: ' . var_export($info));
				}
				curl_close($ch); 
				return $data;
		    }

		    public function getBwinPrematch($sport_id, $skipmarkets)
		    {
		    	if($sport_id === 'all') { $url = 'https://api.b365api.com/v1/bwin/prematch?token=91157-8Gl4g5nOw9IqPB'; }
		    	else { $url = 'https://api.b365api.com/v1/bwin/prematch?token=91157-8Gl4g5nOw9IqPB&sport_id='.$sport_id; }
		    	$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				if ($data === false) {
				    $info = curl_getinfo($ch);
				    curl_close($ch);
				    die('Error occured during curl exec. Add. info: ' . var_export($info));
				}
				curl_close($ch); 
				return $data;
		    }

		    public function getBwinEvent($event_id)
		    {
		    	$url = 'https://api.b365api.com/v1/bwin/event?token=91157-8Gl4g5nOw9IqPB&event_id='.$event_id.'/';
		    	$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				if ($data === false) {
				    $info = curl_getinfo($ch);
				    curl_close($ch);
				    die('Error occured during curl exec. Add. info: ' . var_export($info));
				}
				curl_close($ch); 
				return $data;
		    } 

		    public function getBwinResult($event_id)
		    {
		    	$url = 'https://api.b365api.com/v1/bwin/result?token=91157-8Gl4g5nOw9IqPB&event_id='.$event_id.'/';
		    	$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				if ($data === false) {
				    $info = curl_getinfo($ch);
				    curl_close($ch);
				    die('Error occured during curl exec. Add. info: ' . var_export($info));
				}
				curl_close($ch); 
				return $data;
		    } 


		    public function getInplay($apikey, $sport_id)
		    {

		    	$findapikey = DB::table('Apikeys')
                ->where('apikey', '=', $apikey)
                ->where('type', '=', 'sports')
                ->first();  
				
				$findoperator = DB::table('Apikeys')
                ->where('apikey', '=', $findapikey->apikey)
                ->first();


                if(!$findoperator) {
	        	return response("AUTHORIZATION ERROR", 401);
                }


            	$getInplay = self::getBwinInplay($sport_id);

            	return response()->json(json_decode($getInplay));
			}



		    public function getGameResult($apikey, $event_id)
		    {

				$findapikey = DB::table('Apikeys')
                ->where('apikey', '=', $apikey)
                ->where('type', '=', 'sports')
                ->first();  
				
				$findoperator = DB::table('Apikeys')
                ->where('apikey', '=', $findapikey->apikey)
                ->first();  

                if(!$findoperator) {
	        	return response("AUTHORIZATION ERROR", 401);
                }


            	$getEvent = self::getBwinResult($event_id);

            	return response()->json(json_decode($getEvent));
			}


		    public function getGameEvent($apikey, $event_id)
		    {

				$findapikey = DB::table('Apikeys')
                ->where('apikey', '=', $apikey)
                ->where('type', '=', 'sports')
                ->first();  
				
				$findoperator = DB::table('Apikeys')
                ->where('apikey', '=', $findapikey->apikey)
                ->first();  

                if(!$findoperator) {
	        	return response("AUTHORIZATION ERROR", 401);
                }


            	$getEvent = self::getBwinEvent($event_id);

            	return response()->json(json_decode($getEvent));
			}


		    public function getPrematchOdds($apikey, $sport_id)
		    {
				$findapikey = DB::table('Apikeys')
                ->where('apikey', '=', $apikey)
                ->where('type', '=', 'sports')
                ->first();  
				
				$findoperator = DB::table('Apikeys')
                ->where('apikey', '=', $findapikey->apikey)
                ->first(); 

		    	$findsport = DB::table('Sports_prematch_odds')
                ->where('id', '=', $sport_id)
                ->first();  

                if(!$findoperator) {
	        	return response("AUTHORIZATION ERROR", 401);
                }

                if($sport_id === 'all') {
            	$prematchOdds = self::getBwinPrematch($sport_id, 'all');

                }	else {

                if(!$findsport) {
	        	return response("SPORTSGAME NOT FOUND", 404);
                }

            	$prematchOdds = self::getBwinPrematch($sport_id, $findsport->sports_id);
				}
            	
            	return response()->json(json_decode($prematchOdds));
			}
	}

