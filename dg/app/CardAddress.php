<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
//use App\User;

class CardAddress extends Authenticatable
{

    protected $table = "card_address";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'city', 'state', 'pin', 'fk_users_id'
    ];
    
    /**
     * One to one association with user
     *
     * @return Object User
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'fk_users_id');
    }
}
