<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Exception;
use Log;

/**
 * StoreDetails Model
 */
class SubStoreDetails extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_name', 'fk_users_id', 'fk_parent_id', 'store_status'
    ];
    
    /**
     * One to one association with user
     * 
     * @package StoreDetails Model
     * @return App\User
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'fk_users_id');
    }
    
    /**
     * Function to create a new store for a existing vendor.
     * 
     * @param Array $data
     * @return Int
     */
    public function createSubStore($data) {
        try {
            $insertData = [
                'store_name'    => isset($data['name']) ? $data['name'] : isset($data['store_name']) ? $data['store_name'] : '',
                'fk_users_id'   => $data['id_user'],
            ];
            if (isset($data['fk_parent_id'])) {
                $insertData['fk_parent_id'] = $data['fk_parent_id'];
            }
            return SubStoreDetails::create($insertData);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * 
     */
    public function getStoreSubStores($parentStoreId, $includeParent = true, $getInactive = false) {
        $aSubStores =  SubStoreDetails::where('fk_parent_id', $parentStoreId);
        if ($includeParent) {
            $aSubStores = $aSubStores->orWhere('fk_users_id', $parentStoreId);
        }
        if (!$getInactive) {
            $aSubStores = $aSubStores->where('store_status', 1);
        }
        $aSubStores = $aSubStores->orderBy('id', 'asc')->get();
        if (!empty($aSubStores)) {
            $aSubStores = $aSubStores->toArray();
        }
        return $aSubStores;
    }
    
    /**
     * Method to return store ParentId for child store & own id in case of parent store.
     * 
     * @param Int $storeUserId
     * @return object
     */
    public function getParentId($storeUserId) {
        $parentId = SubStoreDetails::select('fk_parent_id')->where('fk_users_id', $storeUserId)->firstOrFail();
        return isset($parentId['fk_parent_id'])? empty($parentId['fk_parent_id'])? $storeUserId : $parentId['fk_parent_id']: $storeUserId;
    }
}
