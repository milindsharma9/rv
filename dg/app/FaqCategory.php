<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class FaqCategory extends Model
{
     protected $table = "faqs_category";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['category_name'];

}
