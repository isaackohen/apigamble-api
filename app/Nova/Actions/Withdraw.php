<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use App\Wallets;
use Laravel\Nova\Fields\ActionFields;
use App\Http\Controllers\PaymentTatumController;
use Illuminate\Support\Facades\Validator;
use TuneZilla\DynamicActionFields\DynamicFieldAction;
use Illuminate\Support\Facades\Log;

class Withdraw extends Action
{
    use InteractsWithQueue, Queueable;
	use DynamicFieldAction;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {

		$apikey = $fields->apikey;
		$currency = strtok($fields->currency, '-');
		$balance = $fields->balance;
		$amount = $fields->amount;
		$from = $fields->from_wallet;
		$to = $fields->recipient;
		$masterpass = $fields->pincode;
		
		$cryptoname = $fields->currency == 'xblzd-n' ? 'bnb' : $currency;
		Log::info($apikey);
		if($currency == 'xblzd' && substr(strstr($a, '-'), 1, strlen($a)) == 't') {
			return Action::message('Token error #177');
		}
		
		PaymentTatumController::sendCrypto($apikey, $currency, $amount, $from, $to, $masterpass);
		
        return Action::message('Crypto '.$cryptoname.' has been sent, recipient: '.$to);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
	
	public function fieldsForModels(Collection $models): array
    {
        if ($models->isEmpty()) {
            return [];
        }

        $wallet = $models->first();

        return [
			Text::make('ApiKey')
				->default($wallet->apikey)
				->readonly()
				->rules('required'),
			Select::make('Currency')->options([
				'xblzd-n' => 'xBLZD (BNB balance)',
				'xblzd-t' => 'xBLZD (Token balance)',
				'eth' => 'ETH',
				'ltc' => 'LTC',
				'btc' => 'BTC',
				'bsc' => 'BSC',
				'trx' => 'TRX',
				'bch' => 'BCH',
			])->rules('required'),
			Text::make('Balance')
				->default('Balance: '.$wallet->balance.' | Token balance: '.$wallet->tokenbalance)
				->readonly()
				->rules('required'),
			Text::make('Amount')
				->rules('required'),
			Text::make('From Wallet')
				->default($wallet->wallet)
				->readonly()
				->rules('required'),
			Text::make('Recipient')
				->rules('required'),
			Text::make('Pincode')
				->rules('required', 'max:4', 'min:4'),
        ];
    }
}
