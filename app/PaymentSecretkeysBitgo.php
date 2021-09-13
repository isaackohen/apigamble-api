<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PaymentSecretkeysBitgo extends Model
{
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'PaymentSecretkeysBitgo';
	 
    protected $fillable = [
        'id', 'currency', 'bitgo_currencyid', 'bitgo_apikey', 'bitgo_masterpass', 'bitgo_walletid', 'apikey', 'callbackurl' 'enabled'
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
	
    protected $dates = ['created_at', 'updated_at'];
	

 

	
}






