<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\withdrawAndDeposit;
use App\Apikeys;
use App\GameOptions;

class DepositsPart extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
		ini_set('memory_limit', -1);
		if($request->user()->access != 'administrator') {
		$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
		$apioperator = [];
		foreach($apidata as $data) {
			$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
		}
		$withdraws = withdrawAndDeposit::whereIn('operator', $apioperator)->get();
		$zerobet = 0;
		$moreone = 0;
		$moreten = 0;
		$morefifty = 0;
		$morehundred = 0;
		foreach($withdraws as $withdraw) {
			if($withdraw->withdraw == 0) $zerobet++;
			if($withdraw->withdraw >= 100 && $withdraw->withdraw < 1000) $moreone++;
			if($withdraw->withdraw >= 1000 && $withdraw->withdraw < 5000) $moreten++;
			if($withdraw->withdraw >= 5000 && $withdraw->withdraw < 10000) $morefifty++;
			if($withdraw->withdraw >= 10000) $morehundred++;
		}
		$data = [
		'0$' => $zerobet,
		'>1$' => $moreone,
		'>10$' => $moreten,
		'>50$' => $morefifty,
		'>100$' => $morehundred,
		];
        return $this->result($data);
		} else {
		$withdraws = withdrawAndDeposit::get();
		$zerobet = 0;
		$moreone = 0;
		$moreten = 0;
		$morefifty = 0;
		$morehundred = 0;
		foreach($withdraws as $withdraw) {
			if($withdraw->withdraw == 0) $zerobet++;
			if($withdraw->withdraw >= 100 && $withdraw->withdraw < 1000) $moreone++;
			if($withdraw->withdraw >= 1000 && $withdraw->withdraw < 5000) $moreten++;
			if($withdraw->withdraw >= 5000 && $withdraw->withdraw < 10000) $morefifty++;
			if($withdraw->withdraw >= 10000) $morehundred++;
		}
		$data = [
		'0$' => $zerobet,
		'>1$' => $moreone,
		'>10$' => $moreten,
		'>50$' => $morefifty,
		'>100$' => $morehundred,
		];
        return $this->result($data);
		}
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
        return 'deposits-part';
    }
}
