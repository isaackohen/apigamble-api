<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\withdrawAndDeposit;
use App\Apikeys;
use App\Games;
use App\GameOptions;

class ProvidersTransactionsFirst extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
	 
	public function name()
	{
		return 'Providers Transactions Part 1';
	}
	
    public function calculate(NovaRequest $request)
    {
		if($request->user()->access == 'administrator') {
			$livecasino = []; $netent = []; $pragmatic = []; $mascot = [];
			$booongo = []; $evoplay = []; $aristocrat = []; $apollo = []; 
			$gaminator = []; $greentube = []; $igrosoft = []; $igt = []; 
			$kajot = []; $konami = []; $merkur = []; $microgaming = [];
			$playson = []; $quickspin = []; $wazdan = [];
			$amatic = []; $editor = [];
			foreach(Games::get() as $game) {
				if($game->gameprovider == 'livecasino'){
					$livecasino[] = $game->gameid;
				}
				if($game->gameprovider == 'netent'){
					$netent[] = $game->gameid;
				}
				if($game->gameprovider == 'pragmatic'){
					$pragmatic[] = $game->gameid;
				}
				if($game->gameprovider == 'mascot'){
					$mascot[] = $game->gameid;
				}
				if($game->gameprovider == 'booongo'){
					$booongo[] = $game->gameid;
				}
				if($game->gameprovider == 'evoplay'){
					$evoplay[] = $game->gameid;
				}
				if($game->gameprovider == 'aristocrat'){
					$aristocrat[] = $game->gameid;
				}
				if($game->gameprovider == 'apollo'){
					$apollo[] = $game->gameid;
				}
				if($game->gameprovider == 'gaminator'){
					$gaminator[] = $game->gameid;
				}
				if($game->gameprovider == 'greentube'){
					$greentube[] = $game->gameid;
				}
			}
			
			$Infolivecasino = withdrawAndDeposit::whereIn('gameid', $livecasino)->count();
			$Infonetent = withdrawAndDeposit::whereIn('gameid', $netent)->count();
			$Infopragmatic = withdrawAndDeposit::whereIn('gameid', $pragmatic)->count();
			$Infomascot = withdrawAndDeposit::whereIn('gameid', $mascot)->count();
			$Infobooongo = withdrawAndDeposit::whereIn('gameid', $booongo)->count();
			$Infoevoplay = withdrawAndDeposit::whereIn('gameid', $evoplay)->count();
			$Infoaristocrat = withdrawAndDeposit::whereIn('gameid', $aristocrat)->count();
			$Infoapollo = withdrawAndDeposit::whereIn('gameid', $apollo)->count();
			$Infogaminator = withdrawAndDeposit::whereIn('gameid', $gaminator)->count();
			$Infogreentube = withdrawAndDeposit::whereIn('gameid', $greentube)->count();
			
			return $this->result([
				'Live Casino' => $Infolivecasino,
				'NetEnt' => $Infonetent,
				'Pragmatic Play' => $Infopragmatic,
				'Mascot Games' => $Infomascot,
				'Booongo' => $Infobooongo,
				'Evoplay' => $Infoevoplay,
				'Aristocrat' => $Infoaristocrat,
				'Apollo' => $Infoapollo,
				'Gaminator' => $Infogaminator,
				'Greentube' => $Infogreentube
			]);
		} else {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			$livecasino = []; $netent = []; $pragmatic = []; $mascot = [];
			$booongo = []; $evoplay = []; $aristocrat = []; $apollo = []; 
			$gaminator = []; $greentube = []; $igrosoft = []; $igt = []; 
			$kajot = []; $konami = []; $merkur = []; $microgaming = [];
			$playson = []; $quickspin = []; $wazdan = [];
			$amatic = []; $editor = [];
			foreach(Games::get() as $game) {
				if($game->gameprovider == 'livecasino'){
					$livecasino[] = $game->gameid;
				}
				if($game->gameprovider == 'netent'){
					$netent[] = $game->gameid;
				}
				if($game->gameprovider == 'pragmatic'){
					$pragmatic[] = $game->gameid;
				}
				if($game->gameprovider == 'mascot'){
					$mascot[] = $game->gameid;
				}
				if($game->gameprovider == 'booongo'){
					$booongo[] = $game->gameid;
				}
				if($game->gameprovider == 'evoplay'){
					$evoplay[] = $game->gameid;
				}
				if($game->gameprovider == 'aristocrat'){
					$aristocrat[] = $game->gameid;
				}
				if($game->gameprovider == 'apollo'){
					$apollo[] = $game->gameid;
				}
				if($game->gameprovider == 'gaminator'){
					$gaminator[] = $game->gameid;
				}
				if($game->gameprovider == 'greentube'){
					$greentube[] = $game->gameid;
				}
			}
			
			$Infolivecasino = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $livecasino)->count();
			$Infonetent = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $netent)->count();
			$Infopragmatic = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $pragmatic)->count();
			$Infomascot = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $mascot)->count();
			$Infobooongo = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $booongo)->count();
			$Infoevoplay = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $evoplay)->count();
			$Infoaristocrat = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $aristocrat)->count();
			$Infoapollo = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $apollo)->count();
			$Infogaminator = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $gaminator)->count();
			$Infogreentube = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $greentube)->count();
			
			return $this->result([
				'Live Casino' => $Infolivecasino,
				'NetEnt' => $Infonetent,
				'Pragmatic Play' => $Infopragmatic,
				'Mascot Games' => $Infomascot,
				'Booongo' => $Infobooongo,
				'Evoplay' => $Infoevoplay,
				'Aristocrat' => $Infoaristocrat,
				'Apollo' => $Infoapollo,
				'Gaminator' => $Infogaminator,
				'Greentube' => $Infogreentube
			]);
		}
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'providers-transactions';
    }
}
