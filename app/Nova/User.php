<?php

namespace App\Nova;

use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\NewUsers;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
	 
	public static $group = "Main settings"; 
	 
	 
	public static function indexQuery(NovaRequest $request, $query)
    {
		if($request->user()->access != 'administrator') {
			return $query->where('id', $request->user()->id);
		} else {
			return $query;
		}
    }
	 

    public static function label()
    {
        return 'User';
    }


    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
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

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
				}),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
				->readonly(function ($request) {
					return $request->user()->access != "administrator";
				}),
			
			AdvancedNumber::make('Income')
				->sortable()
                ->rules('numeric', 'required')
				->prefix('$')
				->decimals(0)
				->readonly(),
				
			AdvancedNumber::make('Duty', 'debt')
                ->sortable()
                ->rules('numeric', 'required')
				->prefix('$')
				->decimals(0)
				->readonly(),
				
			AdvancedNumber::make('Paid')
                ->sortable()
                ->rules('numeric', 'required')
				->prefix('$')
				->decimals(0)
				->readonly(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),
				
			Select::make('Access')->options([
				'administrator' => 'Administrator',
				'operator' => 'Operator',
				])
				->canSee(function ($request) {
                    return $request->user()->access == "administrator";
                }),
			
			HasMany::make(__('Api Keys'), 'apikeys', Apikeys::class),
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
			(new NewUsers)->canSee(function ($request) {
				return $request->user()->access == "administrator";
			})->width('1/2'),
			(new UsersPerDay)->canSee(function ($request) {
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
        return [
			(new Actions\AddPaidAmount())
			->showOnTableRow()
			->showOnDetail()
			->canSee(function ($request) {
				return $request->user()->access == "administrator";
			})
		];
    }
}
