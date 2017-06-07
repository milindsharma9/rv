<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Faq extends Model
{
    //
    use SoftDeletes;

    protected $table = "faqs";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['title', 'description', 'user_type', 'category'];
    protected $dates = ['deleted_at'];

    /**
     * Method to get Faq
     * @todo Obselete Now, Remove if not used anywhere
     * @return array  $faqList
     */
    public function getFaqs() {
        $faqList    = array();
        $cat = FaqCategory::all();
        $faqGroup = array();
        foreach ($cat as $catn => $catname) {
            $faqGroup[$catname->id] = $catname->category_name;
        }
        $faqs       = Faq::all();
        foreach ($faqs as $faq) {
            if (!isset($faqList[$faq->category])) {
                $faqList[$faq->category] = array(
                    'category'  => $faqGroup[$faq->category],
                    'faq'       => array(),
                );
            }
            $aFaq = array(
                'title'         => $faq->title,
                'description'   => $faq->description,
            );
            $faqList[$faq->category]['faq'][] = $aFaq;
        }
        return $faqList;
    }

    /**
     * Method to update Faq User Group Mapping
     *
     * @param int $faqId
     * @param array $aUserGroupId
     * @return void
     *
     */
    public function updateFaqUserGroupMapping($faqId, $aUserGroupId) {
        if (!empty($aUserGroupId)) {
            DB::table('faq_group_mapping')->where('fk_faq_id', '=', $faqId)->delete();
            $insertData = array();
            foreach ($aUserGroupId as $userGroupId ) {
                $aData = array(
                    'fk_faq_id'     => $faqId,
                    'fk_user_group_id'   => $userGroupId,
                );
                array_push($insertData, $aData);
            }
            DB::table('faq_group_mapping')->insert($insertData);
        }
    }
    
    /**
     * Method to get Faq
     *
     * @return array  $faqList
     */
    public function getFaqsDetails($faqId = Null) {
        $faqList    = array();        
        $cat = FaqCategory::all();
        $faqGroup = array();
        foreach ($cat as $catn => $catname) {
            $faqGroup[$catname->id] = $catname->category_name;
        }
        $faqUserGroup   = config('faq.user_group');
        $faqs       = Faq::all();
        $faqs       = DB::table('faqs')
                    ->select('faqs.*')
                    ->addSelect('faq_group_mapping.fk_user_group_id')
                    ->join('faq_group_mapping', 'faq_group_mapping.fk_faq_id', '=', 'faqs.id');
        if (!empty($faqId)) {
            $faqs = $faqs->where('faqs.id', '=', $faqId);
        }
        $faqs = $faqs->where('faqs.deleted_at', '=', NULL)
                    ->get();
        foreach ($faqs as $faq) {
            if (!isset($faqList[$faq->fk_user_group_id])) {
                $faqList[$faq->fk_user_group_id] = array(
                    'id'            => $faq->fk_user_group_id,
                    'name'          => $faqUserGroup[$faq->fk_user_group_id],
                    'category'      => array(),
                );
            }
            if (!isset($faqList[$faq->fk_user_group_id]['category'][$faq->category])) {
                $faqList[$faq->fk_user_group_id]['category'][$faq->category] = array(
                    'name'  => $faqGroup[$faq->category],
                    'faq'   => array(),
                );
            }
            $aFaq = array(
                'title'         => $faq->title,
                'description'   => $faq->description,
            );
            $faqList[$faq->fk_user_group_id]['category'][$faq->category]['faq'][] = $aFaq;
        }
        return $faqList;
    }

    /**
     * Method to get UserGroup Id of FAQ's
     * Used on Admin Edit page.
     *
     * @param int $faqId
     * @return type
     */
    public function getfaqUserGroupIds($faqId) {
        $faqs = DB::table('faq_group_mapping')
            ->select('faq_group_mapping.fk_user_group_id')
            ->where('faq_group_mapping.fk_faq_id', '=', $faqId)
            ->get();
        $aUserGroupId = array();
        foreach ($faqs as $faq) {
            $aUserGroupId[$faq->fk_user_group_id] = $faq->fk_user_group_id;
        }
        return $aUserGroupId;
    }
}
