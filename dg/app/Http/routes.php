<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */
Route::group([ 'middleware' => []], function () {
    /*
	Route::get('/', [
        "as"    => 'customer.landing',
        "uses"  => 'CustomerController@rendorLandingPage'
    ]); */
	Route::get('/administrator', [
        "as"    => 'customer.landing',
        "uses"  => 'CustomerController@rendorLandingPage'
    ]); 
    /*Route::get('/mango/refund/hook', [
        "as"    => 'payment.refund.hook',
        "uses"  => 'PaymentController@captureRefundStatus'
    ]);*/
    
    
    
    Route::get('/contact-us', [
        "as"    => 'common.contact.us',
        "uses"  => 'HomeController@renderContactUs'
    ]);
    
    Route::post('/savecontact', [
        "as"    => 'common.save.contact',
        "uses"  => 'HomeController@saveContactUs'
    ]);
    
    Route::any('/apply/retailers', [
        "as"    => 'common.retailers.apply.show',
        "uses"  => 'HomeController@renderRetailerApplyForm'
    ]);

    Route::get('/apply/drivers', [
        "as"    => 'common.drivers.apply.show',
        "uses"  => 'HomeController@renderDriverApplyForm'
    ]);
    
    Route::post('/apply/drivers', [
        "as"    => 'common.drivers.apply.save',
        "uses"  => 'HomeController@saveDriverApplicationForm'
    ]);

    Route::get('/resetPassword', [
        "as"    => 'customer.resetPassword',
        "uses"  => 'HomeController@resetPassword'
    ]);
    
    Route::get('/sitemap1', 'SitemapController@index'); 
    
    Route::get('/productfeed', 'FeedsController@index');  

    Route::get('/blogfeed', 'FeedsController@createBlogFeed');
    
    Route::get('/sitemap', [
        "as" => 'home.sitemap',
        "uses" => 'SitemapController@getsitemapHtml'
    ]);
    
    /*Route::get('/changePassword', function () {
        return view('home.createPassword')->with('userId', '1');
    });*/

    Route::get('/home', [
        "as" => 'home.index',
        "uses" => 'CustomerController@index'
    ]);

    Route::get('/search', [
        "as" => 'customer.search',
        "uses" => 'CustomerController@search'
    ]);
    
    Route::post('/search/paginate', [
        "as" => 'customer.search.paginate',
        "uses" => 'CustomerController@searchWithPagination'
    ]);
    
     Route::get('/search/{catId}/{searchterm}', [
        "as" => 'customer.search.cat',
        "uses" => 'CustomerController@searchByCategory'
    ]);

    Route::get('/creations', [
        "as"    => 'customer.creations',
        "uses"  => 'CustomerController@renderCreationsPage'
    ]);
    
     Route::get('/occasions', [
        "as"    => 'customer.occasions',
        "uses"  => 'CustomerController@renderOccasionssPage'
    ]);

    Route::get('/products/{catname?}/{id?}', [
        'as'   => 'customer.products',
        'uses' => 'CustomerController@getProducts'
    ]);

    Route::get('/products/{catname?}/{catId}/{subcatname?}/{subcatId?}/{subsubcatname?}/{subsubcatId?}', [
        'as'   => 'customer.products.subcat.list',
        'uses' => 'CustomerController@getProducts'
    ]);

    Route::get('/getSubCatTree/{pCatId}', [
        'as'   => 'customer.products.subcat',
        'uses' => 'CustomerController@getSubcatTree'
    ]);

    Route::get('/getSubSubCatTree/{pCatId}', [
        'as'   => 'customer.products.subcat.cat',
        'uses' => 'CustomerController@getSubSubcatTree'
    ]);

    Route::post('/cart/add', [
        'as'   => 'customer.cart.add',
        'uses' => 'CartController@add'
    ]);
    
    Route::get('/clearcart', [
        'as'   => 'customer.cart.clearcart',
        'uses' => 'CartController@clearCart'
    ]);

    Route::get('/getSubEvents/{id}', [
        'as'   => 'get.submood',
        'uses' => 'Admin\EventsController@getSubEvents'
    ]);
    
    Route::get('/getOpeningTime', [
        'as'   => 'customer.site.time',
        'uses' => 'StoreController@checkOpeningTime'
    ]);
    
    Route::get('/getSubOccasion/{id}', [
        'as'   => 'get.occasion',
        'uses' => 'Admin\OccasionsController@getSubOccasion'
    ]);

    Route::get('/productdetail/{id}', [
        'as'   => 'products.detail',
        'uses' => 'CustomerController@getProductDetail'
    ]);
    
//    Route::get('/cart', [
//        'as'   => 'customer.cart',
//        'uses' => 'CartController@renderCart'
//    ]);
    
    Route::get('/onestepcheckout', [
        'as'   => 'customer.cart',
        'uses' => 'CartController@checkout'
    ]);

    Route::post('/cart/update', [
        'as'   => 'customer.cart.update',
        'uses' => 'CartController@update'
    ]);
    
    Route::post('/cart/remove', [
        'as'   => 'customer.cart.remove',
        'uses' => 'CartController@remove'
    ]);

    Route::get('/checkout', [
        'as'   => 'customer.checkout',
        'uses' => 'CartController@checkout'
    ]);

    Route::get('/getValidPostcodes', [
        'as'   => 'customer.postcode.get',
        'uses' => 'Admin\PostcodeController@getValidPostcodes'
    ]);

    Route::post('/validatePostcode', [
        'as'   => 'customer.postcode.validate',
        'uses' => 'Admin\PostcodeController@validatePostcode'
    ]);

    Route::get('/getMatchedProducts', [
        'as'   => 'customer.search.matched',
        'uses' => 'CustomerController@getMatchedProducts'
    ]);

    Route::post('/setDeliveryPostcode', [
        'as'   => 'customer.delivery.postcode.set',
        'uses' => 'CustomerController@setDeliveryPostcode'
    ]);

    Route::get('/checkCustomerCartStatus', [
        'as'   => 'customer.cart.status.check',
        'uses' => 'CustomerController@checkCustomerCartStatus'
    ]);

     Route::get('/faq/{api?}', [
        'as'   => 'api.faq',
        'uses' => 'Admin\FaqController@getFaqs'
    ]);

    Route::get('/careers',  [
        "as" => "common.careers",
        "uses" => "CustomerController@getCarrersPage"
    ]);
    
    Route::get('/blog/{blogContentUrl?}/',  [
        "as"    => "common.blog",
        "uses"  => "CustomerController@getBlogContent"
    ]);

    Route::get('/places/{placeContentUrl?}',  [
        "as"    => "common.places",
        "uses"  => "CustomerController@getPlaceContent"
    ]);

    Route::get('/events/{EventContentUrl?}',  [
        "as"    => "common.events",
        "uses"  => "CustomerController@getEventContent"
    ]);
    
    Route::post('/events/month/details',  [
        "as"    => "event.month.details",
        "uses"  => "CustomerController@getEventDetailsByMonth"
    ]);

    Route::get('/content/keywords/{keyword}',  [
        "as"    => "common.content.keywords",
        "uses"  => "CustomerController@getKeywordsContent"
    ]);

    Route::get('/content/archieve/{month?}/{year?}',  [
        "as"    => "common.content.archieve",
        "uses"  => "CustomerController@getArchieveContent"
    ]);
    
    Route::get('/l/{url}',  [
        "as"    => "common.locale",
        "uses"  => "CustomerController@getLocaleContent"
    ]);

    Route::get('/b/{url}',  [
        "as"    => "common.brand",
        "uses"  => "CustomerController@getBrandContent"
    ]);

    Route::post('/keyword/content/get',  [
        "as"    => "common.keyword.content.type.get",
        "uses"  => "CustomerController@getKeywordRelatedContent"
    ]);

    Route::post('/locale/content/get',  [
        "as"    => "common.locale.content.type.get",
        "uses"  => "CustomerController@getLocaleRelatedContent"
    ]);

    Route::auth();

    Route::match(['get', 'post'], '/home/registervendor', [
        "as" => 'home.registerVendor',
        "uses" => 'HomeController@registerVendor'
    ]);
    
    Route::get('/home/activate/{token?}',  [
        "as" => 'home.activate',
        "uses" => 'HomeController@activate'
    ]);
    
    Route::match(['get', 'post'], '/home/createPassword', [
        "as" => 'home.createPassword',
        "uses" => 'StoreController@createPassword'
    ]);
    
    Route::get('/session', 'SessionController@index');
    
    Route::get('/session/clear', 'SessionController@clear');
    
    Route::any('/payment/validateCard/{error?}/{errorCode?}', [
        "as" => "payment.validateCard",
        "uses" => "PaymentController@validateCard"
    ]);
    
    Route::any('/payment/response/{error?}', [
        "as" => "payment.response",
        "uses" => "CartController@response"
    ]);
        
    Route::get('/payment/deduct', [
        "as" => "payment.deduct",
        "uses" => "PaymentController@getUserPayment"
    ]);
    
    Route::get('/search/creations/{moodId?}/{moodName?}',  [
        "as" => "search.mood",
        "uses" => "CustomerController@moodSearch"
    ]);
    
    Route::get('/search/occasions/{occasionId?}/{occasionName?}',  [
        "as" => "search.occasion",
        "uses" => "CustomerController@occasionSearch"
    ]);
    
    Route::get('/customer/bundleDetail/{bundleId?}',  [
        "as" => "customer.bundleDetail",
        "uses" => "CustomerController@bundleDetail"
    ]);

    Route::get('/getBundleDetail/{bundleId?}',  [
        "as" => "customer.getBundleDetail",
        "uses" => "CustomerController@getBundleDetail"
    ]);
    
    // api route to open browser.
    Route::get('/addcard/{userId}', [
        "as" => "customer.addcard",
        "uses" => "PaymentController@addPayment"
    ]);
    
    Route::post('/savePayment', [
        "as" => "customer.savePayment",
        "uses" => "PaymentController@savePayment"
    ]);
    
    Route::post('/savedefaultcard', [
        "as" => "customer.savecarddefault",
        "uses" => "CustomerController@updateCardDefault"
    ]);
    
    Route::get('/termsnconditions',  [
        "as" => "search.terms",
        "uses" => "CustomerController@getTermsCondition"
    ]);
    
    Route::get('/cookies/{api?}',  [
        "as" => "search.cookies",
        "uses" => "CustomerController@getcookiesPolicy"
    ]);
    
    Route::get('/privacy/{api?}',  [
        "as" => "search.privacypolicy",
        "uses" => "CustomerController@getPrivacyPolicy"
    ]);

    Route::get('/page/{url?}/{api?}',  [
        "as" => "General.page",
        "uses" => "CustomerController@getCmsPageContent"
    ]);
    
    Route::get('/terms-and-conditions/{api?}',  [
        "as" => "search.legalterms",
        "uses" => "CustomerController@getlegalterms"
    ]);
    
    Route::get('/success/{responseStatusId?}',  [
        "as" => "card.success",
        "uses" => "PaymentController@response"
    ]);
    
    Route::get('/response/{responseStatusId?}',  [
        "as" => "card.response",
        "uses" => "PaymentController@getErrorResponse"
    ]);
    
    Route::post('/companydetails', [
        'as'   => 'store.companydetails',
        'uses' => 'StoreController@getCompanyDetails'
    ]);
    
    Route::post('/officerdetails', [
        'as'   => 'store.officerdetails',
        'uses' => 'StoreController@getOfficerDetails'
    ]);
    
});

Route::group([ 'middleware' => ['auth_admin', 'revalidate'], 'prefix' => 'admin'], function () {
	 //Users Management
    Route::resource('users', 'Admin\UsersController');
    Route::get('userexcel', 
    [
      'as' => 'admin.users.excel',
      'uses' => 'Admin\UsersController@excel'
    ]);
	 //manuals Management
    Route::resource('manuals', 'Admin\ManualController');
    //clients CMS Management
    Route::resource('clients', 'Admin\ClientcmsController');
    
	
    // Events management
    Route::resource('events', 'Admin\EventsController');
    Route::post('events/massDelete', [
        'as' => 'admin.events.massDelete',
        'uses' => 'Admin\EventsController@massDelete'
    ]);

    // Occassions management
    Route::resource('occasions', 'Admin\OccasionsController');
    Route::post('occasions/massDelete', [
        'as' => 'admin.occasions.massDelete',
        'uses' => 'Admin\OccasionsController@massDelete'
    ]);
    
    //Bundle Management
    Route::resource('bundles', 'Admin\BundlesController');
    Route::post('bundles/massDelete', [
        'as' => 'admin.bundles.massDelete',
        'uses' => 'Admin\BundlesController@massDelete'
    ]);

    Route::get('bundles/{bundle}/map', [
        'as'   => 'admin.bundles.map',
        'uses' => 'Admin\BundlesController@mapBundle'
    ]);

    Route::post('bundles/map/{bundle}', [
        'as'   => 'admin.bundles.map.store',
        'uses' => 'Admin\BundlesController@mapBundleStore'
    ]);
    
    //Driver Management
    Route::resource('drivers', 'Admin\DriversController');
    Route::post('drivers/massDelete', [
        'as' => 'admin.drivers.massDelete',
        'uses' => 'Admin\DriversController@massDelete'
    ]);
    
    //Vendors Management
    Route::resource('vendors', 'Admin\VendorsController'); 
    Route::post('vendors/massUpdate', [
        'as'   => 'admin.vendors.massUpdate',
        'uses' => 'Admin\VendorsController@massUpdate'
    ]);

    Route::get('vendors/manage-products/{vendorId}', [
        'as'   => 'admin.vendors.manage.products',
        'uses' => 'Admin\VendorsController@manageVendorProducts'
    ]);

    Route::get('vendors/stores/{vendorId}', [
        'as'   => 'admin.vendors.stores.list',
        'uses' => 'Admin\VendorsController@showVendorStores'
    ]);
    
    Route::post('/vendors/saveProduct', [
        'as'   => 'admin.vendors.products.save',
        'uses' => 'Admin\VendorsController@saveProduct'
    ]);

    //Orders Management
    Route::resource('orders', 'Admin\OrdersController');
    Route::get('orders/items/details/{id}', [
        'as'   => 'admin.orders.items.details.show',
        'uses' => 'Admin\OrdersController@showOrderDetail'
    ]);
    
    Route::any('orders/{id}/{statusid}/edit/', [
        'as'   => 'admin.orders.edit.new',
        'uses' => 'Admin\OrdersController@edit'
    ]);
    
   
    
    // Categories management
    Route::resource('categories', 'Admin\CategoriesController');
    Route::post('categories/massDelete', [
        'as'   => 'admin.categories.massDelete',
        'uses' => 'Admin\CategoriesController@massDelete'
    ]);
    
    Route::get('categories/getSubcategory/{pCatId}/{currentCatId}/', [
        'as'   => 'admin.categories.getSubcategory',
        'uses' => 'Admin\CategoriesController@getSubcategory'
    ]);

    // Product management
    Route::resource('products', 'Admin\ProductsController');
    Route::post('products/massDelete', [
        'as'   => 'admin.products.massDelete',
        'uses' => 'Admin\ProductsController@massDelete'
    ]);

    Route::get('products/{products}/map', [
        'as'   => 'admin.products.map',
        'uses' => 'Admin\ProductsController@mapProduct'
    ]);

    Route::get('products/images/{productId}', [
        'as'   => 'admin.products.image.list',
        'uses' => 'Admin\ProductsController@getProductImages'
    ]);
    
    Route::post('products/images/upload', [
        'as'   => 'admin.products.image.upload',
        'uses' => 'Admin\ProductsController@uploadProductImage'
    ]);

    Route::delete('products/images/destroy', [
        'as'   => 'admin.products.image.destroy',
        'uses' => 'Admin\ProductsController@deleteProductImage'
    ]);

    Route::post('products/images/setprimary', [
        'as'   => 'admin.products.image.setprimary',
        'uses' => 'Admin\ProductsController@setProductPrimaryImage'
    ]);

    Route::post('products/map/{products}', [
        'as'   => 'admin.products.map.store',
        'uses' => 'Admin\ProductsController@mapProductStore'
    ]);
    
    Route::post('products/getProducts', [
        'as'   => 'admin.products.getProducts',
        'uses' => 'Admin\ProductsController@getProducts'
    ]);
    
    Route::get('getproductlist', [
        'as'   => 'admin.getproductList',
        'uses' => 'Admin\ProductsController@getallProductsUrls'
    ]);
    
    Route::post('bundles/removeMapping', [
        'as'   => 'admin.bundles.removeMapping',
        'uses' => 'Admin\BundlesController@removeMapping'
    ]);
          
    // Valid Postcode Management
    Route::resource('postcode', 'Admin\PostcodeController');

    Route::post('postcode/massDelete', [
        'as'   => 'admin.postcode.massDelete',
        'uses' => 'Admin\PostcodeController@massDelete'
    ]);
    
    Route::get('postcode/download/logs', [
        'as'   => 'admin.postcode.download',
        'uses' => 'Admin\PostcodeController@download'
    ]);
    
    Route::get('/dashboard',[ 
        'as' => 'admin.dashboard', 
        'uses' => 'Admin\PaymentController@getAdminDasboard'
    ]);
    
    Route::get('/releasepayment',[ 
        'as' => 'admin.releasepayment', 
        'uses' => 'Admin\PaymentController@getPendingPayment'
    ]);
    
    Route::post('payment/release/{storeId?}',[ 
        'as' => 'admin.payment.release', 
        'uses' => 'Admin\PaymentController@release'
    ]);
    
    Route::get('payment/storeHistory/{storeId?}/{storePaymentId?}',[ 
        'as' => 'admin.payment.storeHistory', 
        'uses' => 'Admin\PaymentController@getTransactionHistory'
    ]);
    
    Route::get('payment/history/',[ 
        'as' => 'admin.payment.history', 
        'uses' => 'Admin\PaymentController@getPaymentHistoryData'
    ]);
    
    Route::get('payment/storePaymentHistory/{storeId?}',[ 
        'as' => 'admin.payment.storePaymentHistory', 
        'uses' => 'Admin\PaymentController@getstorePaymentHistory'
    ]);

    Route::any('bankdetails', [
        'as'   => 'admin.bank',
        'uses' => 'Admin\UsersController@getUserBankDetails'
    ]);

    Route::any('bankdetails/create', [
        'as'   => 'admin.bank.form',
        'uses' => 'Admin\UsersController@renderBankForm'
    ]);

    Route::any('bankdetails/update', [
        'as'   => 'admin.bank.update',
        'uses' => 'Admin\UsersController@updateBankDetails'
    ]);

    Route::any('bankdetails/getDetails', [
        'as'   => 'admin.bank.get',
        'uses' => 'Admin\UsersController@getDetails'
    ]);

    Route::get('payout/initiate/{userId?}', [
        'as'   => 'admin.payout',
        'uses' => 'Admin\UsersController@renderPayOutForm'
    ]);

    Route::post('payout/initiate', [
        'as'   => 'admin.payout.initiate',
        'uses' => 'Admin\UsersController@initiatePayOut'
    ]);
    
    Route::get('payout/summary', [
        'as'   => 'admin.payout.summary',
        'uses' => 'Admin\PayOutController@getPayOutSummary'
    ]);

    Route::get('payout/detail/{userId}', [
        'as'   => 'admin.payout.detail',
        'uses' => 'Admin\PayOutController@getUserPayOutDetails'
    ]);
    
    Route::any('configurations', [
        'as'   => 'admin.configurations.manage',
        'uses' => 'Admin\ConfigurationsController@manage'
    ]);
    
    Route::post('/getsiteinfo', [
        'as'   => 'admin.getSiteOpeningInfo',
        'uses' => 'Admin\ConfigurationsController@getSiteOpeningInfo'
    ]);

    Route::post('/get-tookan-availability', [
        'as'   => 'admin.getTookanAvailability',
        'uses' => 'Admin\ConfigurationsController@getTookanAvailability'
    ]);

    /**
     * For creating one time admin user.
     */
    Route::get('/createAdmin', [
        "as"    => 'admin.create.admin.user',
        "uses"  => 'PaymentController@createLegalUserAdmin'
    ]);

    Route::get('/changePassword', [
        'as'   => 'admin.changePassword',
        'uses' => 'Admin\UsersController@changePassword'
    ]);

    Route::post('/changePassword', [
        'as'   => 'admin.changePassword.post',
        'uses' => 'Auth\PasswordController@postReset'
    ]);

    // FAQ Management
    Route::resource('faq', 'Admin\FaqController');
    
    Route::post('faq/massDelete', [
        'as'   => 'admin.faq.massDelete',
        'uses' => 'Admin\FaqController@massDelete'
    ]);
    // FAQ Category Management
    Route::get('/faq/category/list',  [
       'as'   => 'admin.faq.category.list',
        'uses' => 'Admin\FaqController@faqCategorylist' 
    ]);
    
    Route::get('/faq/category/add',  [
       'as'   => 'admin.faq.category.add',
        'uses' => 'Admin\FaqController@faqCategoryadd' 
    ]);
    
    Route::post('/faq/category/save',  [
       'as'   => 'admin.faq.category.save',
        'uses' => 'Admin\FaqController@savefaqCat' 
    ]);
    
    Route::get('/faq/category/editcat/{id}',  [
       'as'   => 'admin.faq.category.edit',
        'uses' => 'Admin\FaqController@faqCategoryedit' 
    ]);
    
    Route::post('/faq/category/update',  [
       'as'   => 'admin.faq.category.update',
        'uses' => 'Admin\FaqController@faqCategoryupdate' 
    ]);
    
    Route::get('/configurations/time', [
        'as'   => 'admin.site.time',
        'uses' => 'Admin\ConfigurationsController@getSiteTime'
    ]);
    
    Route::post('/configurations/time', [
        'as'   => 'admin.site.saveTime',
        'uses' => 'Admin\ConfigurationsController@updateSiteTime'
    ]);
    
    Route::get('configurations/tookan', [
        'as'   => 'admin.configurations.tookan.show',
        'uses' => 'Admin\ConfigurationsController@showTokanForm'
    ]);
    
    Route::post('configurations/tookan/save', [
        'as'   => 'admin.configurations.tookan.update',
        'uses' => 'Admin\ConfigurationsController@updateTokanData'
    ]);
       
    // CMS Management
    Route::resource('cms', 'Admin\CmsController');
    
    // BLOG Management
    Route::resource('blog', 'Admin\BlogController');
    
    Route::any('getkeyword', [
        'as'   => 'admin.blog.getkeyword',
        'uses' => 'Admin\BlogController@getAllKeywords'
    ]);
    
    Route::get('get-matching-products', [
        'as'   => 'admin.products.get.matching.products',
        'uses' => 'Admin\ProductsController@getMatchingProducts'
    ]);

    Route::get('get-matching-bundles', [
        'as'   => 'admin.bundles.get.matching',
        'uses' => 'Admin\BundlesController@getMatchingBundles'
    ]);
    
    Route::any('getsavedkeyword', [
        'as'   => 'admin.blog.getsavedkeyword',
        'uses' => 'Admin\BlogController@getSavedKeywords'
    ]);
    
    Route::get('/vendors/time/{storeId?}', [
        'as'   => 'admin.store.time',
        'uses' => 'Admin\ConfigurationsController@getStoreTime'
    ]);
    
    // Locale Management
    Route::resource('locale', 'Admin\LocaleController');
    
    Route::any('locale/get-saved-products', [
        'as'   => 'admin.locale.get.saved.products',
        'uses' => 'Admin\LocaleController@getSavedProducts'
    ]);
    
    Route::any('locale/get-saved-bundles', [
        'as'   => 'admin.locale.get.saved.bundles',
        'uses' => 'Admin\LocaleController@getSavedBundles'
    ]);
    
    // Brand Management
    Route::resource('brand', 'Admin\BrandController');
    
    Route::any('brand/get-saved-products', [
        'as'   => 'admin.brand.get.saved.products',
        'uses' => 'Admin\BrandController@getSavedProducts'
    ]);
    
    Route::any('brand/get-saved-bundles', [
        'as'   => 'admin.brand.get.saved.bundles',
        'uses' => 'Admin\BrandController@getSavedBundles'
    ]);
    
    Route::post('brand/deleteImage', [
        'as' => 'admin.brand.deleteImage',
        'uses' => 'Admin\BrandController@deleteImage'
    ]);
    
    //contact-us Management
    Route::resource('contact', 'Admin\ContactusController');

    Route::get('banners/{type?}', [
        'as'   => 'admin.banners.list',
        'uses' => 'Admin\BannersController@getBannerImages'
    ]);
    
    Route::post('banners/upload', [
        'as'   => 'admin.banners.upload',
        'uses' => 'Admin\BannersController@uploadBannerImage'
    ]);

    Route::delete('banners/destroy', [
        'as'   => 'admin.banners.destroy',
        'uses' => 'Admin\BannersController@deleteBannerImage'
    ]);

    Route::post('banners/setprimary', [
        'as'   => 'admin.banners.setprimary',
        'uses' => 'Admin\BannersController@setBannerPrimaryImage'
    ]);
    
    // Keywords management
    Route::resource('keyword', 'Admin\KeywordController');
    Route::post('keyword/massDelete', [
        'as' => 'admin.keyword.massDelete',
        'uses' => 'Admin\KeywordController@massDelete'
    ]);

});

// Vendors Routes
Route::group([ 'middleware' => ['auth_vendor', 'revalidate'], 'prefix' => 'store'], function () {
    Route::get('/profile',[ 
        'as' => 'store.profile', 
        'uses' => 'StoreController@getProfile'
    ]);
    
    Route::get('/editProfile', [
        'as'   => 'store.editProfile',
        'uses' => 'StoreController@editProfile'
    ]);
    
    Route::post('/saveProfile', [
        'as'   => 'store.saveProfile',
        'uses' => 'StoreController@saveProfile'
    ]);

    Route::post('/saveProduct', [
        'as'   => 'store.products.save',
        'uses' => 'StoreController@saveProduct'
    ]);
    

    Route::get('/myProducts/{id?}', [
        'as'   => 'store.products',
        'uses' => 'StoreController@getProducts'
    ]);
    
    Route::get('/myProducts/cat/{catId}/subcat/{subcatId}', [
        'as'   => 'store.products.subcat.list',
        'uses' => 'StoreController@getProducts'
    ]);

    Route::get('/getSubCatTree/{pCatId}', [
        'as'   => 'store.products.subcat',
        'uses' => 'StoreController@getSubcatTree'
    ]);

    Route::get('/sales',[ 
        'as' => 'store.sales', 
        'uses' => 'StoreController@getSalesPageData'
    ]);
    
    Route::get('/history',[ 
        'as' => 'store.history', 
        'uses' => 'StoreController@getHistoryData'
    ]);
    
    Route::get('/dashboard',[ 
        'as' => 'store.dashboard', 
        'uses' => 'StoreController@getDashboard'
    ]);
    
    Route::get('/orderSearch', [
        'as' => 'store.orderSearch', 
        'uses' => 'StoreController@getorderSearch'
    ]);
    
    Route::post('/orderSearch', [
        'as' => 'store.orderSearch', 
        'uses' => 'StoreController@getorderSearch'
    ]);
    
    Route::post('/updateSession', [
        'as' => 'store.updateSession', 
        'uses' => 'StoreController@updateSession'
    ]);
    
    Route::post('/vendorVerification', [
        'as' => 'store.vendorVerification', 
        'uses' => 'StoreController@vendorVerification'
    ]);

    Route::get('/getSubSubCatTree/{pCatId}', [
        'as'   => 'store.products.subcat.cat',
        'uses' => 'StoreController@getSubSubcatTree'
    ]);
    
    Route::post('/verifyVendor', [
        'as' => 'store.verifyVendor', 
        'uses' => 'StoreController@verifyVendor'
    ]);
    
    Route::post('/verifyVendor', [
        'as' => 'store.verifyVendor', 
        'uses' => 'StoreController@verifyVendor'
    ]);
    
    Route::get('/riderConfirmation', [
        'as' => 'store.riderConfirmation', 
        'uses' => 'StoreController@riderConfirmation'
    ]);
    
    Route::get('/orderData', [
        'as' => 'store.orderData', 
        'uses' => 'StoreController@orderData'
    ]);
    
    Route::get('/orderComplete', [
        'as' => 'store.orderComplete', 
        'uses' => 'StoreController@orderComplete'
    ]);
    
    Route::get('/detailHistory/{id}', [
        'as' => 'store.detailHistory', 
        'uses' => 'StoreController@getDetailedHistory'
    ]);
    
    Route::get('/productdetail/{id}', [
        'as'   => 'store.products.detail',
        'uses' => 'StoreController@getProductDetail'
    ]);
    
    Route::post('/updateprice', [
        'as'   => 'store.updateprice',
        'uses' => 'StoreController@updateStorePrice'
    ]);
    
    Route::post('/uploadImage', [
        "as" => "store.uploadImage",
        "uses" => "StoreController@uploadImage"
    ]);
    
    Route::get('/faq', [
        'as'   => 'store.faq',
        'uses' => 'Admin\FaqController@getFaqs'
    ]);

    Route::any('bankdetails', [
        'as'   => 'store.bank',
        'uses' => 'Admin\UsersController@getUserBankDetails'
    ]);

    Route::any('bankdetails/create', [
        'as'   => 'store.bank.form',
        'uses' => 'Admin\UsersController@renderBankForm'
    ]);

    Route::any('bankdetails/update', [
        'as'   => 'store.bank.update',
        'uses' => 'Admin\UsersController@updateBankDetails'
    ]);

    Route::any('bankdetails/getDetails', [
        'as'   => 'store.bank.get',
        'uses' => 'Admin\UsersController@getDetails'
    ]);
    
    Route::get('payout', [
        'as'   => 'store.payout',
        'uses' => 'Admin\UsersController@renderPayOutForm'
    ]);

    Route::post('payout/initiate', [
        'as'   => 'store.payout.initiate',
        'uses' => 'Admin\UsersController@initiatePayOut'
    ]);
    
    Route::get('/trackorder/{ordernumber}', [
        "as" => "store.order.track",
        "uses" => "StoreController@trackorder"
    ]);

    Route::get('/editAddress', [
        'as'   => 'store.editAddress',
        'uses' => 'StoreController@editAddress'
    ]);

    Route::post('/saveAddress', [
        'as'   => 'store.saveAddress',
        'uses' => 'StoreController@saveAddress'
    ]);
    
    Route::get('/time', [
        'as'   => 'store.time',
        'uses' => 'StoreController@getStoreTime'
    ]);
    
    Route::post('/time', [
        'as'   => 'store.saveTime',
        'uses' => 'StoreController@updateStoreTime'
    ]);

    Route::any('/kyc', [
        'as'   => 'store.kyc.register',
        'uses' => 'StoreController@registerForKyc'
    ]);

    Route::post('/kyc_new', [
        'as'   => 'store.kyc.register.new',
        'uses' => 'StoreController@createVendorLegalAccountNew'
    ]);

    Route::post('/refreshKycDocumentStatus', [
        'as'   => 'store.kyc.document.status.get',
        'uses' => 'StoreController@getKYCDocumentStatus'
    ]);

    Route::post('/kyc/document/upload', [
        "as" => "store.kyc.upload.document",
        "uses" => "StoreController@uploadKycDocument"
    ]);

    Route::get('/changePassword', [
        'as'   => 'store.changePassword',
        'uses' => 'Admin\UsersController@changePassword'
    ]);

    Route::post('/changePassword', [
        'as'   => 'store.changePassword.post',
        'uses' => 'Auth\PasswordController@postReset'
    ]);
    
    Route::get('/bestseller', [
        'as'   => 'store.bestseller',
        'uses' => "StoreController@getBestSeller"
    ]);

    Route::get('/seller_agreement', [
        'as'   => 'store.seller.agreement',
        'uses' => "StoreController@renderSellerAgreement"
    ]);

    Route::get('/product_list', [
        'as'   => 'store.product_list.show',
        'uses' => "StoreController@renderProductList"
    ]);

    Route::get('/courier_agreement', [
        'as'   => 'store.courier.agreement',
        'uses' => "StoreController@renderCourierAgreement"
    ]);
    
    Route::post('/getsiteinfo', [
        'as'   => 'store.getSiteOpeningInfo',
        'uses' => 'Admin\ConfigurationsController@getSiteOpeningInfo'
    ]);

    Route::post('/add-store', [
        'as'   => 'store.add.store',
        'uses' => 'StoreController@addStore'
    ]);

    Route::any('/setsubstore/{subStoreId}', [
        'as'   => 'store.set.substore',
        'uses' => 'StoreController@setDefaultSubStore'
    ]);
    
    Route::get('/page/{url?}/{api?}',  [
        "as" => "Store.page",
        "uses" => "StoreController@getCmsPageContent"
    ]);

});

Route::group([ 'middleware' => ['auth_customer', 'revalidate'], 'prefix' => 'customer'], function () {
    /*Route::get('/orderStatus', function () {
        return view('customer.order-status');
    });*/
    Route::get('/trackorder/{ordernumber}', [
        "as" => "customer.order.track",
        "uses" => "CustomerController@trackorder"
    ]);

    Route::any('/placeorder', [
        'as'   => 'customer.placeorder',
        'uses' => 'CartController@placeOrder'
    ]);
    
    Route::any('/applypromocode', [
        'as'   => 'customer.applypromocode',
        'uses' => 'CartController@applyPromocode'
    ]);

    Route::get('/dashboard', [
        'as'   => 'customer.dashboard',
        'uses' => 'CustomerController@getDashboard'
    ]);
    
    Route::post('/subscribe', [
        'as'   => 'customer.update.subscribe',
        'uses' => 'CustomerController@updateSubscription'
    ]);

    Route::get('/history', [
        'as'   => 'customer.history',
        'uses' => 'CustomerController@getHistoryData'
    ]);

    Route::get('/faq', [
        'as'   => 'customer.faq',
        'uses' => 'Admin\FaqController@getFaqs'
    ]);

    Route::get('/paymentDetails', [
        'as'   => 'customer.paymentDetails',
        'uses' => 'CustomerController@getPaymentDetails'
    ]);

    Route::get('/editProfile', [
        "as" => "customer.editProfile",
        "uses" => "CustomerController@editProfile"
    ]);

    Route::any('/saveProfile', [
        "as" => "customer.saveProfile",
        "uses" => "CustomerController@saveProfile"
    ]);

    Route::post('/uploadImage', [
        "as" => "customer.uploadImage",
        "uses" => "CustomerController@uploadImage"
    ]);

    Route::get('/address', [
        "as" => "customer.address",
        "uses" => "CustomerController@editAddress"
    ]);

    Route::post('/saveAddress', [
        "as" => "customer.saveAddress",
        "uses" => "CustomerController@saveAddress"
    ]);

    Route::get('/payment', [
        "as" => "customer.payment",
        "uses" => "PaymentController@editPayment"
    ]);

    Route::get('/changePassword', [
        'as'   => 'customer.changePassword',
        'uses' => 'Admin\UsersController@changePassword'
    ]);

    Route::post('/changePassword', [
        'as'   => 'customer.changePassword.post',
        'uses' => 'Auth\PasswordController@postReset'
    ]);
    
    Route::get('/page/{url?}/{api?}',  [
        "as"    => "User.page",
        "uses"  => "CustomerController@getCmsCustomerPageContent"
    ]);

});


/**
 * 
 * API Routes
 * 
 */

Route::group(['prefix' => 'api', 'middleware' => ['api:api', 'cors']], function () {

    Route::get('/getOccasionsList', [
        "as" => "api.occasions.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getOccasions'
    ]);

    Route::get('/getCreationsList', [
        "as" => "api.creations.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getCreations'
    ]);

    Route::post('/login', [
        "as" => "api.login",
        "uses" => '\App\Api\V1\Controllers\CustomerController@doLogin'
    ]);

    Route::post('/register', [
        "as" => "api.register",
        "uses" => '\App\Api\V1\Controllers\CustomerController@register'
    ]);
    
    Route::get('/getSubOccasions/{primaryOccasionId}', [
        "as" => "api.suboccasions.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getSubOccasions'
    ]);

    Route::get('/getSubCreations/{primaryCreationId}', [
        "as" => "api.subcreations.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getSubCreations'
    ]);

    Route::get('/getCreationsProducts/{subCreationId}', [
        "as" => "api.subcreations.products.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getCreationsProducts'
    ]);

    Route::get('/getOccasionsProducts/{subCreationId}', [
        "as" => "api.suboccasions.products.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getOccasionsProducts'
    ]);

    Route::post('/forgotPassword', [
        "as" => "api.forgotPassword",
        "uses" => '\App\Api\V1\Controllers\CustomerController@postEmail'
    ]);

    Route::post('/cart/add', [
        "as" => "api.cart.add",
        "uses" => '\App\Api\V1\Controllers\CartController@add'
    ]);

    Route::post('/cart/update', [
        "as" => "api.cart.update",
        "uses" => '\App\Api\V1\Controllers\CartController@update'
    ]);

    Route::post('/cart/remove', [
        "as" => "api.cart.remove",
        "uses" => '\App\Api\V1\Controllers\CartController@remove'
    ]);

    Route::get('/cart', [
        'as'   => 'api.cart.render',
        'uses' => '\App\Api\V1\Controllers\CartController@renderCart'
    ]);
    
    Route::get('/getProductDetail/{productId?}', [
        "as" => "api.products.detail.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getProductDetail'
    ]);
    
    Route::get('/getBundleDetail/{bundleId?}', [
        "as" => "api.bundle.detail.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getBundleDetail'
    ]);
    
    Route::get('/getCategoryList', [
        "as" => "api.category.list.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getCategoryListing'
    ]);

    Route::get('/getSubCatProducts/{subCatId}', [
        "as" => "api.products.subcat.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getSubCatProducts'
    ]);

    Route::get('/getBanners', [
        "as" => "api.home.banners.get",
        "uses" => '\App\Api\V1\Controllers\CommonController@getBanners'
    ]);
    
    Route::get('/validatePostcode/{postCode}', [
        'as'   => 'api.customer.postcode.validate',
        'uses' => '\App\Api\V1\Controllers\CommonController@validatePostcode'
    ]);
    
    Route::get('/getMatchingValidPostcodes/{postCode}', [
        'as'   => 'api.customer.getmatchingpostcodes',
        'uses' => '\App\Api\V1\Controllers\CommonController@getMatchingValidPostcodes'
    ]);
    
    Route::get('/getFooterLinks', [
        'as'   => 'api.footer.links.get',
        'uses' => '\App\Api\V1\Controllers\CommonController@getFooterLinks'
    ]);
    
//    Route::get('/search', [
//        "as" => 'api.customer.search',
//        "uses" => '\App\Api\V1\Controllers\CustomerController@search'
//    ]);
    
    Route::post('/checkout', [
        'as'   => 'api.checkout',
        'uses' => '\App\Api\V1\Controllers\CartController@checkout'
    ]);

    Route::get('/search/{searchTerm}/{pCatId?}', [
        'as'   => 'api.search.get',
        'uses' => '\App\Api\V1\Controllers\CommonController@getSearchProducts'
    ]);

    Route::get('/getMatchedProducts/{searchTerm}', [
        'as'   => 'api.search.matched.get',
        'uses' => '\App\Api\V1\Controllers\CommonController@getMatchedProducts'
    ]);

          
    //routes for logged in user (API)
    
    Route::group(['middleware' => 'jwt-auth'], function () {
        
        Route::get('/logout', [
            "as" => "api.logout",
            "uses" => '\App\Api\V1\Controllers\CustomerController@logout'
        ]);
        
        Route::post('/editProfile', [
            "as" => "api.editProfile",
            "uses" => '\App\Api\V1\Controllers\CustomerController@editProfile'
        ]);
        
        Route::post('/editAddress', [
            "as" => "api.editAddress",
            "uses" => '\App\Api\V1\Controllers\CustomerController@editAddress'
        ]);
        
        Route::post('/uploadImage', [
            "as" => "api.uploadImage",
            "uses" => '\App\Api\V1\Controllers\CustomerController@uploadImage'
        ]);
        
        Route::post('/getOrderList', [
            "as" => "api.order.list.get",
            "uses" => '\App\Api\V1\Controllers\CommonController@getOrderList'
        ]);
        
        Route::post('/ordertrack', [
            "as" => "api.customer.order.track",
            "uses" => "\App\Api\V1\Controllers\CustomerController@ordertrack"
        ]);
        
        Route::post('/changePassword', [
            'as'   => 'api.change.password',
            'uses' => '\App\Api\V1\Controllers\CustomerController@changePassword'
        ]);
        
        Route::post('/getCard', [
            "as" => "api.customer.card",
            "uses" => "\App\Api\V1\Controllers\CustomerController@getCustomerCard"
        ]);

        Route::post('/placeorder', [
            'as'   => 'api.placeorder',
            'uses' => '\App\Api\V1\Controllers\CartController@placeorder'
        ]);

        Route::get('/getUserDataByToken', [
            'as'   => 'api.user.data.token.get',
            'uses' => '\App\Api\V1\Controllers\CustomerController@getUserDataByToken'
        ]);

        Route::get('/page/{url?}/{api?}',  [
            "as" => "User.api.page",
            "uses" => "CustomerController@getCmsCustomerPageContent"
        ]);

    });

});
