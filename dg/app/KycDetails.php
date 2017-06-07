<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KycDetails extends Model
{
    protected $table = "user_kyc_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'document_id', 'type', 'image', 'status'
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
