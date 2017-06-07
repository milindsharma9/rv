<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Cache;
use App\Configurations;
use App\Http\Helper\CommonHelper;
use Exception;
use Log;

class StoreAddress extends Model {

    /**
     *
     * @var table name 
     */
    protected $table = "store_address";
    protected $currentDay = null;
    protected $lat = null;
    protected $lng = null;
    protected $postCode = null;
    protected $nearByRadius = 2;

    const EMPTY_STRING = 'EMPTY';
    const CACHE_TIME_LIMIT = '1440'; // In Minutes i.e 24 * 60

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'fk_users_id', 'address', 'city', 'state', 'pin', 'lat', 'lng'
    ];

    /**
     *
     * Method to get stores in given radius which are open and has closeTime > Now + x mins.
     *
     * @param decimal $lat Latitite of current user
     * @param decimal $lng Longitude of current user
     * @param string $postCode Psotcode eneterd by current user
     *
     * @return array $aNearByStore Near Stores.
     *
     */
    public function getNearbyStores($lat, $lng, $postCode) {
        $aNearByStoresId = array();
        try {
            $this->currentDay = date("l");
            $this->postCode = $postCode;
            $this->lat = $lat;
            $this->lng = $lng;
            $configModel = new Configurations();
            $minOpenTime = $configModel->get(\Config::get('configurations.min_open_time_left_key'));
            $nearByKm = $configModel->get(\Config::get('configurations.search_radius_key'));
            $this->nearByRadius = $nearByKm;
            //$minOpenTime                = 30; // in minutes
            $stores = $this->_getNearbyStores();
            $currentDateTime = date('Y-m-d H:i:s');
            $aCurrent = explode(" ", $currentDateTime);
            $currentTime = $aCurrent[1];
            $currentTime = strtotime($currentTime);
            //$startTime = date("H:i", strtotime('-30 minutes', $time));
            $currentTimeAfterBuffer = date("H:i:s", strtotime('+' . $minOpenTime . ' minutes', $currentTime));
            foreach ($stores as $store) {
                $storeId = $store->fk_users_id;
                $storeOpenTime = $store->open_time;
                $storeCloseTime = $store->close_time;
                $isClose = $store->is_closed;
                $is24 = $store->is_24hrs;
                if ($is24 || ($isClose == 0 && strtotime($storeOpenTime) <= strtotime($currentTimeAfterBuffer) && strtotime($storeCloseTime) > strtotime($currentTimeAfterBuffer)
                        )
                ) {
                    array_push($aNearByStoresId, $storeId);
                }
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::DELIVERY_POSTCODE_LOG_FILE, CommonHelper::DAILY);
        }
        return $aNearByStoresId;
    }

    /**
     *
     * Protected Method to get near stores from Cache.
     * If Not set in cache it fetches from DB & sets in cache.
     *
     * @return array $aNearByStore Near Stores.
     *
     */
    protected function _getNearbyStores() {
        $aNearByStore = $this->_getNearbyStoresFromCache();
        if ($aNearByStore == self::EMPTY_STRING) {
            $aNearByStore = $this->_getNearbyStoresFromStorage();
            $this->_setNearbyStoresInCache($aNearByStore);
        }
        return $aNearByStore;
    }

    /**
     *
     * Method to get near stores from DB
     * @return array $aNearByStore Near Stores fetched from DB.
     *
     */
    protected function _getNearbyStoresFromStorage() {
        $nearByKm = $this->nearByRadius; // in KM
        $stores = DB::select(
                        DB::raw("SELECT sa.fk_users_id, st.day, st.is_closed, "
                                . "is_24hrs, st.open_time, st.close_time, "
                                . "( 6371 * acos( cos( radians(:userlat) ) * cos( radians( lat ) ) "
                                . "* cos( radians( lng ) - radians(:userlng) ) + sin( radians(:userlat1) ) "
                                . "* sin( radians( lat ) ) ) ) AS distance, count(ps.fk_product_id) FROM store_address sa, "
                                . "timings st , sub_store_details ssd, users u, products_store ps where u.activated = 1 "
                                . "and ssd.store_status = 1 and u.id = sa.fk_users_id "
                                . "and ssd.fk_users_id = sa.fk_users_id and st.day = DAYNAME(NOW()) and "
                                . "st.fk_user_id = sa.fk_users_id and (st.is_24hrs = 1 || (st.is_closed = 0 )) "
                                . "and ps.fk_user_id = st.fk_user_id group by st.fk_user_id "
                                . "HAVING distance <= :distance and count(ps.fk_product_id) >= 1 ORDER BY distance limit 2"),
                        array(
                    'userlat' => $this->lat,
                    'userlat1' => $this->lat,
                    'userlng' => $this->lng,
                    'distance' => $nearByKm,
                        )
        );
        $storesRefined = array();
        // Fetch all timings of store for a particular day. (as we now have multiple open times of store)
        if (!empty($stores)) {
            $aNearStoreId = array();
            $aNearStoreDistance = array();
            foreach ($stores as $storeSingle) {
                array_push($aNearStoreId, $storeSingle->fk_users_id);
                $aNearStoreDistance[$storeSingle->fk_users_id] = $storeSingle->distance;
            }
            $storesRefined = DB::table('timings')
                    ->addSelect('fk_user_id as fk_users_id', 'day', 'is_closed','is_24hrs', 'open_time', 'close_time')
                    ->whereIn('fk_user_id', $aNearStoreId)
                    ->where('day', '=', DB::raw('DAYNAME(NOW())'))
                    ->get();
            foreach ($storesRefined as $storeRefinedSingle) {
                $storeRefinedSingle->distance = $aNearStoreDistance[$storeRefinedSingle->fk_users_id];
            }
        }
        return $storesRefined;
    }

    /**
     *
     * Method to get near stores from cache
     * @return array $aNearByStore Near Stores fetched from cache.
     *
     */
    protected function _getNearbyStoresFromCache() {
        $aNearByStore = Cache::get('near_stores_' . $this->currentDay . $this->postCode, self::EMPTY_STRING);
        return $aNearByStore;
    }

    /**
     *
     * Method to set near stores in cache
     * @param array $aNearByStore Near Stores fetched frm DB.
     *
     */
    protected function _setNearbyStoresInCache($aNearByStore) {
        Cache::put('near_stores_' . $this->currentDay . $this->postCode, $aNearByStore, self::CACHE_TIME_LIMIT);
    }

    /**
     *
     * Method to get site opening timings.
     *
     * @return array $open_Time.
     *
     */
    public function checkSiteTimings() {
        $site_time = array();
        $open_Time = array();
        try {
            $this->currentDay = date("l");
            $configModel = new Configurations();
            $currentTime = date('H:i:s');            
            $site_timeS = $this->_getSiteTimings();
            $storeId = $site_timeS[0]->fk_user_id;
            foreach ($site_timeS as $key => $site_time) { 
                $siteOpenTime = $site_time->open_time;
                $siteCloseTime = $site_time->close_time;
                $isClose = $site_time->is_closed;
                $is24 = $site_time->is_24hrs;
                if ($is24 || ($isClose == 0 && strtotime($siteOpenTime) < strtotime($currentTime) && strtotime($siteCloseTime) > strtotime($currentTime)))
                  {
                    array_push($open_Time, $storeId);
                  }
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::DELIVERY_POSTCODE_LOG_FILE, CommonHelper::DAILY);
        }
        return $open_Time;
    }

    /**
     * Method to site opening time from cache.
     * If cache exist the pick up from cache otherwise fetch from db
     *
     * @return $SiteTimings
     *
     */
    public function _getSiteTimings() {
    //        $SiteTimings= $this->_getSiteTimingsFromCache();
    //        if ($SiteTimings == self::EMPTY_STRING) {
            $SiteTimings = $this->_getSiteTimingsFromStorage();
    //            $this->_setSiteTimingsInCache($SiteTimings);
    //        }
       
        return $SiteTimings;
    }

    /**
     *
     * Method to get site opening time from DB
     * @return array $SiteTimings site opening time fetched from DB.
     *
     */
    public function _getSiteTimingsFromStorage() {
        try {
            $SiteTimings = DB::select(
                            DB::raw("SELECT  st.fk_user_id, st.day, st.is_closed, "
                                    . "is_24hrs, st.open_time, st.close_time "
                                    . "FROM timings st where st.day = DAYNAME(NOW()) and "
                                    . "st.fk_user_id = :admin_id"), // @todo Change > to < (Done currently to fetch results)
                            array(
                        'admin_id' => config('appConstants.admin_role_id')
                            )
            );
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        return $SiteTimings;
    }

    /**
     *
     * Method to get site opening time from cache
     * @return array $siteOpenTime site opening time  fetched from cache.
     *
     */
    public function _getSiteTimingsFromCache() {
        $siteOpenTime = Cache::get('Open_Time_' . $this->currentDay, self::EMPTY_STRING);
        return $siteOpenTime;
    }

    /**
     *
     * Method to set site opening time  in cache
     * @param array $siteOpenTime site opening time  fetched frm DB.
     *
     */
    public function _setSiteTimingsInCache($siteOpenTime) {
        Cache::put('Open_Time_' . $this->currentDay, $siteOpenTime, self::CACHE_TIME_LIMIT);
    }
    
    /**
     * Function to create a address for newly added store.
     * 
     * @param Array $data
     * @return Int
     */
    public function createStoreAddress($data) {
        try {
            $postCode = $data['pin'];
            $commonModel = new CommonHelper();
            $latlngResponse = $commonModel->getLatLngFromPostCode($postCode);
            if ($latlngResponse['status']) {
                $data['lat'] = $latlngResponse['data']['lat'];
                $data['lng'] = $latlngResponse['data']['lng'];
            }
            return StoreAddress::create([
                        'fk_users_id' => isset($data['id_user']) ? $data['id_user'] : '',
                        'address' => isset($data['address']) ? $data['address'] : '',
                        'city' => isset($data['city']) ? $data['city'] : '',
                        'state' => isset($data['state']) ? $data['state'] : '',
                        'pin' => isset($data['pin']) ? $data['pin'] : '',
                        'lat' => isset($data['lat']) ? $data['lat'] : '',
                        'lng' => isset($data['lng']) ? $data['lng'] : '',
            ]);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     *
     * Method to get stores in given radius which are open and has closeTime > Now + x mins.
     *
     * @param decimal $lat Latitite of current user
     * @param decimal $lng Longitude of current user
     * @param string $postCode Psotcode eneterd by current user
     *
     * @return array $aNearByStore Near Stores details
     *
     */
    public function getNearbyStoresWithDistance($lat, $lng, $postCode) {
        $aNearByStoresId = array();
        try {
            $this->currentDay   = date("l");
            $this->postCode     = $postCode;
            $this->lat          = $lat;
            $this->lng          = $lng;
            $configModel        = new Configurations();
            $minOpenTime        = $configModel->get(config('configurations.min_open_time_left_key'));
            $nearByKm           = $configModel->get(config('configurations.search_radius_key'));
            $this->nearByRadius = $nearByKm;
            $stores             = $this->_getNearbyStores();
            $currentDateTime    = date('Y-m-d H:i:s');
            $aCurrent           = explode(" ", $currentDateTime);
            $currentTime        = $aCurrent[1];
            $currentTime        = strtotime($currentTime);
            $currentTimeAfterBuffer = date("H:i:s", strtotime('+' . $minOpenTime . ' minutes', $currentTime));
            foreach ($stores as $store) {
                $storeId            = $store->fk_users_id;
                $storeOpenTime      = $store->open_time;
                $storeCloseTime     = $store->close_time;
                $isClose            = $store->is_closed;
                $is24               = $store->is_24hrs;
                $distance           = $store->distance;
                if ($is24 || ($isClose == 0 && strtotime($storeOpenTime) <= strtotime($currentTimeAfterBuffer) && strtotime($storeCloseTime) > strtotime($currentTimeAfterBuffer)
                        )
                ) {
                    $aNearByStoresId[$storeId] = array(
                        'distance' => $distance
                    );
                }
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::DELIVERY_POSTCODE_LOG_FILE, CommonHelper::DAILY);
        }
        return $aNearByStoresId;
    }

}
