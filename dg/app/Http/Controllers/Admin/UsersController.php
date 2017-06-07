<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Illuminate\Support\Facades\Redirect;
//use App\PaymentModel;
use Illuminate\Support\Facades\Auth;
//use App\BankDetails;
use Exception;
//use App\StoreModel;
use DB;
use Input;
use PDF;
class UsersController extends Controller {

    private $currentUserTemplate = null;

    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;
    private $paginatorvalue;
    /**
     *
     * @var type
     */
    private $paymentModel = null;

    /**
     *
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct() {
        $this->paginatorvalue =  \Config::get('appConstants.paginatorvalue');
      //  $this->mangopay = $mangopay;
        //$this->currentUserTemplate = 'store';
        if (Auth::user()->fk_users_role == \Config::get('appConstants.admin_role_id')) {
            $this->currentUserTemplate = 'admin';
        }
    }
	
	 /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $params='';
        
       if (Input::has('name') || Input::has('email') || Input::has('mobile') || Input::has('companyname')){
        $params['email'] = Input::get('email');
        $params['fullname'] = Input::get('name');
        $params['mobile'] = Input::get('mobile');
        $params['companyname'] = Input::get('companyname');
        }
        $users = User::getSystemUsers($this->paginatorvalue,$params);
        if (Input::has('name') || Input::has('email') || Input::has('mobile')){
             $users->appends( Input::only('email', 'name','mobile','companyname') ); 
           // $users->appends(array('email' => Input::get('email')));
        }
        return view($this->currentUserTemplate.'.users.index', compact('users'));
    }
	/**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.users.create');
    }
	public function getValidationMessages() {
        $aMessage = array(
            'fullname.required'         => 'The Name field is required.',
            'companyname.required'  => 'The CompanyName field is required.',
            'address.required'   => 'The Address field is required.',
			'mobile.numeric'   => 'The Mobile field must be numeric.',
        );
        return $aMessage;
    }
	public function store(Request $request) {
		 $aMessage = $this->getValidationMessages();
        $rules = array(
			  'fullname'    => 'required|max:250',
            'companyname'     => 'required|max:255',
			'address'     => 'required',
            'mobile'         => 'required|numeric|digits_between:7,15',            
            'email'       => 'required|email|unique:users',
			'password'      => 'min:6|confirmed'       
			
            
        );
		$validator = Validator::make($request->all(), $rules, $aMessage);
		if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
		// Users::create($request->all());
		 $userData = array();
		 $userData['fullname'] = $request->input('fullname');
		 $userData['companyname']  = $request->input('companyname');
		 $userData['address'] = $request->input('address');
		 $userData['mobile'] = $request->input('mobile');
		 $userData['email'] = $request->input('email');
		 $userData['password'] = bcrypt($request->input('password'));
		 $userData['fk_users_role'] = 3;
		 
		 if($request->activated){
				$userData['activated'] =1;
		 } else{
			 $userData['activated'] =0;
		 }
		/* $userData[''] = ;
			*/
			DB::table('users')->insert($userData);
			//print_r($userData);
		/* DB::table('users')->insert($userData);*/
                 $request->session()->flash('message', 'User added successfully!');
		 return redirect()->route('admin.users.index');
	}
	
	 /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        try {
            $users = User::findOrFail($id)->where('id', '=', $id)
                ->where('fk_users_role', '=', config('appConstants.user_role_id'))
                ->firstOrFail();
            //$users['userOrderDetails'] = User::getUserOrderDetails($id);
           // $users['productDetails'] = User::fetchOrderProducts($id);
            return view('admin.users.edit', compact('users'));
        } catch (Exception $ex) {
            return redirect()->route($this->currentUserTemplate.'.users.index')->withErrors(trans('admin/users.not_exists'));
        }
    }
        /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $users = User::findOrFail($id);
        $rules = array(
            'fullname'    => 'required|max:250',
            'companyname'     => 'required|max:255',
			'address'     => 'required',
            'email'       => 'required|email|unique:users,email,'. $id,
            'mobile'         => 'required|numeric|digits_between:7,15',
            'password'      => 'min:6|confirmed',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        if(empty($request->get('password'))){
            $users->update($request->only('fullname', 'companyname', 'address','mobile','activated','email'));
        } else {
            $aInputRequest = $request->only('fullname', 'companyname', 'address', 'mobile','password','activated','email');
            $aInputRequest['password'] = bcrypt($aInputRequest['password']);
            $users->update($aInputRequest);
        }
        $request->session()->flash('message', 'User details updated successfully!');
        return redirect()->route('admin.users.index');
    }
     /**
     *
     * @return view Object
     */
    public function changePassword() {
        $routePrefix = $this->currentUserTemplate;
        if (Auth::user()->fk_users_role == \Config::get('appConstants.user_role_id')) {
            $routePrefix = 'customer';
        }
        return view($routePrefix.'.change-password', compact('routePrefix'));
    }
    
    /*
     * for export as excel
     */
       public function excel() {

            // Execute the query used to retrieve the data. In this example
            // we're joining hypothetical users and payments tables, retrieving
            // the payments table's primary key, the user's first and last name, 
            // the user's e-mail address, the amount paid, and the payment
            // timestamp.

           /* $payments = Payment::join('users', 'users.id', '=', 'payments.id')
                ->select(
                  'payments.id', 
                  \DB::raw("concat(users.first_name, ' ', users.last_name) as `name`"), 
                  'users.email', 
                  'payments.total', 
                  'payments.created_at')
                ->get();
                */
           $payments = User::select('id','fullname','email')->get();
            // Initialize the array which will be passed into the Excel
            // generator.
            $paymentsArray = []; 

            // Define the Excel spreadsheet headers
            $paymentsArray[] = ['id', 'fullname','email'];

            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            foreach ($payments as $payment) {
                $paymentsArray[] = $payment->toArray();
            }
               
            // Generate and return the spreadsheet
            \Excel::create('users', function($excel) use ($paymentsArray) {

                // Set the spreadsheet title, creator, and description
                $excel->setTitle('Users');
                $excel->setCreator('Laravel')->setCompany('test company');
                $excel->setDescription('Users file');

                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function($sheet) use ($paymentsArray) {
                    $sheet->fromArray($paymentsArray, null, 'A1', false, false);
                });

            })->download('xlsx');  
            
            
            
            
        }
       // public function downpdf(){
             public function downpdf() {
            $payments = User::select('id','fullname','email')->get();
            // Initialize the array which will be passed into the Excel
            // generator.
            $paymentsArray = []; 
               //https://github.com/barryvdh/laravel-dompdf 
            // Define the Excel spreadsheet headers
            $paymentsArray = "<h1>user list</h1>";
            $paymentsArray .= "<table colspan='1' cellpadding='10' style='border 1px solid' >";
            $paymentsArray .="<tr>";
            $paymentsArray .="<td>id</td>";
            $paymentsArray .="<td>Name</td>";
            $paymentsArray .="<td>Email</td>";
            $paymentsArray .="</tr>";
            foreach ($payments as $payment) {
                $paymentsArray1='';
                $paymentsArray1[0] = $payment->toArray();
                $paymentsArray .="<tr>";
                $paymentsArray .="<td>" .$paymentsArray1[0]['id']."</td>";
                $paymentsArray .="<td>" .$paymentsArray1[0]['fullname']."</td>";
                $paymentsArray .="<td>" .$paymentsArray1[0]['email']."</td>";
                $paymentsArray .="</tr>";
            }
            //$paymentsArray[] = ['id', 'fullname','email'];
            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
            $pdf = PDF::loadHTML($paymentsArray);
            return $pdf->download('users.pdf');
        }
    
    /**
     * Function to Instatntiate Store Model.
     * @return type
     */
    private function getPaymentModel() {
        if ($this->paymentModel == null) {
            $this->paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->paymentModel;
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
//    public function show($id) {
//        $users = User::getSalesOrder($id);
//        return view('admin.users.index', compact('users'));
//    }
    
    
   
    

    
    /**
     * Show the form for creating a Bank Details.
     *
     * @return Response
     */
    public function renderBankForm() {
        $routeName          = $this->currentUserTemplate.".bank.update";
        $formRoute          = route($routeName);
        $aBankDetailType    = config('appConstants.bank_detail_types');
        $isKYCComplete      = true;
        $userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
        if (Auth::user()->fk_users_role == config('appConstants.vendor_role_id')) {
            $storeModel         = new StoreModel();
            $isKYCComplete      = $storeModel->getStoreKYCStatus($userId);
        }
        return view($this->currentUserTemplate.'.bankdetails.bank_detail_form', compact('formRoute', 'aBankDetailType', 'isKYCComplete'));
    }

    /**
     *
     * @return view Object
     */
    public function getUserBankDetails() {
        $bankDetailModel    = new BankDetails();
        $userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
        $userBankDetails    = $bankDetailModel->getUserBankDetails($userId);
        $isKYCComplete      = true;
        if (Auth::user()->fk_users_role == config('appConstants.vendor_role_id')) {
            $storeModel         = new StoreModel();
            $isKYCComplete      = $storeModel->getStoreKYCStatus($userId);
        }
        if ($userBankDetails['hasAccount']) {
            return view($this->currentUserTemplate.'.bankdetails.detail');
        } else {
            $routeName          = $this->currentUserTemplate.".bank.update";
            $formRoute          = route($routeName);
            $aBankDetailType    = config('appConstants.bank_detail_types');
            return view($this->currentUserTemplate.'.bankdetails.bank_detail_form', compact('formRoute', 'aBankDetailType', 'isKYCComplete'));
        }
    }

    /**
     *
     * @param Request $request
     * @return mixed
     */
    public function updateBankDetails(Request $request) {
        $aRequestData   = $request->all();
        $bankType       = $aRequestData['banktype'];
        $aRules         = $this->getValidationRulesForBankType($bankType);
        $validator      = Validator::make($request->all(), $aRules);
        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
        }
        $userId     = isset(Auth::user()->id) ? Auth::user()->id: NULL;
        $response   = $this->getPaymentModel()->createUserBankAccount($userId, $request->all());
        if ($response['status']) {
            return redirect()->route($this->currentUserTemplate.'.bank');
        } else {
            return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($response['message']);
        }
    }

    /**
     * Method to fetch user bank details from MangoPay
     *
     * @return array $response User Bank Details
     */
    public function getDetails() {
        $response = array(
            'status' => false,
            'message' => 'Error',
            'data' => array()
        );
        $bankDetailModel    = new BankDetails();
        $userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
        $userBankDetails    = $bankDetailModel->getUserBankDetails($userId);
        if ($userBankDetails['hasAccount']) {
            $bankDetails    = $this->getPaymentModel()->GetBankAccount($userId, $userBankDetails['details']['userMbankAccId']);
            $bankData       = $bankDetails['data'];
            $response['status']     = $bankDetails['status'];
            $response['data']       = $bankDetails['data'];
            $response['message']    = $bankDetails['message'];
        } else {
            $response['message'] = 'No Bank Details Found';
        }
        return $response;
    }

    /**
     * Method to render Payout Form
     *
     * @return view Object
     */
    public function renderPayOutForm($userId = NULL) {
        $fetchUserDetails = true;
        if (empty($userId) || $userId == config('appConstants.admin_role_id')) {
            $userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
            $fetchUserDetails   = false;
        }
        // Block Payout functionality for vendor users.
        if (Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
            return redirect()
                    ->back();
        }
        $userWalletDetails = $this->getPaymentModel()->getWalletDetail($userId, $fetchUserDetails);
        $viewData = array();
        if ($userWalletDetails['status']) {
            $viewData['amount'] = isset($userWalletDetails['data']->Balance->Amount) ? ($userWalletDetails['data']->Balance->Amount / \Config::get('appConstants.poundToPence')) : 0;
            $viewData['currency'] = isset($userWalletDetails['data']->Balance->Currency) ? ($userWalletDetails['data']->Balance->Currency) : 0;
            return view($this->currentUserTemplate.'.bankdetails.payout_form', compact('viewData', 'userId'));
        } else {
            return view('errors.500');
        }
    }

    /**
     *
     * @param Request $request
     * @return mixed
     */
    public function initiatePayOut(Request $request) {
        $rules = array (
            'transfer_amount'            => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
        }
        $amount             = $request['transfer_amount'];
        $userId             = $request['user_id'];
        $fetchUserDetails   = true;
        if ($userId == config('appConstants.admin_role_id')) {
            $fetchUserDetails   = false;
        }
        $payOutResponse     = $this->getPaymentModel()->payout($amount, $userId, $fetchUserDetails);
        $message            = $payOutResponse['message'];
        if ($payOutResponse['status']) {
            $message = "Payout Success Transaction Id :" .$payOutResponse['data']['transaction_id']." Amount :".$payOutResponse['data']['amount'];
            if (!empty($payOutResponse['data']['raw_response'])) {
                $message .= " Message : ".$payOutResponse['data']['raw_response'];
            }
        }
        \Session::flash('message', $message);
        
//        return redirect()
//                    ->back();
        //return Redirect::route($this->currentUserTemplate.'.payout');
        return Redirect::route('admin.payout', $userId);
    }

    /**
     *
     * @param string $bankType Type of Bank being added
     * @return array $aRules Validation Rules
     */
    public function getValidationRulesForBankType($bankType) {
        $aBankDetailType = config('appConstants.bank_detail_types');
        switch ($bankType) {
            case $aBankDetailType['IBAN']:
                $aRules = array (
                    'owner_name'           => 'required',
                    'IBAN'                 => 'required',
                    'bic'                  => 'required',
                    'owner_add'            => 'required',
                    'owner_City'           => 'required',
                    'owner_PostalCode'     => 'required',
                    'owner_Country'        => 'required',
                );
                break;

            case $aBankDetailType['US']:
                $aRules = array (
                    'owner_name'           => 'required',
                    'AccountNumber'        => 'required',
                    'ABA'                  => 'required',
                    'owner_add'            => 'required',
                    'owner_City'           => 'required',
                    'owner_PostalCode'     => 'required',
                    'owner_Country'        => 'required',
                );
                break;

            case $aBankDetailType['GB']:
                $aRules = array (
                    'owner_name'           => 'required',
                    'AccountNumber'        => 'required',
                    'SortCode'             => 'required',
                    'owner_add'            => 'required',
                    'owner_City'           => 'required',
                    'owner_PostalCode'     => 'required',
                    'owner_Country'        => 'required',
                );
                break;

            case $aBankDetailType['CA']:
                $aRules = array (
                    'owner_name'           => 'required',
                    'AccountNumber'        => 'required',
                    'BankName'             => 'required',
                    'owner_add'            => 'required',
                    'owner_City'           => 'required',
                    'owner_PostalCode'     => 'required',
                    'owner_Country'        => 'required',
                    'InstitutionNumber'    => 'required',
                    'BranchCode'           => 'required',
                );
                break;

            case $aBankDetailType['OTHER']:
                $aRules = array (
                    'owner_name'           => 'required',
                    'AccountNumber'        => 'required',
                    'bic'                  => 'required',
                    'owner_add'            => 'required',
                    'owner_City'           => 'required',
                    'owner_PostalCode'     => 'required',
                    'owner_Country'        => 'required',
                );
                break;

            default:
                $aRules = array();
        }
        return $aRules;
    }

   

}
