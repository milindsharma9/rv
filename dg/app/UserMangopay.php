<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
/**
 * UserMangopay Model
 */
class UserMangopay extends Authenticatable {

    protected $table = "user_mangopay";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'mango_users_id', 'mango_users_wallet_id'
    ];

    /**
     * One to one association with user
     * 
     * @package UserMangopay Model
     * @return App\User
     */
    public function user() {
        return $this->belongsTo('App\User', 'fk_users_id');
    }

}
