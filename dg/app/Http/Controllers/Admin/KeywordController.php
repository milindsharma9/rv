<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;
use App\Keyword;
use Exception;
use App\Http\Helper\CommonHelper;
use App\Http\Controllers\CartController;

use DB;
use Auth;
use App\EventLog;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $keywords = Keyword::orderby('name', 'asc')->get();
        return view('admin.keyword.index', compact('keywords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.keyword.create');
    }

    public function store(Request $request) {
        DB::enableQueryLog();
        try {
            $aInputRequest  = $request->all();
            $keyword        = $aInputRequest['name'];
            $aInputRequest['machine_name'] = CommonHelper::formatCatName($keyword);
            $rules = array(
                'name'          => 'required',
                'machine_name'  => 'required|unique:keyword',
            );
            $messages = [
                'machine_name.unique'   => 'The name has already been taken.',
                'machine_name.required' => '',
            ];
            $validator = Validator::make($aInputRequest, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            Keyword::create($aInputRequest);
           return redirect()->route('admin.keyword.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $keywords         = Keyword::find($id);
        return view('admin.keyword.edit', compact('keywords'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        DB::enableQueryLog();
        try {
            $keywords = Keyword::findOrFail($id);
            $aInputRequest  = $request->all();
            $keyword        = $aInputRequest['name'];
            $aInputRequest['machine_name'] = CommonHelper::formatCatName($keyword);
            $rules = array(
                'name' => 'required',
                'machine_name'  => 'required|unique:keyword,machine_name,'.$id,
            );
            $messages = [
                'machine_name.unique'   => 'The name has already been taken.',
                'machine_name.required' => '',
            ];
            $validator = Validator::make($aInputRequest, $rules, $messages);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $keywords->update($aInputRequest);
            return redirect()->route('admin.keyword.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

    public function destroy($id) {
        return redirect()->route('admin.keyword.index');
        Keyword::destroy($id);
        \Session::flash('message', 'You have successfully deleted Keyword');
        return Redirect::route('admin.keyword.index');
    }

}
