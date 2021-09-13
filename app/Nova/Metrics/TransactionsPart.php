<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\withdrawAndDeposit;
use App\Apikeys;
use App\GameOptions;

class TransactionsPart extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
	 
	public function name()
	{
		return 'Operators Transactions';
	} 
	 
    public function calculate(NovaRequest $request)
    {
		if($request->user()->access == 'administrator') {
			return $this->count($request, withdrawAndDeposit::class, 'operator');
		} else {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			return $this->count($request, withdrawAndDeposit::whereIn('operator', $apioperator), 'operator');
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
        return 'transactions-part';
    }
}
