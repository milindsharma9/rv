<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Exception;
use Log;
use Cache;

/**
 * Event Model
 */
class Event extends Model {

    /**
     * Soft delete trait included to ensure soft delete is unable
     */
    use SoftDeletes;

    /**
     *
     * @var table name 
     */
    protected $table = "events";
    //only allow the following items to be mass-assigned to our model

    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['name', 'image', 'image_logo', 'parent_id', 'image_banner', 'sort_order', 'floating_text', 'sub_text'];
    protected $dates = ['deleted_at'];

    /**
     * Method to get events group by parentId
     * 
     * @package Event
     * @return array
     */
    public function getEventsByParentId() {
        try {
            $categories = DB::table('events')
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
            Log::error('error in eventModel/getEventsByParentId' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get Primary Events List
     * 
     * @package Event
     * @return mixed
     */
    public function getPrimaryEvents() {
        try {
            $primaryEvents = Event::where('parent_id', 0)->orderBy('name')->pluck('name', 'id');
            $primaryEvents->prepend('Primary Theme', 0);
            return $primaryEvents;
        } catch (Exception $ex) {
            Log::error('error in eventModel/getPrimaryEvents' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get All Events List
     * 
     * @package Event
     * @return mixed
     */
    public function getEventsList() {
        try {
            $allEvents = Event::orderBy('name')->pluck('name', 'id');
            $allEvents->prepend('Primary Theme', 0);
            return $allEvents;
        } catch (Exception $ex) {
            Log::error('error in eventModel/getEventsList' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get Primary Events List
     * 
     * @package Event
     * @return mixed
     */
    public function getPrimaryEventsForHomePage() {
        $primaryEvents = array();
        try {
            $primaryEvents = Event::where('parent_id', 0)->select('id', 'name', 'image', 'image_logo')->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $primaryEvents;
    }

    /**
     * Get Sub Events List
     * 
     * @package Event
     * @param type $eventId
     * @return mixed
     */
    public function getSubEvents($eventId) {
        $subEvents = array();
        try {
            $subEvents = Event::where('parent_id', $eventId)->select('id', 'name')->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $subEvents;
    }

    /**
     * Return number of associated subevents
     * 
     * @package Event
     * @param array $ids
     * @return Integer
     */
    public function hasSubEvents(array $ids) {
        try {
            $count = Event::whereIn('parent_id', $ids)->count();
            return $count;
        } catch (Exception $ex) {
            Log::error('error in eventModel/hasSubEvents' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get events details with child info.
     * 
     * @package Event
     * @param type $id
     * @return mixed
     */
    public function getEventDetailByChildId($id) {
        try {
            $event = DB::select(DB::raw("select evp.id, evp.name, evp.image, evp.image_logo, evp.image_banner"
                    . ", ev.name as cname, ev.image_banner as cimage_banner"
                    . ", ev.floating_text as cfloating_text, ev.sub_text as csub_text, ev.image_logo as cimage_logo from events ev, events evp where ev.parent_id = evp.id and ev.id= :id"), array(
                        'id' => $id,
            ));
            return $event;
        } catch (Exception $ex) {
            Log::error('error in eventModel/getEventDetailByChildId' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Public function to Fetch Event Tree
     * 
     * @return array $eventTree
     */
    public function getEventTree() {
        $eventTree = array();
        try {
            $eventTree = $this->_getEventTreeCache();
            if (empty($eventTree)) {
                $eventTree = $this->_getEventTree();
                $this->_setEventTreeCache($eventTree);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $eventTree;
    }

    /**
     * Private function to Fetch Event Tree from cache
     * 
     * @return array $eventTree
     */
    private function _getEventTreeCache() {
        $eventTree = array();
        try {
            $eventTree = Cache::get('event_tree', array());
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $eventTree;
    }

    /**
     * Private function to set Event Tree in cache 
     *
     * @param array $eventTree
     * @return void
     *
     */
    private function _setEventTreeCache($eventTree) {
        try {
            Cache::forever('event_tree', $eventTree);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Private function to get Event Tree from DB
     * 
     * @return array
     */
    private function _getEventTree() {
        $aEventTree = array();
        try {
            $parentEvents = DB::table('events')
                    ->select('id', 'name', 'image', 'image_logo', 'image_banner')
                    ->where('parent_id', 0)
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order')
                    ->get();
            foreach ($parentEvents as $parentEvent) {
                $parentEventId          = $parentEvent->id;
                $parentEventName        = $parentEvent->name;
                $parentEventImage       = $parentEvent->image;
                $parentEventImageLogo   = $parentEvent->image_logo;
                $parentEventImageBanner = $parentEvent->image_banner;
                $subEvents = DB::table('events')
                        ->select('id', 'name', 'image', 'image_logo')
                        ->where('parent_id', $parentEventId)
                        ->whereNull('deleted_at')
                        ->get();
                $aSubEvent = array();
                foreach ($subEvents as $subEvent) {
                    $subEventId   = $subEvent->id;
                    $subEventName = $subEvent->name;
                    $aSubEvent[$subEventId] = array(
                        'id' => $subEventId,
                        'name' => $subEventName,
                        'image'         => $subEvent->image,
                        'image_logo'    => $subEvent->image_logo,
                    );
                }
                $aEventTree[$parentEventId] = array(
                    'id'            => $parentEventId,
                    'name'          => $parentEventName,
                    'image'         => $parentEventImage,
                    'image_logo'    => $parentEventImageLogo,
                    'image_banner'  => $parentEventImageBanner,
                    'subEvents'     => $aSubEvent
                );
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aEventTree;
    }

    /**
     * Clear Event Cache.
     * 
     * @return void
     */
    public function clearEventCache() {
        try {
            Cache::forget('event_tree');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

}
