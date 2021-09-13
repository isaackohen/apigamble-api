<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Support\Facades\DB;

use App\Sessions;
use App\Apikeys;
use App\Games;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'access', 'income', 'debt', 'paid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	protected $dates = ['created_at', 'updated_at', 'updated_check_at'];
	

    /* public function access() {
        $db = DB::table('users')->where('id', $this->id)->get();
        return $db->access;
    } */

    public function getApikeys() {
        return DB::table('Apikeys')->where('ownedBy', $this->id)->get();
    }

    public function wallets()
    {
        return $this->hasMany('App\Wallets');
    }	
	
    public function apikeys()
    {
        return $this->hasMany('App\Apikeys', 'ownedBy');
    }

}



