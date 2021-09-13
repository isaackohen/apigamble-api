<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Apikeys;

class PaymentOptions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
	 
	public static $group = "Crypto Payments";  
	 
    public static $model = \App\PaymentOptions::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */

    public static function label()
    {
        return 'Settings';
    }
	
	public static function authorizedToCreate(Request $request)
    {
        return $request->user()->access == "administrator";
    }
	
	public function authorizedToDelete(Request $request)
    {
        return $request->user()->access == "administrator";
    }
	 
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->where('type', 'paykey')->get();
			$apikey = [];
			foreach($apidata as $data) {
				$apikey[] = $data->apikey;
			}
			return $query->whereIn('apikey', $apikey);
		} else {
			return $query;
		}
    }
	 
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'apikey'
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
					return optional($request->findModelQuery()->first())->type != 'paykey';
			})
			->sortable(),
			Text::make('Api Key', 'apikey')
                ->hideWhenCreating()
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			})->hideWhenUpdating(),
			Select::make('Api Key', 'apikey')
			->options($request->user()->access == "administrator" ? \App\Apikeys::pluck('apikey', 'apikey') : \App\Apikeys::where('ownedBy', $request->user()->id)->pluck('apikey', 'apikey'))
			->displayUsingLabels()->hideFromIndex()->hideFromDetail()->rules('required'),
			Select::make('Currency', 'crypto')->options([
				'eth' => 'ETH',
				'ltc' => 'LTC',
				'btc' => 'BTC',
                'doge' => 'DOGE',
				'bsc' => 'BSC',
				'trx' => 'TRX',
                'game1' => 'GAME1',
                'xblzd' => 'xBLZD',
                'gamblecoin' => 'GAMBLECOIN',
                'betshiba' => 'BETSHIBA',
				'bch' => 'BCH',
			])->displayUsingLabels()
			->rules('required'),
			Select::make('Forward', 'forward_enabled')->options([
				'0' => 'Disabled',
				'1' => 'Enabled'
			])->displayUsingLabels()
			->rules('required'),
			Text::make('Callback Url', 'callbackurl')
                ->hideFromIndex()
                ->rules('required', 'max:128', 'min:3'),
			Text::make('Forward Min.', 'forward_minimum')
				->hideFromIndex()
				->rules('required', 'max:32', 'min:1'),
			Text::make('Forward Address', 'forward_address')
				->hideFromIndex()
				->rules('required', 'max:32', 'min:1'),
			Text::make('Pincode', 'masterpass')
				->hideFromIndex()
				->rules('required', 'max:32', 'min:1'),
			
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
