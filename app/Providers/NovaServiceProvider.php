<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\Metrics\Profit;
use App\Nova\Metrics\Deposit;
use App\Nova\Metrics\Withdraw;
use App\Nova\Metrics\DepositTrend;
use App\Nova\Metrics\WithdrawTrend;
use App\Nova\Metrics\TransactionsPart;
use App\Nova\Metrics\DepositsPart;
use App\Nova\Metrics\WithdrawsPart;
use App\Nova\Metrics\CurrencyPart;
use App\Nova\Metrics\GamePart;
use App\Nova\Metrics\TransactionsStat;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            Profit::make()->defaultRange('30'),
			Deposit::make()->defaultRange('30'),
			Withdraw::make()->defaultRange('30'),
			DepositTrend::make()->defaultRange('30')->width('1/2'),
			WithdrawTrend::make()->defaultRange('30')->width('1/2'),
			TransactionsPart::make()->width('1/3'),
			TransactionsStat::make()->defaultRange('30'),
			WithdrawsPart::make()->width('1/3'),
			DepositsPart::make()->width('1/3'),
			CurrencyPart::make()->width('1/3'),
			GamePart::make()->width('1/3'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
