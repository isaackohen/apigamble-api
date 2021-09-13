<?php

namespace App\Nova;

use App\Nova\Metrics\NewApikeys;
use App\Nova\Metrics\ApikeysPerDay;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;

class Apikeys extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
	 
     */
    public static $model = \App\Apikeys::class;

	public static function label()
	{
		return 'API Keys';
	}
	
	public static $group = "Main settings";


    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
	 
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			return $query->where('ownedBy', $request->user()->id);
		} else {
			return $query;
		}
    }
	
	public function authorizedToUpdate(Request $request)
    {
        return $request->user()->access == "administrator";
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
        'id', 'apikey',
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
			BelongsTo::make('User')->rules('required')
			->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
			Select::make('Type')->options([
				'slots' => 'Slots',
				'sports' => 'Sports',
                'creditcard' => 'Creditcard Payments',
				'paykey' => 'Crypto Payments',
			])->displayUsingLabels()->readonly(function() {
				return $this->resource->id ? true : false;
			})
			->rules('required'),
			Text::make('Api Key', 'apikey')
                ->sortable()
				->default(strtoupper(md5(microtime())))
                ->rules('required', 'max:32', 'min:3')
				->readonly(function() {
					return $this->resource->id ? true : false;
				}),
			Select::make('Status')->options([
				'active' => 'Active',
				'inactive' => 'Inactive',
				'pending' => 'Pending',
			])->displayUsingLabels()->hideFromIndex()->hideFromDetail()
			->rules('required'),
			Indicator::make('Status')
				->labels([
					'active' => 'Active',
					'inactive' => 'Inactive',
					'pending' => 'Pending',
				])
				->colors([
					'active' => 'green',
					'inactive' => 'red',
					'pending' => 'orange',
				])
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
		return [
			(new NewApikeys)->canSee(function ($request) {
				return $request->user()->access == "administrator";
			})->width('1/2'),
			(new ApikeysPerDay)->canSee(function ($request) {
				return $request->user()->access == "administrator";
			})->width('1/2'),
		];
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
