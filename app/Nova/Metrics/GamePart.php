<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\withdrawAndDeposit;
use App\Apikeys;
use App\GameOptions;

class GamePart extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
	 
	 public function name()
	{
		return 'Games Part';
	} 
	 
    public function calculate(NovaRequest $request)
    {
		if($request->user()->access == 'administrator') {
			return $this->count($request, withdrawAndDeposit::class, 'gameid');
		} else {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			return $this->count($request, withdrawAndDeposit::whereIn('operator', $apioperator), 'gameid')
				->label(function ($value) {
					switch ($value) {
						case null:
							return 'None';
						default:
							return ucfirst($value);
					}
			});	
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
        return 'game-part';
    }
}
