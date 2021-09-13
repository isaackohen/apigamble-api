<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CreditcardOptions extends Model
{
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'CreditcardOptions';
     
    protected $fillable = [
        'id', 'apikey', 'forward_enabled', 'forward_minimum', 'forward_address', 'balance', 'callbackurl'
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
    
    public function apikey()
    {
        return $this->belongsTo('App\Apikeys', 'apikey', 'apikey');
    }

}  

