<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use App\Nova\Metrics\ProvidersTransactionsFirst;
use App\Nova\Metrics\ProvidersTransactionsTwo;
use Laravel\Nova\Fields\BelongsTo;

class Gameslist extends Resource
{
	
	
    public static function authorizedToCreate(Request $request)
    {
        return $request->user()->admin == "1";
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
    public static $model = \App\Games::class;

	public static function label()
	{
		return 'List Games';
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
        'id', 'gameid', 'gameprovider', 'gamename',
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
            //ID::make(__('Internal ID'), 'id')->sortable(),
            BelongsTo::make(__('Provider'), 'provider', Provider::class)
            ->display(function ($provider) {
                return $provider->name;
            }), 
            Text::make('Game ID', 'gameid'),
            Text::make('Game Name', 'gamename'),
            Text::make('Game Description', 'gamedesc'),

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
			ProvidersTransactionsFirst::make()->width('1/2'),
			ProvidersTransactionsTwo::make()->width('1/2'),
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
        return [
            new Filters\Provider,
        ];
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
