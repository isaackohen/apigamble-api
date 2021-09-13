<?php

namespace App\Nova;

use App\Nova\Metrics\NewApikeys;
use App\Nova\Metrics\ApikeysPerDay;
use Illuminate\Http\Request;
use App\Apikeys;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;

class GameOptions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
	 
     */
    public static $model = \App\GameOptions::class;

	public static function label()
	{
		return 'Options';
	}
	
	public static $group = "Games API"; 


    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
	 
	public static function authorizedToCreate(Request $request)
    {
        return false;
    }
	 
	public function authorizedToDelete(Request $request)
    {
        return false;
    }
	 
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apikey = [];
			foreach($apidata as $data) {
				$apikey[] = $data->apikey;
			}
			return $query->whereIn('apikey', $apikey);
		} else {
			return $query;
		}
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
			ID::make(__('ID'), 'id')
			->canSee(function ($request) {
					return $request->user()->access == "administrator";
			})
			->sortable(),
			Text::make('Operator', 'operator')
                ->sortable()
                ->rules('required', 'max:32', 'min:3'),
			Text::make('Api Key', 'apikey')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
            Text::make('Bank Group USD', 'bankgroup')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
            Text::make('Bank Group EUR', 'bankgroupeur')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
            Text::make('Bonus Bank Group USD', 'bonusbankgroup')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
            Text::make('Bonus Bank Group EUR', 'bonusbankgroupeur')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
            Text::make('Static Host', 'statichost')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
			Text::make('Callback Base URL', 'callbackurl')
                ->hideFromIndex()
                ->rules('required', 'max:128', 'min:3'),
            Text::make('Livecasino Prefix URL', 'livecasino_prefix')
                ->hideFromIndex()
                ->rules('required', 'max:24', 'min:3'),
            Text::make('Slots Prefix URL', 'slots_prefix')
                ->hideFromIndex()
                ->rules('required', 'max:24', 'min:3'),
            Text::make('Poker Prefix URL', 'poker_prefix')
                ->hideFromIndex()
                ->rules('required', 'max:24', 'min:3'),
            Text::make('Evoplay Prefix URL', 'evoplay_prefix')
                ->hideFromIndex()
                ->rules('required', 'max:24', 'min:3'),
			Text::make('Casino Website', 'operatorurl')
                ->hideFromIndex()
                ->rules('required', 'max:128', 'min:3'),
            Text::make('Session Domain', 'sessiondomain')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3'),
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
