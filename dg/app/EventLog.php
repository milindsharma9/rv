<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\Http\Helper\CommonHelper;

class EventLog extends Model
{
    const EVENT_FAQ_ADD         = 'faq_add';
    const EVENT_FAQ_UPDATE      = 'faq_update';
    const EVENT_FAQ_DELETE      = 'faq_delete';
    const EVENT_FAQ_MASS_DELETE = 'faq_mass_delete';
    
    const EVENT_CATEGORY_UPDATE = 'category_update';
    const EVENT_CATEGORY_DELETE = 'category_delete';
    
    const EVENT_CMS_UPDATE = 'cms_update';
    const EVENT_CONTACTUS_UPDATE = 'contactus_update';
    const EVENT_CMS_ADD     = 'cms_add';
    
    const EVENT_DRIVER_ADD         = 'driver_add';
    const EVENT_DRIVER_UPDATE      = 'driver_update';
    const EVENT_DRIVER_DELETE      = 'driver_delete';
    const EVENT_DRIVER_MASS_DELETE = 'driver_mass_delete';
    
    const EVENT_THEME_ADD         = 'theme_add';
    const EVENT_THEME_UPDATE      = 'theme_update';
    const EVENT_THEME_DELETE      = 'theme_delete';
    const EVENT_THEME_MASS_DELETE = 'theme_mass_delete';
    
    const EVENT_OCCASSION_UPDATE      = 'occassion_update';
    const EVENT_OCCASSION_DELETE      = 'occassion_delete';
    const EVENT_OCCASSION_MASS_DELETE = 'occassion_mass_delete';
    
    const EVENT_POSTCODE_ADD         = 'postcode_add';
    const EVENT_POSTCODE_UPDATE      = 'postcode_update';
    const EVENT_POSTCODE_DELETE      = 'postcode_delete';
    const EVENT_POSTCODE_MASS_DELETE = 'postcode_mass_delete';
    
    const EVENT_VENDOR_UPDATE       = 'vendor_update';
    const EVENT_UPDATE_STORE_STATUS = 'update_store_status';
    
    const EVENT_CHANGE_PASSWORD         = 'change_password';
    const EVENT_STORE_PRODUCT_CHANGE    = 'storeprodct_add_remove';
    
    const EVENT_ORDER_STATUS_CHANGE     = 'order_status_changed';
    
    const EVENT_BLOG_ADD                = 'blog_add';
    const EVENT_BLOG_UPDATE             = 'blog_update';
    
    
    /**
     *
     * @var table name 
     */
    protected $table = "al_event_log";
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['users_id', 'created_at', 'operation_type', 'ip_address', 'al_event'];
    
    public static function logEvent($aLogData) {
        /*$aLogData = array(
            'users_id' => Auth::user()->id,
            'created_at' => CommonHelper::getCurrentDateTime(),
            'operation_type' => 'category_update',
            'ip_address' => CommonHelper::get_ip(),
            'al_event' => serialize(DB::getQueryLog()),
        );*/
        
        $aLogData['created_at'] = CommonHelper::getCurrentDateTime();
        $aLogData['ip_address'] = CommonHelper::get_ip();
        DB::table('al_event_log')->insert($aLogData);
    }
}
