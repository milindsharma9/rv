<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Exception;
use Log;
use Cache;

/**
 * Occasion Model
 */
class Occasion extends Model {

    /**
     * Soft delete trait included to ensure soft delete is unable
     */
    use SoftDeletes;

    /**
     *
     * @var table name 
     */
    protected $table = "occasions";
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['name', 'image', 'image_logo', 'parent_id', 'is_banner', 'image_banner', 'sort_order', 'floating_text', 'sub_text'];
    protected $dates = ['deleted_at'];

    /**
     * Method to get occasions group by parentId
     * 
     * @package Occasion Model
     * @return array
     */
    public function getOccasionsByParentId() {
        try {
            $categories = DB::table('occasions')
                    ->select(DB::raw('group_concat(name SEPARATOR "||") as name, group_concat(id SEPARATOR "||") as id, parent_id'))
                    ->groupBy('parent_id')
                    ->whereNull('deleted_at')
                    ->get();
            $aResponse = array();
            foreach ($categories as $result) {
                $aResponse[$result->parent_id] = array(
                    'name' => explode('||', $result->name),
                    'id' => explode('||', $result->id)
                );
            }
            return $aResponse;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get Primary Occasion List
     * 
     * @package Occasion Model
     * @return Occasion mixed
     */
    public function getPrimaryOccasions() {
        try {
            $primaryOccasions = Occasion::where('parent_id', 0)->orderBy('name')->pluck('name', 'id');
            $primaryOccasions->prepend('Primary Occasion', 0);
            return $primaryOccasions;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get All Occasion List
     *
     * @package Occasion Model
     * @return Occasion Mixed
     */
    public function getOccasionsList() {
        try {

            $primaryOccasions = Occasion::orderBy('name')->pluck('name', 'id');
            $primaryOccasions->prepend('Primary Occasion', 0);
            return $primaryOccasions;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Return primary Occasion
     * 
     * @package Occasion Model
     * @return Occasion mixed
     */
    public function getPrimaryOccasionsForHomePage() {
        $primaryOccasions = array();
        try {
            $primaryOccasions = Occasion::where('parent_id', 0)->select('id', 'name', 'image', 'image_logo', 'is_banner')->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $primaryOccasions;
    }

    /**
     * Return sub categories of occasion.
     * 
     * @package Occasion Model
     * @param Integer $occasionId
     * @return Occasion mixed
     */
    public function getSubOccasions($occasionId) {
        $subOccasions = array();
        try {
            $subOccasions = Occasion::where('parent_id', $occasionId)->select('id', 'name')->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $subOccasions;
    }

    /**
     * Update Banner Image
     * 
     * @package Occasion Model
     * @return void
     */
    public function resetBannerImage() {
        try {
            Occasion::whereNull('deleted_at')
                    ->update(['is_banner' => 0]);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Return count od suboccasions.
     * 
     * @package Occasion Model
     * @param array $ids
     * @return Integer
     */
    public function hasSubOccasions(array $ids) {
        try {

            $count = Occasion::whereIn('parent_id', $ids)->count();
            return $count;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Return Occasion Details by child 
     * 
     * @package Occasion Model
     * @param integer $id
     * @return Event mixed
     */
    public function getOccasionDetailByChildId($id) {
        try {
            $event = DB::select(DB::raw("select evp.id, evp.name, evp.image, evp.image_logo, evp.image_banner"
                    . ", ev.name as cname, ev.image_banner as cimage_banner"
                    . ", ev.floating_text as cfloating_text, ev.sub_text as csub_text, ev.image_logo as cimage_logo from occasions ev, occasions evp where ev.parent_id = evp.id and ev.id= :id"), array(
                        'id' => $id,
            ));
            return $event;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Return Primary Occasion Detail
     * 
     * @package Occasion Model
     * @param Integer $limit
     * @param Boolean $paginationRequired
     * @return Occasion mixed.
     */
    public function getPrimaryOccasionsDetail($limit = 5, $paginationRequired = TRUE) {
        try {
            $primaryOccasions = Occasion::where('parent_id', 0)
                    ->select('id', 'name', 'image', 'image_logo', 'is_banner')
                    ->orderBy('id');
            if ($paginationRequired) {
                $primaryOccasions = $primaryOccasions->paginate($limit);
            } else {
                $primaryOccasions = $primaryOccasions->take($limit)->get();
            }
            return $primaryOccasions;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get occasions detail by Id
     * 
     * @package Occasion Model
     * @param integer $id
     * @return Occasion mixed
     */
    public function getPrimaryOccasionsDetailById($id) {
        try {
            $primaryOccasions = Occasion::select('id', 'name', 'image', 'image_logo', 'image_banner', 'is_banner')
                    ->where('id', '=', $id)
                    ->where('parent_id', '=', 0)
                    ->get();
            return $primaryOccasions;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Public function to Fetch Occasion Tree
     * 
     * @return array $occassionTree
     */
    public function getOccassionTree() {
        $occassionTree = array();
        try {
            $occassionTree = $this->_getOccassionTreeCache();
            if (empty($occassionTree)) {
                $occassionTree = $this->_getOccassionTree();
                $this->_setOccassionTreeCache($occassionTree);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $occassionTree;
    }

    /**
     * Private function to Fetch Occassion Tree from cache
     * 
     * @return array $occassionTree
     */
    private function _getOccassionTreeCache() {
        $occassionTree = array();
        try {
            $occassionTree = Cache::get('occasion_tree', array());
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $occassionTree;
    }

    /**
     * Private function to set Occassion Tree in cache 
     *
     * @param array $occassionTree
     * @return void
     *
     */
    private function _setOccassionTreeCache($occassionTree) {
        try {
            Cache::forever('occasion_tree', $occassionTree);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Private function to get Occassion Tree from DB
     * 
     * @return array
     */
    private function _getOccassionTree() {
        $aOccasionTree = array();
        try {
            $parentOccasions = DB::table('occasions')
                    ->select('id', 'name', 'image', 'image_logo', 'is_banner', 'image_banner')
                    ->where('parent_id', 0)
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order')
                    ->get();
            foreach ($parentOccasions as $parentOccasion) {
                $parentOccassionId          = $parentOccasion->id;
                $parentOccassionName        = $parentOccasion->name;
                $parentOccassionImage       = $parentOccasion->image;
                $parentOccassionImageLogo   = $parentOccasion->image_logo;
                $parentOccassionIsBanner    = $parentOccasion->is_banner;
                $parentOccassionImageBanner = $parentOccasion->image_banner;
                $subOccasions = DB::table('occasions')
                        ->select('id', 'name', 'image', 'image_logo')
                        ->where('parent_id', $parentOccassionId)
                        ->whereNull('deleted_at')
                        ->get();
                $aSubOccasion = array();
                foreach ($subOccasions as $subOccasion) {
                    $subOccasionId   = $subOccasion->id;
                    $subOccasionName = $subOccasion->name;
                    $aSubOccasion[$subOccasionId] = array(
                        'id' => $subOccasionId,
                        'name' => $subOccasionName,
                        'image'         => $subOccasion->image,
                        'image_logo'    => $subOccasion->image_logo,
                    );
                }
                $aOccasionTree[$parentOccassionId] = array(
                    'id'            => $parentOccassionId,
                    'name'          => $parentOccassionName,
                    'image'         => $parentOccassionImage,
                    'image_logo'    => $parentOccassionImageLogo,
                    'image_banner'  => $parentOccassionImageBanner,
                    'is_banner'     => $parentOccassionIsBanner,
                    'subOccasions'  => $aSubOccasion
                );
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aOccasionTree;
    }

    /**
     * Clear Occasion Cache.
     * 
     * @return void
     */
    public function clearOccasionCache() {
        try {
            Cache::forget('occasion_tree');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

}
