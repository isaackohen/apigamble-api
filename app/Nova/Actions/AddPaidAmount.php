<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\ActionFields;

class AddPaidAmount extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
	 
	public $name = 'Amount Paid'; 
	 
    public function handle(ActionFields $fields, Collection $models)
    {
		$user = $models->first();
		$paid = $user->paid;
		$amount = $fields->amount + $paid;
        $models->first()->update(['paid' => $amount]);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
			Text::make('Amount'),
		];
    }
}
