<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class Configurations extends Model
{

    /**
     *
     * @var table name 
     */
    protected $table = "configurations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value'
    ];

    /*
     * Have Structure Like following
     * array(
            'configurations' => 
                array(
                    'search_radius' => array(
                        'label' => 'Search Radius (In KM)',
                        'value' => 5, // in KM
                    ),
                    'min_open_time_left' => array(
                        'label' => 'Shop Min Open Time (In mins)',
                        'value' => 30, // In Mins
                    )
                )
            );
     */
    protected $aConfiguration;

    /**
     * Contructor Method. Prepares Configuration structured Array.
     * 
     */
    public function __construct() {
        $aConfiguration = array(
            'configurations' => 
                array(
                    \Config::get('configurations.search_radius_key') => array(
                        'label' => 'Search Radius (In KM)',
                        'value' => \Config::get('configurations.search_radius_default_value'), // in KM
                    ),
                    \Config::get('configurations.min_open_time_left_key') => array(
                        'label' => 'Shop Min Open Time (In mins)',
                        'value' => \Config::get('configurations.min_open_time_left_default_value'), // In Mins
                    ),                    
                    \Config::get('configurations.age_limit_msg_key') => array(
                        'label' => 'Age Limit Message',
                        'value' => \Config::get('configurations.age_limit_msg_default_value'), // In Mins
                    ),
                    \Config::get('configurations.open_time_msg_key') => array(
                        'label' => 'Offline Message',
                        'value' => \Config::get('configurations.open_time_msg_default_value'), // In Mins
                    ),
                    \Config::get('configurations.online_msg_key') => array(
                        'label' => 'Online Message',
                        'value' => \Config::get('configurations.online_msg_default_value'), // In Mins
                    ),
                    config('configurations.after_midnight_key') => array(
                        'label' => 'Midnight Delivery Charge',
                        'value' => config('configurations.after_midnight_default_value'),
                    ),
                    config('configurations.special_category_key') => array(
                        'label' => 'Special Category Delivery Charge',
                        'value' => config('configurations.special_category_default_value'),
                    ),
                    config('configurations.delivery_charge_key') => array(
                        'label' => 'Driver Delivery Charge',
                        'value' => config('configurations.delivery_charge_default_value'),
                    ),
                    config('configurations.threshold_delivery_key') => array(
                        'label' => 'Delivery Charge Threshold',
                        'value' => config('configurations.threshold_delivery_default_value'),
                    ),
                    config('configurations.threshold_min_basket_key') => array(
                        'label' => 'Min Order Threshold',
                        'value' => config('configurations.threshold_min_basket_default_value'),
                    ),
                    config('configurations.min_basket_price_key') => array(
                        'label' => 'Min Basket Charge',
                        'value' => config('configurations.min_basket_price_default_value'),
                    ),
                    config('configurations.gpsc_key') => array(
                        'label' => config('configurations.gpsc'),
                        'value' => config('configurations.gpsc_default_value'),
                    ),
                    config('configurations.gvpc_key') => array(
                        'label' =>  config('configurations.gvpc'),
                        'value' => config('configurations.gvpc_default_value'),
                    ),
                    config('configurations.apply_retailer_title_key') => array(
                        'label' => 'Apply Retailer Title',
                        'value' => config('configurations.apply_retailer_title_default_value'),
                    ),
                    config('configurations.apply_retailer_subtext_key') => array(
                        'label' => 'Apply Retailer Subtext',
                        'value' => config('configurations.apply_retailer_subtext_default_value'),
                    ),
                    config('configurations.apply_driver_title_key') => array(
                        'label' => 'Apply Driver Title',
                        'value' => config('configurations.apply_driver_title_default_value'),
                    ),
                    config('configurations.apply_driver_subtext_key') => array(
                        'label' => 'Apply Driver Subtext',
                        'value' => config('configurations.apply_driver_subtext_default_value'),
                    ),
                    config('configurations.mangopay_3dsecure_key') => array(
                        'label' => '3D Secure Value',
                        'value' => config('configurations.mangopay_3dsecure_default_value'),
                    ),
                    config('configurations.order_delivery_time_key') => array(
                        'label' => 'Estimated Delivery Time (mins)',
                        'value' => config('configurations.order_delivery_time_default_value'),
                    ),
                )
        );
        $this->aConfiguration = $aConfiguration;
    }

    /**
     * Method to save configuration in DB & cache as well.
     *
     *
     * @return array
     */
    public function saveConfigurations($confValues) {
        $response = array(
            'status'    => false,
            'message'   => ''
        );
        try {
            $aValidConfigurationKeys = array_keys($this->aConfiguration['configurations']);
            foreach ($confValues as $key => $value) {
                if (in_array($key, $aValidConfigurationKeys)) {
                    $this->set($key, $value);
                }
            }
            Cache::flush();
            $response['status'] = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Prepare Array of configuration whith label & default Values
     *
     *
     * @return array
     */
    public function getConfigurations() {
        $aConfiguration = $this->aConfiguration;
        foreach ($aConfiguration as $configuration) {
            foreach($configuration as $configurationKey => $configurationValues) {
                $configValue                        = $this->fetch($configurationKey);
                if (!empty($configValue)) {
                    $configurationValues['value']       = $configValue;
                    $configuration[$configurationKey]   = $configurationValues;
                }
            }
            $aConfiguration['configurations'] = $configuration;
        }
        return $aConfiguration;
    }

    /**
     * Store value into registry
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function set($key, $value)
    {
        $value = serialize($value);
        $setting = DB::table($this->table)->where('key', $key)->first();
        if (is_null($setting)) {
            DB::table($this->table)
                           ->insert(['key' => $key, 'value' => $value]);
        } else {
            DB::table($this->table)
                           ->where('key', $key)
                           ->update(['value' => $value]);
        }
        Cache::forever($key, unserialize($value));
        return $value;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function fetch($key)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        $row = DB::table($this->table)->where('key', $key)->first(['value']);
        $keyDefault = $key."_default_value";
        $keyDefaultValue = config('configurations.'.$keyDefault);
        //return (!is_null($row)) ? Cache::forever($key, unserialize($row->value)) : $keyDefaultValue;
        if ((!is_null($row))) {
            $keyDefaultValue = unserialize($row->value);
            Cache::forever($key, $keyDefaultValue);
        }
        return $keyDefaultValue;
    }

    /**
     * Public method to get Key Value
     *
     * @param string $key Key of configuration
     * @return mixed
     */
    public function get($key) {
        return self::fetch($key);
    }
        
}
