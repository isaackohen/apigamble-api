<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use App\Apikeys;
use App\GameOptions;
use App\withdrawAndDeposit;

class Deposit extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
	  	 
	public function name()
	{
		return 'Total Paid In';
	} 
	
	 
    public function calculate(NovaRequest $request)
    {
		$timezone = Nova::resolveUserTimezone($request) ?? $request->timezone;
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			$result = withdrawAndDeposit::whereIn('operator', $apioperator)->selectRaw('SUM(withdraw) as total')
				->whereBetween('created_at', $this->currentRange($request->range, $timezone))
				->toBase()
				->first();
			$previous = withdrawAndDeposit::whereIn('operator', $apioperator)->selectRaw('SUM(withdraw) as total')
				->whereBetween('created_at', $this->previousRange($request->range, $timezone) )
				->toBase()
				->first();	
			return $this->result($result->total/100)->previous($previous->total/100)->currency('$')->format('0,0');
		} else {
			$result = withdrawAndDeposit::selectRaw('SUM(withdraw) as total')
				->whereBetween('created_at', $this->currentRange($request->range, $timezone))
				->toBase()
				->first();
			$previous = withdrawAndDeposit::selectRaw('SUM(withdraw) as total')
				->whereBetween('created_at', $this->previousRange($request->range, $timezone) )
				->toBase()
				->first();	
			return $this->result($result->total/100)->previous($previous->total/100)->currency('$')->format('0,0');
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
			1 => __('1 Day'),
			5 => __('5 Days'),
			15 => __('15 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
    }
	
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

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
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
        return 'deposit';
    }
}
