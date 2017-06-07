<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Support\Facades\Cache;
use Exception;
use Log;

/**
 * Category Model
 */
class Category extends Model {

    /**
     * Soft delete trait included to ensure soft delete is unable
     */
    use SoftDeletes;

    /**
     *
     * @var table name 
     */
    protected $table = "categories";
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['name', 'image', 'parent_id'];
    protected $dates = ['deleted_at'];

    /**
     * Gets Primary Category as associated Array
     * 
     * @package Category
     * @param Integer|NULL $parentId
     * @param Integer|NULL $excludeCurrentId
     * @param boolean $includeParentText
     * @return mixed
     */
    public function getSubCategories($parentId = NULL, $excludeCurrentId = NULL, $includeParentText = TRUE) {
        try {
            $parentText = 'Parent Category';
            if ($parentId === null) {
                $primaryCategories = Category::orderBy('name')->pluck('name', 'id');
            } else {
                if ($excludeCurrentId == null) {
                    $primaryCategories = Category::where('parent_id', $parentId)->orderBy('name')->pluck('name', 'id');
                } else {
                    $primaryCategories = Category::where(
                                    [['parent_id', $parentId],
                                        ['id', '!=', $excludeCurrentId]
                            ])->orderBy('name')->pluck('name', 'id');
                }
                if ($parentId != 0) {
                    $parentText = 'Sub Category';
                }
            }
            if ($includeParentText) {
                $primaryCategories->prepend($parentText, 0);
            }
            return $primaryCategories;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getSubCategories' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Gets Default Subcategory Array
     * 
     * @package Category
     * @return string
     */
    public function getDefaultSubCategory() {
        try {
            $defaultSubCategory[0] = 'Sub Category';
            return $defaultSubCategory;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getDefaultSubCategory' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get Parent & SuperParentId of category
     * 
     * @package Category
     * @param Integer $catId
     * @return array
     */
    public function getCategoryParentDetails($catId) {
        try {
            $parentId = $superParentId = 0;
            if ($catId != 0) {
                $category = Category::find($catId);
                $parentId = $category->parent_id;
                if ($parentId == 0) {
                    $superParentId = 0;
                } else {
                    $subCategory = Category::find($parentId);
                    $superParentId = $subCategory->parent_id;
                }
            }
            return array(
                'parentId' => $parentId,
                'superParentId' => $superParentId
            );
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getCategoryParentDetails' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to get categories group by parentId
     * 
     * @package Category
     * @return array
     */
    public function getCategoriesByParentId() {
        try {
            $categories = DB::table('categories')
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
            Log::error('error in Catergory Model/getCategoriesByParentId' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Fectch Data for category listing page
     * 
     * @package Category
     * @return categories mixed
     */
    public function getCategoryListing() {
        try {
            $categories = DB::select(
                            DB::raw("SELECT c.id AS id, c.name AS name, c.image, parent.id AS parent, grandparent.id AS grandparent FROM categories AS c LEFT JOIN categories AS parent ON c.parent_id = parent.id LEFT JOIN categories AS grandparent ON parent.parent_id = grandparent.id where c.deleted_at is NULL order by c.name ASC")
            );
            return $categories;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getCategoryListing' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Public function to Fetch Category Tree
     * 
     * @package Category
     * @return mixed
     */
    public function getCategoryTree() {
        try {
            $categoryTree = $this->_getCategoryTreeCache();
            if (empty($categoryTree)) {
                $categoryTree = $this->_getCategoryTree();
                $this->_setCategoryTreeCache($categoryTree);
            }
            return $categoryTree;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getEventsByParentId' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Private function to Fetch Category Tree from cache
     * 
     * @package Category
     * @return Cache mixed
     */
    private function _getCategoryTreeCache() {
        try {
            $categoryTree = Cache::get('caterory_tree', array());
            return $categoryTree;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/_getCategoryTreeCache' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Private function to set Category Tree in cache 
     * 
     * @package Category
     * @param type $categoryTree
     * @return void
     */
    private function _setCategoryTreeCache($categoryTree) {
        try {
            Cache::forever('caterory_tree', $categoryTree);
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/_setCategoryTreeCache' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Private function to get Category Tree from DB
     * 
     * @package Category
     * @return array
     */
    private function _getCategoryTree() {
        try {
            $parentCategories = DB::table('categories')
                    ->select('id', 'name', 'image')
                    ->where('parent_id', 0)
                    ->whereNull('deleted_at')
                    ->get();
            $aCatTree = array();
            foreach ($parentCategories as $parentCategory) {
                $parentCatId = $parentCategory->id;
                $parentCatName = $parentCategory->name;
                $parentCatImage = $parentCategory->image;
                $subCategories = DB::table('categories')
                        ->select('id', 'name')
                        ->where('parent_id', $parentCatId)
                        ->whereNull('deleted_at')
                        ->orderBy('name')
                        ->get();
                $aSubCat = array();
                foreach ($subCategories as $subCategory) {
                    $subCatId = $subCategory->id;
                    $subCatName = $subCategory->name;
                    $aSubSubCat = array();
                    $subSubCategories = DB::table('categories')
                            ->select('id', 'name')
                            ->where('parent_id', $subCatId)
                            ->whereNull('deleted_at')
                            ->get();
                    foreach ($subSubCategories as $subSubCategory) {
                        $subSubCatId = $subSubCategory->id;
                        $subSubCatName = $subSubCategory->name;
                        $aSubSubCat[$subSubCatId] = array(
                            'id' => $subSubCatId,
                            'name' => $subSubCatName,
                        );
                    }
                    $aSubCat[$subCatId] = array(
                        'id' => $subCatId,
                        'name' => $subCatName,
                        'subSubCat' => $aSubSubCat
                    );
                }
                $aCatTree[$parentCatId] = array(
                    'id' => $parentCatId,
                    'name' => $parentCatName,
                    'image' => $parentCatImage,
                    'subCategory' => $aSubCat
                );
            }
            return array('categories' => $aCatTree);
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/_getCategoryTree' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to get Sub Category Tree from DB
     * 
     * @package Category
     * @param Integer $pCatId
     * @param boolean $getSubCats
     * @return array
     */
    public function getSubCatTree($pCatId, $getSubCats = FALSE) {
        try {
            $subCategories = DB::table('categories')
                    ->select('id', 'name')
                    ->where('parent_id', $pCatId)
                    ->whereNull('deleted_at')
                    ->get();
            $aSubCat = array();
            $i = 0;
            foreach ($subCategories as $subCategory) {
                $subCatId = $subCategory->id;
                $subCatName = $subCategory->name;
                $aSubSubCat = array();
                if ($getSubCats) {
                    if ($i == 0) {
                        $subSubCategories = DB::table('categories')
                                ->select('id', 'name')
                                ->where('parent_id', $subCatId)
                                ->whereNull('deleted_at')
                                ->get();
                        foreach ($subSubCategories as $subSubCategory) {
                            $subSubCatId = $subSubCategory->id;
                            $subSubCatName = $subSubCategory->name;
                            array_push($aSubSubCat, array(
                                'id' => $subSubCatId,
                                'name' => $subSubCatName,
                            ));
                        }
                    }
                }
                array_push($aSubCat, array(
                    'id' => $subCatId,
                    'name' => $subCatName,
                    'subSubCat' => $aSubSubCat
                ));
                $i ++;
            }
            return array('sub_categories' => $aSubCat);
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/getSubCatTree' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Return Sub categories count
     * 
     * @package Category
     * @param array $ids
     * @return Integer
     */
    public function hasSubCategories(array $ids) {
        try {
            $count = Category::whereIn('parent_id', $ids)->count();
            return $count;
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/hasSubCategories' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Clear Category Cache.
     * 
     * @package Category
     * @return void
     */
    public function clearCategoryCache() {
        try {
            Cache::forget('caterory_tree');
        } catch (Exception $ex) {
            Log::error('error in Catergory Model/clearCategoryCache' . $ex->getMessage());
            return view('errors.500');
        }
    }
    
    /**
     * Get products for a subSubCatId.
     * 
     * @package Category
     * @param Int $subSubCatId
     * @return Object mixed
     */
    function getProductDetail($subSubCatId) {
        try {
            $productsArr = DB::table('products')
                    ->select('id', 'name', 'description')
                    ->join('xref_product_categories', 'products.id', '=', 'xref_product_categories.fk_product_id')
                    ->where('fk_category_id', '=', $subSubCatId)
                    ->get();
            Log::info('getProductDetail found');
            return $productsArr;
        } catch (Exception $ex) {
            Log::error('error in getProductDetail' . $ex->getMessage());
        }
    }

}
