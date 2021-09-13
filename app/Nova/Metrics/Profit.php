<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Apikeys;
use App\withdrawAndDeposit;
use App\GameOptions;

class Profit extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
	
	public function name()
	{
		return 'Profit Games';
	} 
	 
	 
    public function calculate(Request $request)
    {
		$timezone = Nova::resolveUserTimezone($request) ?? $request->timezone;
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			$result = withdrawAndDeposit::whereIn('operator', $apioperator)->selectRaw('SUM(withdraw - deposit) as total')
				->whereBetween('created_at', $this->currentRange($request->range, $timezone))
				->toBase()
				->first();
			$previous = withdrawAndDeposit::whereIn('operator', $apioperator)->selectRaw('SUM(withdraw - deposit) as total')
				->whereBetween('created_at', $this->previousRange($request->range, $timezone) )
				->toBase()
				->first();	
			return $this->result($result->total/100)->previous($previous->total/100)->currency('$')->format('0,0');
		} else {
			$result = withdrawAndDeposit::selectRaw('SUM(withdraw - deposit) as total')
				->whereBetween('created_at', $this->currentRange($request->range, $timezone))
				->toBase()
				->first();
			$previous = withdrawAndDeposit::selectRaw('SUM(withdraw - deposit) as total')
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

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'profit';
    }
}