<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Blog;

class BlogMeta extends Model
{

    protected $table = "events_places_meta";
    
    /**
     * places_food_url used as Drink text in FE
     */
    
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['fk_master_blog_id', 'location', 'address', 'city', 
        'state','pin', 'event_ticket_text', 'event_ticket_url', 
        'places_drink_text', 'places_drink_url','places_food_text', 
        'places_food_url'];
    
    /**
     * One to one association with Blog
     * 
     * @package Blog Model
     * @return App\Blog
     */
    public function blog()
    {
        return $this->belongsTo('App\Blog', 'fk_master_blog_id');
    }

}
