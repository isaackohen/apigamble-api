<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Support\Facades\Log;
use App\Apikeys;
use App\GameOptions;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;


class Transactions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
	 
     */
	 
	public static $group = "Games API";  
	 
    public static $model = \App\withdrawAndDeposit::class;

	public static function label()
	{
		return 'Transactions';
	}
	
	public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }


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
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
            Log::notice($apidata);
			return $query->whereIn('operator', $apioperator);
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
        'id', 'operator',
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
                DateTime::make(__('Timestamp'), 'created_at')
                    ->sortable(),
				Text::make('Operator', 'operator')
					->sortable(),
                Text::make('Game ID', 'gameid')
                    ->sortable(),
				Text::make('Player', 'user')
					->sortable(),               
                Text::make('Bet', 'withdraw', function () {
                return '$'.($this->withdraw / 100);
                }),
                Text::make('Win', 'deposit', function () {
                return '$'.($this->deposit / 100);
                }),                
				Text::make('Currency', 'currency')
					->sortable(),
				Text::make('Transaction ID', 'txid'),
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
