@section('title')
Seshaashai Admin - Change Password
@endsection
@extends('admin.layouts.master')
@section('content')
    <h3>Change Password :</h3>
    <div class="row">
        @include('partials.change-password')       
    </div>
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
@endsection