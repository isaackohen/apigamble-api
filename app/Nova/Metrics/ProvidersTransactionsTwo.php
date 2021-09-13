<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\withdrawAndDeposit;
use App\Apikeys;
use App\Games;
use App\GameOptions;

class ProvidersTransactionsTwo extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
	 
	public function name()
	{
		return 'Providers Transactions Part 2';
	}
	 
    public function calculate(NovaRequest $request)
    {
		if($request->user()->access == 'administrator') {
			$igrosoft = []; $igt = []; 
			$kajot = []; $konami = []; $merkur = []; $microgaming = [];
			$playson = []; $quickspin = []; $wazdan = [];
			$amatic = []; $editor = [];
			
			foreach(Games::get() as $game) {
				if($game->gameprovider == 'igrosoft'){
					$igrosoft[] = $game->gameid;
				}
				if($game->gameprovider == 'igt'){
					$igt[] = $game->gameid;
				}
				if($game->gameprovider == 'kajot'){
					$kajot[] = $game->gameid;
				}
				if($game->gameprovider == 'konami'){
					$konami[] = $game->gameid;
				}
				if($game->gameprovider == 'merkur'){
					$merkur[] = $game->gameid;
				}
				if($game->gameprovider == 'microgaming'){
					$microgaming[] = $game->gameid;
				}
				if($game->gameprovider == 'playson'){
					$playson[] = $game->gameid;
				}
				if($game->gameprovider == 'quickspin'){
					$quickspin[] = $game->gameid;
				}
				if($game->gameprovider == 'wazdan'){
					$wazdan[] = $game->gameid;
				}
				if($game->gameprovider == 'amatic'){
					$amatic[] = $game->gameid;
				}
				if($game->gameprovider == 'editor'){
					$editor[] = $game->gameid;
				}
			}
			
			$Infoigrosoft = withdrawAndDeposit::whereIn('gameid', $igrosoft)->count();
			$Infoigt = withdrawAndDeposit::whereIn('gameid', $igt)->count();
			$Infokajot = withdrawAndDeposit::whereIn('gameid', $kajot)->count();
			$Infokonami = withdrawAndDeposit::whereIn('gameid', $konami)->count();
			$Infomerkur = withdrawAndDeposit::whereIn('gameid', $merkur)->count();
			$Infomicrogaming = withdrawAndDeposit::whereIn('gameid', $microgaming)->count();
			$Infoplayson = withdrawAndDeposit::whereIn('gameid', $playson)->count();
			$Infoquickspin = withdrawAndDeposit::whereIn('gameid', $quickspin)->count();
			$Infowazdan = withdrawAndDeposit::whereIn('gameid', $wazdan)->count();
			$Infoamatic = withdrawAndDeposit::whereIn('gameid', $amatic)->count();
			$Infoeditor = withdrawAndDeposit::whereIn('gameid', $editor)->count();
			
			return $this->result([
				'Igrosoft' => $Infoigrosoft,
				'IGT' => $Infoigt,
				'Kajot' => $Infokajot,
				'Konami' => $Infokonami,
				'Merkur' => $Infomerkur,
				'Microgaming' => $Infomicrogaming,
				'Playson' => $Infoplayson,
				'Quickspin' => $Infoquickspin,
				'Wazdan' => $Infowazdan,
				'Amatic' => $Infoamatic,
				'Editor' => $Infoeditor,
			]);
		} else {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			$igrosoft = []; $igt = []; 
			$kajot = []; $konami = []; $merkur = []; $microgaming = [];
			$playson = []; $quickspin = []; $wazdan = [];
			$amatic = []; $editor = [];
			
			foreach(Games::get() as $game) {
				if($game->gameprovider == 'igrosoft'){
					$igrosoft[] = $game->gameid;
				}
				if($game->gameprovider == 'igt'){
					$igt[] = $game->gameid;
				}
				if($game->gameprovider == 'kajot'){
					$kajot[] = $game->gameid;
				}
				if($game->gameprovider == 'konami'){
					$konami[] = $game->gameid;
				}
				if($game->gameprovider == 'merkur'){
					$merkur[] = $game->gameid;
				}
				if($game->gameprovider == 'microgaming'){
					$microgaming[] = $game->gameid;
				}
				if($game->gameprovider == 'playson'){
					$playson[] = $game->gameid;
				}
				if($game->gameprovider == 'quickspin'){
					$quickspin[] = $game->gameid;
				}
				if($game->gameprovider == 'wazdan'){
					$wazdan[] = $game->gameid;
				}
				if($game->gameprovider == 'amatic'){
					$amatic[] = $game->gameid;
				}
				if($game->gameprovider == 'editor'){
					$editor[] = $game->gameid;
				}
			}
			
			$Infoigrosoft = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $igrosoft)->count();
			$Infoigt = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $igt)->count();
			$Infokajot = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $kajot)->count();
			$Infokonami = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $konami)->count();
			$Infomerkur = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $merkur)->count();
			$Infomicrogaming = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $microgaming)->count();
			$Infoplayson = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $playson)->count();
			$Infoquickspin = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $quickspin)->count();
			$Infowazdan = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $wazdan)->count();
			$Infoamatic = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $amatic)->count();
			$Infoeditor = withdrawAndDeposit::whereIn('operator', $apioperator)->whereIn('gameid', $editor)->count();
			
			return $this->result([
				'Igrosoft' => $Infoigrosoft,
				'IGT' => $Infoigt,
				'Kajot' => $Infokajot,
				'Konami' => $Infokonami,
				'Merkur' => $Infomerkur,
				'Microgaming' => $Infomicrogaming,
				'Playson' => $Infoplayson,
				'Quickspin' => $Infoquickspin,
				'Wazdan' => $Infowazdan,
				'Amatic' => $Infoamatic,
				'Editor' => $Infoeditor,
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
        return 'providers-transactions-two';
    }
}
