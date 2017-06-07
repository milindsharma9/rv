<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
/**
 * UserAddress Model
 */
class UserAddress extends Authenticatable
{

    protected $table = "user_address";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'address', 'city', 'state', 'pin'
    ];
    
    /**
     * One to one association with user
     * 
     * @package UserAddress Model
     * @return App\User
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'fk_users_id');
    }
}
