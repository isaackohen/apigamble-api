<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class GameOptions extends Model
{
        public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $table = 'GameOptions';
	 
    protected $fillable = [
        'id', 'apikey', 'operator', 'operatorurl', 'livecasino_prefix', 'slots_prefix', 'evoplay_prefix', 'poker_prefix', 'bankgroup', 'bankgroupeur', 'bonusbankgroup', 'bonusgroupeur', 'callbackurl', 'sessiondomain', 'statichost', 'ggr', 'created_at', 'updated_at', 'newevoplay', 'livecasino_enabled'
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
}
