<?php

namespace App\Nova;

use App\Games;
use Laravel\Nova\Panel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;

class Provider extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
	 
	public static $group = "Games API"; 
	
    public static $model = \App\Providers::class;

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
        'id',
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
			->sortable()
			->canSee(function ($request) { 
					return $request->user()->access == "administrator";
			}),
			Text::make('Name')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
			Text::make('Provider')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
				})
				->canSee(function ($request) { 
					return $request->user()->access == "administrator";
			}),
				
			AdvancedNumber::make('Revenue', 'revenue')
			    ->sortable()
                ->rules('required', 'numeric', 'max:100', 'min:1')
				->decimals(0)
				->min(0)
				->max(100)
				->prefix('%')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
			
			HasMany::make(__('Provider Games'), 'games', Gameslist::class)
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
