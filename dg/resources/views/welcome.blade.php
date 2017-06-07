@section('title', ' Seshaasai ')
@section('meta_description', 'Seshaasai ')
@section('meta_keywords', '')
@extends('layouts.landing')

@section('content')

<header class="landing-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 landing-logo">
                <a href=""><img src="{{ url('alchemy/images') }}/logo.png"></a>
            </div>
            <!-- div class="col-xs-6 login-direct">
                @if(!Auth::user())
                <button id="login-btn" data-target="#login-register" data-toggle="modal">Login</button>
                @else
                    @php
                        $route = 'home.index'
                        @endphp
                    @if(Auth::user()->fk_users_role == config('appConstants.admin_role_id'))
                        @php
                        $route = 'admin.dashboard'
                        @endphp
                    @elseif (Auth::user()->fk_users_role == config('appConstants.vendor_role_id'))
                        @php
                        $route = 'store.dashboard'
                        @endphp
                    @endif
                <a id="home-btn" href="{{ route($route) }}">Home</a>
                @endif
            </div -->
        </div>
    </div>
</header>
   
<section class="front-page-landing landing-page-banner-wrapper">
   
    <div class="container">
        <div class="row">
           
          <!----
		  
		   -->
		   <div class="login-section" style="width:400px; margin:0 auto" >
                      
                    <!--    {{--@if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif--}}
                        @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                        @endif -->
                    <div class="modal-body">
                        <h3>Login</h3>
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" id="login-form">
                            {!! csrf_field() !!}
                            <label>Email</label>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input type="email" class="form-control" name="email" id='login-email' value="">
                                <span  class="help-block"></span>
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label>Password</label>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" class="form-control" name="password" id='login-password'>
                                <span  class="help-block"></span>
                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                           
                            <input type="submit" value="Login" id="login">
                        </form>
						 <a href="{!! url('/resetPassword') !!}" class="forgot-pass">Forgot password?</a>
                    </div>
		   <!----
		  
		   -->
		   
		   
        </div>
    </div>
</section>


@include('partials.login')

@endsection
@section('javascript')
<script src="{{ url('js') }}/login.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>  /* 
    var checkStoreTimeUrl              = "{!! route('customer.site.time')!!}";
    var isServiceable = '<?php //echo $isServiceable; ?>';
    if (!isServiceable) {
        var selPostCode = "<?php // echo $postcode; ?>";
        var eventName = "postcode_search_no_products_" + selPostCode;
        //console.log(eventName);
        $('#invalidZip').modal();
        Intercom('trackEvent', eventName);
    }
    var registerUrl                   = "{!! route('home.index'); !!}";
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
    $(".postcode").keyup(function() {
        var searchedPostCode = $(this).val();
        if (searchedPostCode.length > 0) {
            $("#start_button").prop("disabled", false);
        } else {
            $("#start_button").prop("disabled", true);
        }
    });
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( ".postcode" );
            $( ".postcode" ).scrollTop( 0 );
        }
        $( ".postcode" ).autocomplete({
          source: validPostCodeUrl,
          minLength: 1,
          select: function( event, ui ) {
            log( ui.item);
          }
        });
    }); */
</script>
@endsection
