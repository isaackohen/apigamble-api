<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ericlagarda\NovaTextCard\TextCard;
use App\Apikeys;
use \Cache;

class Wallets extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
	 
	public static $group = "Crypto Payments";  
	 
    public static $model = \App\Wallets::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */

	public static function label()
	{
		return 'Wallets';
	}
	 
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
			$apidata = Apikeys::where('ownedBy', $request->user()->id)->get();
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
        'id', 'currency', 'wallet', 'apikey'
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
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Currency')
                ->sortable()
                ->rules('required', 'max:32', 'min:3')
                ->readonly(function ($request) {
                    return $request->user()->access != "administrator";
            }),
			Text::make('Address', 'wallet')
                ->sortable()
                ->rules('required', 'min:8')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}), 
			Text::make('Balance')
                ->sortable()
                ->rules('required', 'min:1')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
			Text::make('Token Balance', 'tokenbalance')
                ->sortable()
                ->rules('required', 'min:1')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
			Text::make('Api Key', 'apikey')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
			}),
            Text::make('Label')
                ->rules('required', 'max:10', 'min:3')
                ->sortable()
				->readonly(),
			Text::make('Callback Url', 'callbackurl')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
			Text::make('Subscribed')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
			Text::make('Contract Address', 'contractaddress')
                ->hideFromIndex()
                ->rules('required', 'max:32', 'min:3')
				->readonly(),
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
		$apidata = Apikeys::where('ownedBy', $request->user()->id)->get();
		$apikey = [];
		foreach($apidata as $data) {
			$apikey[] = $data->apikey;
		}
		$result = Cache::remember('key', 12000, function () {
			return json_decode(file_get_contents('https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,MATIC,USDC,NANO,BNB,USDT,ETH,BCH,XRP,LTC,IOTA,DOGE,XMR,TRX&tsyms=USD,EUR'));
        });
		if (!Cache::has('conversions: xblzd')) Cache::put('conversions: xblzd', file_get_contents("https://api.coingecko.com/api/v3/coins/blizzard?localization=false&market_data=true"), now()->addHours(1));
        $json = json_decode(Cache::get('conversions: xblzd'));
		if($request->user()->access != 'administrator') {
			$totalEth = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'eth')->sum('balance');
			$totalEthUsd = $totalEth* $result->ETH->USD;
			$totalBtc = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'btc')->sum('balance');
			$totalBtcUsd = $totalBtc* $result->BTC->USD;
			$totalLTC = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'ltc')->sum('balance');
			$totalLTCUsd = $totalLTC* $result->LTC->USD;
			$totalBSC = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'bcs')->sum('balance');
			$totalBSCUsd = $totalBSC* $result->BNB->USD;
			$totalTRX = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'trx')->sum('balance');
			$totalTRXUsd = $totalTRX* $result->TRX->USD;
			$totalBCH = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'bch')->sum('balance');
			$totalBCHUsd = $totalBCH* $result->BCH->USD;
			$totalNormalxBLZD = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'xblzd')->sum('balance');
			$totalNormalxBLZDUsd = $totalNormalxBLZD* $result->BNB->USD;
			$totalTokenxBLZD = \App\PaymentOptions::whereIn('apikey', $apikey)->where('crypto', 'xblzd')->sum('balance');
			$totalTokenxBLZDUsd = $totalNormalxBLZD* $json->market_data->current_price->usd;
		} else {
			$totalEth = \App\PaymentOptions::where('crypto', 'eth')->sum('balance');
			$totalEthUsd = $totalEth* $result->ETH->USD;
			$totalBtc = \App\PaymentOptions::where('crypto', 'btc')->sum('balance');
			$totalBtcUsd = $totalBtc* $result->BTC->USD;
			$totalLTC = \App\PaymentOptions::where('crypto', 'ltc')->sum('balance');
			$totalLTCUsd = $totalLTC* $result->LTC->USD;
			$totalBSC = \App\PaymentOptions::where('crypto', 'bcs')->sum('balance');
			$totalBSCUsd = $totalBSC* $result->BNB->USD;
			$totalTRX = \App\PaymentOptions::where('crypto', 'trx')->sum('balance');
			$totalTRXUsd = $totalTRX* $result->TRX->USD;
			$totalBCH = \App\PaymentOptions::where('crypto', 'bch')->sum('balance');
			$totalBCHUsd = $totalBCH* $result->BCH->USD;
			$totalNormalxBLZD = \App\PaymentOptions::where('crypto', 'xblzd')->sum('balance');
			$totalNormalxBLZDUsd = $totalNormalxBLZD* $result->BNB->USD;
			$totalTokenxBLZD = \App\PaymentOptions::where('crypto', 'xblzd')->sum('balance');
			$totalTokenxBLZDUsd = $totalNormalxBLZD* $json->market_data->current_price->usd;
		}
        return [
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total ETH</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalEth.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total ETH $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalEthUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total BTC</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalBtc.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total BTC $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalBtcUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total LTC</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalLTC.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total LTC $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalLTCUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total BSC</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalBSC.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total BSC $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalBSCUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total TRX</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalTRX.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total TRX $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalTRXUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total TRX</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalBCH.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total TRX $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalBCHUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total xBLZD (BNB)</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalNormalxBLZD.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total xBLZD (BNB) $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalNormalxBLZDUsd), 2, '.', '').'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total xBLZD (Token)</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">'.$totalTokenxBLZD.'</p>')
				->textAsHtml(),
			(new TextCard())
				->width('1/4')
				->height(120)
				->center(false)
				->heading('<div class="flex mb-4"><h3 class="mr-3 text-base text-80 font-bold">Total xBLZD (Token) $</h3></div>')
				->headingAsHtml()
				->text('<p class="flex items-center text-3xl mb-4">$'.number_format(floatval($totalTokenxBLZDUsd), 2, '.', '').'</p>')
				->textAsHtml(),
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
        return [
			(new Actions\Withdraw())
			->showOnTableRow()
			->showOnDetail(),
			(new Actions\SendGas())
			->showOnTableRow()
			->showOnDetail(),
		];
    }
}
