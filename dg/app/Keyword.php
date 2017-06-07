<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Keyword extends Model
{
    protected $table = "keyword";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['name', 'machine_name'];

    public static function getKeywordsForListing() {
        $aKeywords = Keyword::orderBy('name')->pluck('name', 'machine_name')->take(8);
        return $aKeywords;
    }
}
