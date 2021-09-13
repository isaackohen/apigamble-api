<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use App\Apikeys;
use App\withdrawAndDeposit;
use App\GameOptions;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Players extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Players::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
	 
	public static $group = "Games API"; 
	 
    public static $title = 'id';
	
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
        return $request->user()->access == "administrator";
    }
	
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			return $query->whereIn('operator', $apioperator);
		} else {
			return $query;
		}
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

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
			->sortable(),
			Text::make('Uniqid', 'name')
			->sortable(),
			Text::make('Operator', 'operator')
			->sortable(),
			Text::make('Total Games', function () {
				$games = withdrawAndDeposit::where('user', 'like', '%'.$this->name.'%')->count();
				return $games;
			}),
			Text::make('Bets loss', function () {
				$deposit = withdrawAndDeposit::where('user', 'like', '%'.$this->name.'%')->selectRaw('SUM(withdraw) as total')
				->toBase()
				->first();
            return ($deposit->total/100).'$';
			}),
			Text::make('Bets wins', function () {
				$withdraw = withdrawAndDeposit::where('user', 'like', '%'.$this->name.'%')->selectRaw('SUM(deposit) as total')
				->toBase()
				->first();
            return ($withdraw->total/100).'$';
			}),
			Text::make('Bets profit', function () {
				$profit = withdrawAndDeposit::where('user', 'like', '%'.$this->name.'%')->selectRaw('SUM(deposit - withdraw) as total')
				->toBase()
				->first();
            return ($profit->total/100).'$';
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
