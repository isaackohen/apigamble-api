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
use App\Evoplay;
use App\Players;

class SessionController extends Controller
{
		    private $system_id = '1104';
		    private $secret_key = '4172e079f58e4f78a89bfb5f6b88f6d9';
		    private $new_system_id = '1103';
		    private $new_secret_key = 'f9e9fa1971f76ec4bae4a0d6cb3d844e';
		    private $version = '1';
		    private $currency = 'USD';
	
		    public function getCallbackLink(Request $request)
		    {


		    	$findcallback = DB::table('GameOptions')
                ->get();

                if($findcallback) {
				    return $findcallback;

	        	
                } else {
                		return response()->json([
							'status' => "error"
		           		], 400);	

                }
		    }



    public function livecasinoUrl($game, $operatoruser, $playerId)
    {
            $livecasino_apikey = '=MjZhdjZ2EGM1UTNmJGNmhTY5MDN3UmMiRjN5Q2YzI2M6cTOwkDN1QTN';

            $id = $playerId;
            $idreplace = preg_replace("/[^0-9]/", "", $id );
            $user = $playerId.'-'.$operatoruser;
            $userdata = array('userId' => substr($idreplace,0,10), 'username' => $user, 'nick' => $playerId, 'currency' => "USD");
            $jsonbody = json_encode($userdata);
            $curlcatalog = curl_init();

            curl_setopt_array($curlcatalog, array(
            CURLOPT_URL => 'https://gateway.ssl256bit.com/catalog_service/set_user_data',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $jsonbody,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
            "X-CASINO-TOKEN: ".$livecasino_apikey,
            "Content-Type: application/json"
          ),
        ));
        $responsecurl = curl_exec($curlcatalog);
                    //Log::notice($responsecurl);

        curl_close($curlcatalog);
        $responsecurl = json_decode($responsecurl);
        

        //gateway.ssl256bit.com/catalogs/game/?gameId=OS_Blackjack&referenceId=100092

        if ($game == 'up_baccarat') {
            $gameid = 'g01.ssl256bit.com/_Games/Baccarat/?gameId=OS_Baccarat';
        }
        elseif ($game == 'blackjack' || $game == 'up_blackjack') {
            $gameid = 'g01.ssl256bit.com/_Games/blackjack_live/#/?gameId=OS_Blackjack';
        }
        elseif ($game == 'blackjack2' || $game == 'up_blackjack2') {
            $gameid = 'g01.ssl256bit.com/_Games/blackjack_live/clients/Blackjack2/#/?gameId=OS_Blackjack_2';
        }
        elseif ($game == 'viproulette' || $game == 'up_viproulette') {
            $gameid = 'g01.ssl256bit.com/_Games/roulette/clients/OriginalSpirit/?gameId=OS_Roulette_2';
        }
        elseif ($game == 'autowheel' || $game == 'up_autowheel') {
            $gameid = 'g01.ssl256bit.com/_Games/roulette/clients/OriginalSpirit/?gameId=OS_Roulette_3';
        }
        elseif ($game == 'rapidroulette' || $game == 'up_rapidroulette') {
            $gameid = 'g01.ssl256bit.com/_Games/roulette/clients/OriginalSpirit/?gameId=OS_Roulette_4';
        }
        else {
            return 'error';
        }
        
        $url = 'https://'.$gameid.'&clientId=&mode=Real&gameToken='.$responsecurl->sessionToken.'&casinoId=55079051&lobbyUrl=https%3A%2F%2Fg01.ssl256bit.com%2F_Apps%2Flobby%2F%3FcatalogId%3D100092_3685299&sessionToken='.$responsecurl->sessionToken;
				return Response::json(array('url' => $url));
    }
    


		    public function createSession($apikey, $playerId, $gameId, $mode)
		    {
		    	$gameid = DB::table('Games')
                ->where('gameid', '=', $gameId)
                ->orWhere('id', '=', $gameId)
                ->first();

		    	$findoperator = DB::table('GameOptions')
                ->where('apikey', '=', $apikey)
                ->first();

                if(!$findoperator) {
				return Response::json(array('error' => 'Auth error'))->setStatusCode(401);
                }

                if(!$gameid) {
				return Response::json(array('error' => 'Game not found'))->setStatusCode(404);
                }

				if($gameid->gameprovider === "upgames" and $findoperator->livecasino_enabled == '0') {
				return Response::json(array('error' => 'Livecasino not enabled on your account'))->setStatusCode(401);
                }

            	$operator = $findoperator->operator;
            	$statichost = $findoperator->statichost;
            	$operatorurl = $findoperator->operatorurl;
            	$player = $playerId.'-'.$operator;
            	$sessiondomain = $findoperator->sessiondomain;
            	$statichost = $findoperator->statichost;
				
				Players::firstOrCreate(
					['name' => strtok($playerId, '-')],
					['operator' => $operator]
				);

                if($gameid->gameprovider === "mascot") {
                	$apimethod = "apiCreateSessionMascot";

                } elseif($gameid->gameprovider === "upgames") {
		            return $this->livecasinoUrl($gameId, $operator, $playerId);
                } elseif($gameid->gameprovider === "evoplay") {
                	$apimethod = "apiCreateEvoplay";
                } else {
                	$apimethod = "apiCreateSessionC27";
                }

				if($mode === "usd" || $mode === "USD") {
            	$bankgroup = $findoperator->bankgroup;
            	}
            	if($mode === "eur" || $mode === "EUR") {
            	$bankgroup = $findoperator->bankgroupeur;
            	}



              	if($gameid->gameprovider === "mascot") {
                $request = file_get_contents('http://slots.apigamble.com/'.$apimethod.'/'.$player.'/'.$gameId.'/'.$bankgroup.'/'.$statichost.'/');
                $sessionurl = $request.'.mascot.games';
              	header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$sessionurl));

            	} elseif($gameid->gameprovider === "evoplay") {
            		if($findoperator->newevoplay === 0) {
					        $evoexplode = explode('-', $player);
					        $currencyevo = $evoexplode[1];
					        $playerevo = $evoexplode[0];
					        $operatorevo = $evoexplode[2];

					        $unique = uniqid();

					        $getevouid = (\App\Evoplay::where('_id', $gameId)->first()->u_id);
					        

					        $token = $unique . '-' . $playerevo . '-' . $currencyevo . '-' . $gameId .'-'. $operatorevo;
					        $gameevo = $getevouid;
					        $args = [ 
					                    $token, 
					                    $gameevo, 
					                    [ 
					                        $playerevo, 
					                        $operatorurl, //exit_url 
					                        $operatorurl, //cash_url
					                        '1' //https
					                    ], 
					                    '1', //denomination
					                    'USD', //currency
					                    '1', //return_url_info
					                    '2' //callback_version
					                ]; 


					        $signature = self::getEvoplaySignature($this->system_id, $this->version, $args, $this->secret_key);

					        $response = json_decode(file_get_contents('http://api.production.games/Game/getURL?project='.$this->system_id.'&version=1&signature='.$signature.'&token='.$token.'&game='.$gameevo.'&settings[user_id]='.$playerevo.'&settings[exit_url]='.$operatorurl.'&settings[cash_url]='.$operatorurl.'&settings[https]=1&denomination=1&currency=USD&return_url_info=1&callback_version=2'), true);
					                $url = $response['data']['link'];
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => $url));

				} else {
					        $evoexplode = explode('-', $player);
					        $currencyevo = $evoexplode[1];
					        $playerevo = $evoexplode[0];
					        $operatorevo = $evoexplode[2];

					        $unique = uniqid();

					        $getevouid = (\App\Evoplay::where('_id', $gameId)->first()->u_id);
					        

					        $token = $unique . '-' . $playerevo . '-' . $currencyevo . '-' . $gameId .'-'. $operatorevo;
					        $gameevo = $getevouid;
					        $args = [ 
					                    $token, 
					                    $gameevo, 
					                    [ 
					                        $playerevo, 
					                        $operatorurl, //exit_url 
					                        $operatorurl, //cash_url
					                        '1' //https
					                    ], 
					                    '1', //denomination
					                    'USD', //currency
					                    '1', //return_url_info
					                    '2' //callback_version
					                ]; 


					        $signature = self::getEvoplaySignature($this->new_system_id, $this->version, $args, $this->new_secret_key);

					        $response = json_decode(file_get_contents('http://api.production.games/Game/getURL?project='.$this->new_system_id.'&version=1&signature='.$signature.'&token='.$token.'&game='.$gameevo.'&settings[user_id]='.$playerevo.'&settings[exit_url]='.$operatorurl.'&settings[cash_url]='.$operatorurl.'&settings[https]=1&denomination=1&currency=USD&return_url_info=1&callback_version=2'), true);
					                $url = $response['data']['link'];
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => $url));
				}

              	} else {

              	$request = file_get_contents('http://slots.apigamble.com/'.$apimethod.'/'.$player.'/'.$gameId.'/'.$bankgroup.'/'.$statichost.'/');
                $url = array('url' => $request.$sessiondomain);
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$request.$sessiondomain));
				
				}


               	DB::table('Sessions')->insertGetId(
				    array('operator' => $operator, 'gameId' => $gameId, 'mode' => $mode, 'playerId' => $playerId, 'sessionUrl' => $sessionurl, 'created_at' => now())
				);
			}



		    public function createDemoSession($apikey, $gameId)
		    {
		    	$game = DB::table('Games')
                ->where('gameid', '=', $gameId)
                ->orWhere('id', '=', $gameId)
                ->first();

		    	$findoperator = DB::table('GameOptions')
                ->where('apikey', '=', $apikey)
                ->first();

                if(!$findoperator) {
	        	return response("AUTHORIZATION ERROR", 401)
                ->header('Content-Type', 'text/plain'); 
                }

                if(!$game) {
	        	return response("GAME NOT FOUND", 404)
                ->header('Content-Type', 'text/plain'); 
                }


                if($game->gameprovider === "mascot") {
                	$apimethod = "apiDemoSessionC27mascot";

                } elseif($game->gameprovider === "upgames") {
                	$apimethod = "apiCreateLiveCasino";
                } elseif($game->gameprovider === "evoplay") {
                	$apimethod = "apiCreateEvoplay";
                } else {
                	$apimethod = "apiDemoSessionC27";
                }

              	if($game->gameprovider === "mascot") {
                $request = file_get_contents('http://slots.apigamble.com/apiDemoSessionC27mascot/'.$gameId.'/');
                $sessionurl = $request.'.mascot.games';
              	header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$sessionurl));


              	} else {

                $request = file_get_contents('http://slots.apigamble.com/apiDemoSessionC27/'.$gameId.'/');
                $url = array('url' => $request.'.gambleapi.com');
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$request.'.gambleapi.com'));
				
				}

			}



		    public function createBonusSession($apikey, $playerId, $gameId, $mode, $fscount)
		    {
		    	$gameid = DB::table('Games')
                ->where('gameid', '=', $gameId)
                ->orWhere('id', '=', $gameId)
                ->first();

		    	$findoperator = DB::table('GameOptions')
                ->where('apikey', '=', $apikey)
                ->first();

                if(!$findoperator) {
				return Response::json(array('error' => 'Auth error'))->setStatusCode(401);
                }

                if(!$gameid) {
				return Response::json(array('error' => 'Game not found'))->setStatusCode(404);
                }


            	$operator = $findoperator->operator;
            	$statichost = $findoperator->statichost;
            	$operatorurl = $findoperator->operatorurl;
            	$player = $playerId.'-'.$operator;
            	$sessiondomain = $findoperator->sessiondomain;
            	$statichost = $findoperator->statichost;
				
				Players::firstOrCreate(
					['name' => strtok($playerId, '-')],
					['operator' => $operator]
				);


                if($gameid->gameprovider === "mascot") {
                	$apimethod = "apiFreeSpinsSessionMascot";
                } elseif($gameid->gameprovider === "netent") {
		        	$apimethod = "apiFreeSpinsSession";

		        } else {
		        	$apimethod = "apiFreeSpinsSession";

                }

				if($mode === "usd" || $mode === "USD") {
            	$bankgroup = $findoperator->bankgroup;
            	}
            	if($mode === "eur" || $mode === "EUR") {
            	$bankgroup = $findoperator->bankgroupeur;
            	}


              	if($gameid->gameprovider === "mascot") {
                $request = file_get_contents('http://slots.apigamble.com/'.$apimethod.'/'.$player.'/'.$gameId.'/'.$bankgroup.'/'.$statichost.'/'.$fscount);
                $sessionurl = $request.'.mascot.games';
              	header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$sessionurl));

            	} elseif($gameid->gameprovider === "evoplay") {     
					        $evoexplode = explode('-', $player);
					        $currencyevo = $evoexplode[1];
					        $playerevo = $evoexplode[0];
					        $operatorevo = $evoexplode[2];

					        $unique = uniqid();

					        $getevouid = (\App\Evoplay::where('_id', $gameId)->first()->u_id);
					        

					        $token = $unique . '-' . $playerevo . '-' . $currencyevo . '-' . $gameId .'-'. $operatorevo;
					        $gameevo = $getevouid;
					        $args = [ 
					                    $token, 
					                    $gameevo, 
					                    [ 
					                        $playerevo, 
					                        $operatorurl, //exit_url 
					                        $operatorurl, //cash_url
					                        '1' //https
					                    ], 
					                    '1', //denomination
					                    'USD', //currency
					                    '1', //return_url_info
					                    '2' //callback_version
					                ]; 


					        $signature = self::getEvoplaySignature($this->system_id, $this->version, $args, $this->secret_key);

					        $response = json_decode(file_get_contents('http://api.production.games/Game/getURL?project='.$this->system_id.'&version=1&signature='.$signature.'&token='.$token.'&game='.$gameevo.'&settings[user_id]='.$playerevo.'&settings[exit_url]='.$operatorurl.'&settings[cash_url]='.$operatorurl.'&settings[https]=1&denomination=1&currency=USD&return_url_info=1&callback_version=2'), true);
					                $url = $response['data']['link'];
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => $url));

              	} else {

              	$request = file_get_contents('http://slots.apigamble.com/'.$apimethod.'/'.$player.'/'.$gameId.'/'.$bankgroup.'/'.$statichost.'/'.$fscount);
                $url = array('url' => $request.$sessiondomain);
                header('Access-Control-Allow-Origin: *');
				header('Content-type: application/json');
				return Response::json(array('url' => 'https://'.$request.$sessiondomain));
				
				}


               	DB::table('Sessions')->insertGetId(
				    array('operator' => $operator, 'gameId' => $gameId, 'mode' => $mode, 'playerId' => $playerId, 'sessionUrl' => $sessionurl, 'created_at' => now())
				);
			}






		    public function getEvoplaySignature($system_id, $version, array $args, $secret_key)
		    {
		        $md5 = array();
		                $md5[] = $system_id;
		                $md5[] = $version;
		                foreach ($args as $required_arg) {
		                        $arg = $required_arg;
		                        if(is_array($arg)){
		                                if(count($arg)) {
		                                        $recursive_arg = '';
		                                        array_walk_recursive($arg, function($item) use (& $recursive_arg) { if(!is_array($item)) { $recursive_arg .= ($item . ':');} });
		                                        $md5[] = substr($recursive_arg, 0, strlen($recursive_arg)-1); // get rid of last colon-sign
		                                } else {
		                                $md5[] = '';
		                                }
		                        } else {
		                $md5[] = $arg;
		                }
		        };
		        $md5[] = $secret_key;
		        $md5_str = implode('*', $md5);
		        $md5 = md5($md5_str);
		        return $md5;
		    }




}

