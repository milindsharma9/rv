<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;
use Log;
use Cache;
use DB;
use App\BlogMeta;
use Illuminate\Pagination\Paginator;

class Blog extends Model
{

    /**
     * Set table name.
     * @var type 
     */
    protected $table = "master_blog";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['type', 'title', 'description', 'image', 'image_thumb',
        'start_date', 'end_date', 'url_path', 'published', 'sub_title', 'meta_title', 'meta_keywords', 'meta_description'];

    /**
     * Method returns blog data on the basis of id.
     * 
     * @param Int $id
     * @return object
     */
    public function getBlogDataById($id = NULL) {
        $blogData = array();
        try {
            $blogData = DB::table('master_blog')
                    ->addSelect('epm.*')
                    ->addSelect('epm.id as ePMId')
                    ->addSelect('master_blog.*')
                ->leftjoin('events_places_meta AS epm', 'epm.fk_master_blog_id', '=', 'master_blog.id');
            if (NULL != $id) {
                $blogData = $blogData->where('master_blog.id', '=', $id)->first();
            } else {
                $blogData = $blogData->get();
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }
    
    
    /**
     * Returns validation rules on the basis of blog Type.
     * 
     * @param int $blogType
     * @param int $id
     * @return array
     */
    public function validationRules($blogType, $id =NULL) {
        try {
            $rules = array(
                'description'   => 'required',
            );
            if($id != NULL){
                $rules = array_merge($rules, ['url_path'    => 'required|unique:master_blog,url_path,' . $id . ',id,type,' . $blogType,
                    'title'         => 'required|unique:master_blog,title,' . $id . ',id,type,'.$blogType]);
            }else{
                $rules = array_merge($rules, ['url_path'    => 'required|unique:master_blog,url_path,NULL,id,type,'.$blogType,
                                            'title'         => 'required|unique:master_blog,title,NULL,id,type,'.$blogType,
                                            'type'          => 'required',
                                            'image'         => 'required',
                                            'image_thumb'   => 'required']);
            }
            $conditionalRules = [];
            switch ($blogType) {
                case config('blog.type_blog'):
                    $conditionalRules = $this->validationRulesForBlog();
                    break;
                case config('blog.type_event'):
                    $conditionalRules = $this->validationRulesForEvents();
                    break;
                case config('blog.type_place'):
                $conditionalRules = $this->validationRulesForPlaces();
                    break;
                default:
                $conditionalRules = $rules;
                    break;
            }
            return array_merge($rules, $conditionalRules);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Return validation rules for blog.
     * 
     * @return array
     */
    private function validationRulesForBlog() {
        $rules = array(
            'sub_title'     => 'required',
//            'start_date'    => 'required',
        );
        return $rules;
    }
    
    /**
     * Return validation rules for Events.
     * 
     * @return array
     */
    private function validationRulesForEvents() {
        $rules = array(
            'address'           => 'required',
            'location'          => 'required',
//            'city'              => 'required',
//            'state'             => 'required',
//            'pin'               => 'required',
            'event_ticket_text' => 'required',
            'start_date'        => 'required',
            'event_ticket_url'  => 'url',
//            'end_date'          => 'required',
        );
        return $rules;
    }
    
    /**
     * Return validation rules for Places.
     * 
     * @return array
     */
    private function validationRulesForPlaces() {
        $rules = array(
            'address'           => 'required',
            'location'          => 'required',
//            'city'              => 'required',
//            'state'             => 'required',
//            'pin'               => 'required',
            'places_drink_url'  => 'url',
            //'places_food_url'   => 'url',
        );
        return $rules;
    }
    

    /**
     * One to One association with BlogMeta.
     * 
     * @package Blog Model
     * @return App\BlogMeta
     */
    public function blogMeta()
    {   try{
            return $this->hasOne('App\BlogMeta', 'fk_master_blog_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to update Blog Keywords mapping
     *
     * @param int $contentId Primary Id of content
     * @param array $aKeyWordId
     */
    public static function saveBlogKeywordMapping($contentId, array $aKeyWordId) {
        DB::table('keyword_map')->where('blog_events_place_id', '=', $contentId)->delete();
        $insertData = array();
        foreach ($aKeyWordId as $index => $keywordId) {
            $aData = array(
                'blog_events_place_id'  => $contentId,
                'keyword_id'            => $keywordId,
            );
            array_push($insertData, $aData);
        }
        DB::table('keyword_map')->insert($insertData);
    }
    
    
    /**
     * Return keyword as required for JS.
     * 
     * @param Int $id
     * @return array
     */
    public function getKeywordForToken($id, $type = 'blog', $random = true) {
        $blogData = array();
        try {
            $blogData = DB::table('keyword')
                    ->addSelect('keyword.id AS id')
                    ->addSelect('keyword.name AS name')
                    ->addSelect('keyword.machine_name AS machine_name');
            if ($random) {
                $blogData = $blogData->orderBy(DB::raw('RAND()'));
            } else {
                $blogData = $blogData->orderBy('keyword.name');
            }
            if($type == 'blog'){
                $blogData = $blogData->join('keyword_map AS km', 'keyword.id', '=', 'km.keyword_id') 
                        ->where('km.blog_events_place_id', '=', $id);
            }else if($type == 'locale'){
                $blogData = $blogData->join('xref_locale_keyword AS lk', 'keyword.id', '=', 'lk.fk_keyword_id') 
                        ->where('lk.fk_locale_id', '=', $id);
            }
            $blogData = $blogData->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }
    
    /**
     * Method to fetch blog listing data.
     * 
     * @param Int $type
     * @param Int $month
     * @param Int $year
     * @return Object
     */
    public function getListingData($type, $urlPath = NULL, $date = NULL, $month = NULL, $year = NULL, $isArchive = false) {
        $blogData = array();
        try {
            $blogData = DB::table('master_blog')
                    ->addSelect('epm.*')
                    ->addSelect('epm.id as ePMId')
                    ->addSelect('master_blog.*')
                    ->leftjoin('events_places_meta AS epm', 'epm.fk_master_blog_id', '=', 'master_blog.id')
                    ->where('master_blog.type', '=', $type)
                    ->where('master_blog.published', '=', '1');
            if ($type == config('blog.type_event')) {
                if (NULL == $urlPath && !$isArchive) {
                    $blogData = $blogData->whereRaw('master_blog.start_date >= CURDATE()');
                }
                $blogData = $blogData->addSelect(DB::raw('DATE_FORMAT(master_blog.start_date, "%M") as eventMonth'))
                        ->addSelect(DB::raw("DATE_FORMAT(master_blog.start_date, '%d') as eventDate"))
                        ->addSelect(DB::raw('DATE_FORMAT(master_blog.start_date, "%D") as eventDateSuffix'))
                        ->addSelect(DB::raw('UPPER(DATE_FORMAT(master_blog.start_date, "%W")) as eventDay'))
                        ->orderBy('master_blog.start_date');
                if (NULL != $month) {
                    $blogData = $blogData->whereRaw('MONTH(master_blog.start_date) = ' . $month);
                }
                if (NULL != $year) {
                    $blogData = $blogData->whereRaw('YEAR(master_blog.start_date) = ' . $year);
                }
                if (NULL != $date) {
                    $blogData = $blogData->whereRaw('DAY(master_blog.start_date) >= ' . $date);
                }
                // In Archive show previous content of month
                if ($isArchive) {
                    $currDate       =  date('Y-m-d');
                    $aCurrDate      = explode("-", $currDate);
                    $currentYear    = $aCurrDate[0];
                    $currentMonth   = $aCurrDate[1];
                    $currentDate    = $aCurrDate[2];
                    if ($currentMonth == $month) {
                        $blogData = $blogData->whereRaw('DAY(master_blog.start_date) < ' . $currentDate);
                    }
                }
            }
            if ($type == config('blog.type_blog')) {
                if (NULL == $urlPath && !$isArchive) {
                    $blogData = $blogData->whereRaw('IF(master_blog.end_date = "0000-00-00 00:00:00", 1, master_blog.end_date >= CURDATE())');
                }
            }
            if (NULL != $urlPath) {
                $blogData = $blogData->where('master_blog.url_path', '=', $urlPath)
                        ->first();
            } else {
                $blogData = $blogData
                    ->orderBy('master_blog.id', 'desc')->paginate(9);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }
    

    /**
     * Method to fetch blog listing data.
     * 
     * @param Int $type
     * @param Int $month
     * @param Int $year
     * @return Object
     */
    public function getListingDataForKeyword($keywordUrlPath, $type, $related = false, $selective = false, $contentId = NULL) {
        $blogData = array();
        try {
            $blogData = DB::table('master_blog')
                    ->addSelect('epm.*')
                    ->addSelect('epm.id as ePMId')
                    ->addSelect('master_blog.*')
                    ->addSelect('ky.name as keywordName')
                    ->leftjoin('events_places_meta AS epm', 'epm.fk_master_blog_id', '=', 'master_blog.id')
                    ->join('keyword_map AS kym', 'kym.blog_events_place_id', '=', 'master_blog.id')
                    ->join('keyword AS ky', 'ky.id', '=', 'kym.keyword_id')
                    ->where('ky.machine_name', '=', $keywordUrlPath)
                    ->where('master_blog.published', '=', '1');
            if ($type == config('blog.type_event')) {
                $blogData = $blogData->whereRaw('master_blog.start_date >= CURDATE()');
                $blogData = $blogData->addSelect(DB::raw('DATE_FORMAT(master_blog.start_date, "%M") as eventMonth'))
                        ->addSelect(DB::raw("DATE_FORMAT(master_blog.start_date, '%d') as eventDate"))
                        ->addSelect(DB::raw('DATE_FORMAT(master_blog.start_date, "%D") as eventDateSuffix'))
                        ->addSelect(DB::raw('UPPER(DATE_FORMAT(master_blog.start_date, "%W")) as eventDay'))
                        ->orderBy('master_blog.start_date');
            }
            if ($type == config('blog.type_blog')) {
                $blogData = $blogData->whereRaw('IF(master_blog.end_date = "0000-00-00 00:00:00", 1, master_blog.end_date >= CURDATE())');
            }
            if ($selective) {
                $blogData = $blogData->where('master_blog.id', '!=', $contentId)
                        ->where('master_blog.type', '=', $type);
            }
            if ($related) {
                $blogData = $blogData->orderBy(DB::raw('RAND()'))->orderBy('master_blog.id', 'desc')->first();
            } else {
                $blogData = $blogData->orderBy('master_blog.id', 'desc')->paginate(9);
            }
            
            
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }

    /**
     * Method to fetch blog listing data.
     * 
     * @param Int $type
     * @param Int $month
     * @param Int $year
     * @return Object
     */
    public function getListingDataForLocale($keywordUrlPath, $aType, $currentPage = NULL) {
        $blogData = array();
        try {
            $blogData = DB::table('master_blog')
                    ->addSelect('epm.*')
                    ->addSelect('epm.id as ePMId')
                    ->addSelect('master_blog.*')
                    ->addSelect('ky.name as keywordName')
                    ->leftjoin('events_places_meta AS epm', 'epm.fk_master_blog_id', '=', 'master_blog.id')
                    ->join('keyword_map AS kym', 'kym.blog_events_place_id', '=', 'master_blog.id')
                    ->join('keyword AS ky', 'ky.id', '=', 'kym.keyword_id')
                    ->where('ky.machine_name', '=', $keywordUrlPath)
                    ->where('master_blog.published', '=', '1')
                    ->whereRaw('IF(master_blog.type = "'.  config('blog.type_event').'", master_blog.start_date >= CURDATE(), 1)')
                    ->whereRaw('IF(master_blog.type = "'.  config('blog.type_blog').'", IF(master_blog.end_date = "0000-00-00 00:00:00", 1, master_blog.end_date >= CURDATE()), 1)')
                    ->orderBy(DB::raw('IF(master_blog.type = "'.  config('blog.type_event').'", master_blog.start_date, master_blog.id)'));
            $blogData = $blogData->whereIn('master_blog.type', $aType);
            if (NULL != $currentPage) {
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }

            $blogData = $blogData->orderBy('master_blog.id', 'desc')->paginate(6);
            
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }
    
    public function getBlogDataForFeeds($type) {
        $blogData = array();
        try {
            $blogData = DB::table('master_blog')
                    ->addSelect('epm.*')
                    ->addSelect('epm.id as ePMId')
                    ->addSelect('master_blog.*')
                    ->leftjoin('events_places_meta AS epm', 'epm.fk_master_blog_id', '=', 'master_blog.id')
                    ->where('master_blog.type', '=', $type)
                    ->whereRaw('IF(master_blog.type = "'.  config('blog.type_event').'", master_blog.start_date >= CURDATE(), 1)')
                    ->whereRaw('IF(master_blog.type = "'.  config('blog.type_blog').'", IF(master_blog.end_date = "0000-00-00 00:00:00", 1, master_blog.end_date >= CURDATE()), 1)')
                    ->where('master_blog.published', '=', '1')->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }

}
