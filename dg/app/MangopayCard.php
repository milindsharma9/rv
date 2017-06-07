<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

//use App\User;

class MangopayCard extends Authenticatable {

    protected $table = "mangopay_card_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'fk_card_addres_id', 'mango_users_card_id', 'is_default'
    ];

    /**
     * One to one association with user
     *
     * @return object
     */
    public function user() {
        return $this->belongsTo('App\User', 'fk_users_id');
    }

}
