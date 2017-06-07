<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Cache;
use App\Configurations;
use App\Http\Helper\CommonHelper;
use Exception;

class StoreLegalAddress extends Model
{
    /**
     *
     * @var table name 
     */
    protected $table = "vendor_legal_address";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'address_line_1','address_line_2', 'city', 'region', 'pin', 'country', 'nationality', 'country_residence'
    ];
    
    /**
     * One to one association with user
     * 
     * @package StoreLegalAddress Model
     * @return App\User
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'fk_users_id');
    }
}
