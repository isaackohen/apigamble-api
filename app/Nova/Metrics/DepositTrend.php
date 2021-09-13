<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use App\withdrawAndDeposit;
use Illuminate\Support\Facades\DB;
use App\Apikeys;
use App\GameOptions;

class DepositTrend extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
		if($request->user()->access == 'administrator') {
			return $this->sumByDays($request, withdrawAndDeposit::class, DB::raw('(withdraw/100)'))->format('$0,0');
		} else {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			return $this->sumByDays($request, withdrawAndDeposit::whereIn('operator', $apioperator), DB::raw('(withdraw/100)'))->format('$0,0');
		}
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
			5 => __('5 Days'),
			15 => __('15 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
	 
	protected function getCacheKey(NovaRequest $request)
	{
		return sprintf(
			'nova.metric.%s.%s.%s.%s.%s',
			$this->uriKey(),
			$request->input('range', 'no-range'),
			$request->input('timezone', 'no-timezone'),
			$request->input('twelveHourTime', 'no-12-hour-time'),
			$request->user()->id
		);
	} 
	
    public function cacheFor()
    {
        return now()->addMinutes(60);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'deposit-trend';
    }
}
