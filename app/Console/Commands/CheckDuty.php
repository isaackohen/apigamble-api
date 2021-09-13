<?php

namespace App\Console\Commands;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\User;
use App\Providers;
use App\Games;
use App\Apikeys;
use App\GameOptions;
use App\withdrawAndDeposit;

class CheckDuty extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:duty {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user duty';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
	 
	public static function duty($user)
	{
		$checkDebt = Carbon::now()->subDays(2);
		$apidata = Apikeys::where('ownedBy', $user->id)->where('type', 'slots')->get();
		$apioperator = [];
		foreach($apidata as $data) {
			$apioperator[] = GameOptions::where('apikey', $data->apikey)->first()->operator;
		}
		$transactions = withdrawAndDeposit::whereIn('operator', $apioperator)->whereDate('created_at', '>=', $checkDebt )->get();
		if($transactions !== null){
			$stats = [];
			$providers = [];
			ini_set('max_execution_time', '120'); 
			foreach($transactions as $transaction) {
				$provider_name = Games::where('gameid', $transaction->gameid)->with('provider')->first();
				if($provider_name == null) return;
				if($provider_name->provider == null) return;
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
			Log::info('User: '.$user->name.' | Debit: '.$debit.' | Income: '.$income);
			$user->debt += $debit;
			$user->updated_check_at = Carbon::now();
			$user->save();
		}
	}


    public function handle() {
		if(strtolower($this->argument('user')) === 'all') {
            $users = User::all();
			foreach ($users as $user) { 
				self::duty($user);
				$this->info('Started calculating Duty for user '.$user->name);
			}
            return;
        }
		
		$user = User::where('name', $this->argument('user'))->first();

        if($user == null) {
            $this->error('Unknown user');
            return;
        }
		self::duty($user);
		
        $this->info('Started calculating Duty for user '.$this->argument('user'));
    }
}
