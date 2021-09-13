<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CallbackLog extends Model
{
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'CallbackLog';
	 
    protected $fillable = [
        'id', 'extid', 'callbackurl', 'callbackstate', 'callbacktries', 'finished', 'amount', 'address', 'callbackurl', 'subscribed'
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






