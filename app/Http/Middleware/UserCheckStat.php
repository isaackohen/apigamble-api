<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Carbon\Carbon;
use App\Providers;
use App\Games;
use App\Apikeys;
use App\GameOptions;
use App\withdrawAndDeposit;
use Illuminate\Support\Arr;

class UserCheckStat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {	
		/* $user = User::where('id', $request->user()->id)->first();
		$userUpdate = $user->updated_check_at;
		$checkDebt = Carbon::now()->subDays(3);
		if($userUpdate == null) {
			$user->updated_check_at = Carbon::now()->subDays(3);
			$user->save();
		} else if($userUpdate->lt($checkDebt)) {
			$apidata = Apikeys::where('ownedBy', $user->id)->where('type', 'slots')->get();
			$apioperator = [];
			foreach($apidata as $data) {
				$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
			}
			$transactions = withdrawAndDeposit::whereIn('operator', $apioperator)->whereDate('created_at', '>=', $userUpdate)->get();
			if($transactions !== null){
				$stats = [];
				$providers = [];
				ini_set('max_execution_time', '120'); 
				foreach($transactions as $transaction) {
					$provider_name = Games::where('gameid', $transaction->gameid)->with('provider')->first();
					if($provider_name->provider == null) continue;
					$provider_revenue = $provider_name->provider->revenue; //%
					if(!array_key_exists($provider_name->provider->name, $stats)) {
						array_push($providers, $provider_name->provider->name);
						$data = [
							$provider_name->provider->name  => [
								'withdraw' => 0,
								'deposit' => 0,
								'revenue' => 0,
								'free' => 0,
								'count' => 0
							]
						];
						$stats += $data;
					}
					if($transaction->withdraw > 0) {
						$stats[$provider_name->provider->name]['withdraw'] += $transaction->withdraw;
					}
					if($transaction->deposit > 0) {
						$stats[$provider_name->provider->name]['deposit'] += $transaction->deposit; 
					}
					if($transaction->deposit == 0 && $transaction->withdraw == 0) {
						$stats[$provider_name->provider->name]['free']++;
					}
					$stats[$provider_name->provider->name]['revenue'] = $provider_revenue;
					$stats[$provider_name->provider->name]['count']++;
				}
				$income = 0;
				$debt = 0;
				foreach($providers as $provider) {
					$incomes = ($stats[$provider]['withdraw'] - $stats[$provider]['deposit']) / 100;
					$debts = ($incomes / 100) * $stats[$provider]['revenue'];
					$debt += $debts;
					$income += $incomes;
				}
				$avg = GameOptions::whereIn('operator', $apioperator)->avg('ggr');
				$user->income += $income;
				$debit = (($income / 100) * $avg) + $debt;
				$user->debt += $debit;
				$user->updated_check_at = Carbon::now();
				$user->save();
			}
		} */ 
        return $next($request);
    }
}
