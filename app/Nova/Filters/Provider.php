<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Provider extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('gameprovider', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Live Casino' => 'livecasino',
            'NetEnt' => 'netent',
            'Pragmatic Play' => 'pragmatic',
            'Mascot Games' => 'mascot',
			'Booongo' => 'booongo',
			'Evoplay' => 'evoplay',
			'Aristocrat' => 'aristocrat',
			'Apollo' => 'apollo',
			'Gaminator' => 'gaminator',
			'Greentube' => 'greentube',
            'Playstar' => 'playstar',
			'Igrosoft' => 'igrosoft',
			'IGT' => 'igt',
			'Kajot' => 'kajot',
			'Konami' => 'konami',
			'Merkur' => 'merkur',
			'Microgaming' => 'microgaming',
			'Playson' => 'playson',
			'Quickspin' => 'quickspin',
			'Wazdan' => 'wazdan',
			'Amatic' => 'amatic',
            'Editor' => 'editor',
        ];
    }
}