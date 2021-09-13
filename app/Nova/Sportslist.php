<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use App\Nova\Metrics\ProvidersTransactionsFirst;
use App\Nova\Metrics\ProvidersTransactionsTwo;
use Laravel\Nova\Fields\BelongsTo;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;

class Sportslist extends Resource
{
	
	public static $group = "Sports API"; 
	
    public static function authorizedToCreate(Request $request)
    {
        return $request->user()->access == "administrator";
    }

    public function authorizedToDelete(Request $request)
    {
        return $request->user()->access == "administrator";
    }

    public function authorizedToUpdate(Request $request)
    {
        return $request->user()->access == "administrator";
    }
    
    /**
     * The model the resource corresponds to.
     *
     * @var string
	 
     */
    public static $model = \App\Sports::class;

	public static function label()
	{
		return 'List Sports';
	}

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'sports_id', 'sports_name', 'pay_per_month'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('Internal ID'), 'id')->sortable(),
            Text::make('Sport ID', 'sports_id')
			->sortable()
			->rules('required', 'numeric'),
            Text::make('Sport Name', 'sports_name')
			->sortable()
			->rules('required'),
			AdvancedNumber::make('Fee per Month', 'pay_per_month')
			    ->sortable()
                ->rules('required', 'numeric')
				->decimals(2)
				->prefix('$')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
