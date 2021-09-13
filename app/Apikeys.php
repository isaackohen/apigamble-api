<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Apikeys extends Model
{
	
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'Apikeys';
	 
    protected $fillable = [
        'id', 'apikey', 'operator', 'operatorurl', 'bankgroup', 'bankgroupeur', 'bonusgroup', 'bonusgroupeur', 'callbackurl', 'sessiondomain', 'ownedBy', 'type', 'statichost', 'ggr', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
	
	
    public function user()
    {
        return $this->belongsTo('App\User', 'ownedBy');
    }
	
	public function gameoptions()
    {
        return $this->hasMany('App\GameOptions', 'apikey', 'apikey');
    }

	public function creditcardoptions()
    {
        return $this->hasMany('App\CreditcardOptions', 'apikey', 'apikey');
    }

	public function paymentoptions()
    {
        return $this->hasMany('App\PaymentOptions', 'apikey', 'apikey');
    }
	
	public function save(array $options = array()) {
		parent::save($options);
		if($this->type == 'paykey') {
			$cryptos = array('xblzd', 'eth', 'ltc', 'btc', 'bsc', 'trx', 'bch', 'doge', 'game1');
			foreach ($cryptos as $crypto) {
				$key = new PaymentOptions();
				$key->crypto = $crypto; 
				$key->apikey = $this->apikey; 
				$key->masterpass = rand(0001, 9998); 
				$key->balance = '0'; 
				$key->callbackurl = 'example.com';
				$key->save();
			}
		}
		if($this->type == 'creditcard') {
			$key = new CreditcardOptions();
			$key->apikey = $this->apikey;
			$key->forward_enabled = '0';
			$key->forward_minimum = '50';
			$key->forward_address = 'BUSD BEP-20';
			$key->balance = '0';
			$key->callbackurl = 'https://example.com/callback';
			$key->save();
		}
		if($this->type == 'slots') {
			$key = new GameOptions();
			$key->apikey = $this->apikey;
			$key->operator = 'example';
			$key->operatorurl = 'example';
			$key->bankgroup = 'usdbank';
			$key->bankgroupeur = 'eurbank';
			$key->bonusbankgroup = 'usdbank_bonus';
			$key->bonusbankgroupeur = 'eurbank_bonus';
			$key->callbackurl = 'example';
			$key->livecasino_prefix = 'livecasino';
			$key->slots_prefix = 'slots';
			$key->evoplay_prefix = 'evoplay';
			$key->poker_prefix = 'poker';
			$key->sessiondomain = '.gambleapi.com';
			$key->statichost = 'static.gambleapi.com';
			$key->ggr = '10';
			$key->save();
		}
		if($this->type == 'sports') {
			$key = new SportOptions();
			$key->apikey = $this->apikey;
			$key->operator = 'example';
			$key->operatorurl = 'example';
			$key->callbackurl = 'example';
			$key->save();
		}
	}
	
	public function delete(array $options = array())
    {
		parent::delete($options);
		if($this->type == 'paykey') {
			PaymentOptions::where('apikey', '=', $this->apikey)->delete();
		}
		if($this->type == 'slots') {
			GameOptions::where('apikey', '=', $this->apikey)->delete();
		}
		if($this->type == 'creditcard') {
			CreditcardOptions::where('apikey', '=', $this->apikey)->delete();
		}
		if($this->type == 'sports') {
			SportOptions::where('apikey', '=', $this->apikey)->delete();
		}
    }
	
}






