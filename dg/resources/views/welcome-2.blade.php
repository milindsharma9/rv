@section('title', ' Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('layouts.landing')

@section('content')
@if((CommonHelper::checkForSiteTimings()))
    <div class="store-unavail-error">
       {{ CommonHelper::getOfflineMessage()}}
    </div>
    @include('partials.check-store-site-timing')
    @else
    <div class="store-unavail-error">
        {{ CommonHelper::getOnlineMessage()}}
    </div>
    @endif
<header class="landing-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 landing-logo">
                <a href=""><img src="{{ url('alchemy/images') }}/logo.png"></a>
            </div>
            <div class="col-xs-6 login-direct">
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
            </div>
        </div>
    </div>
</header>
    @php
        $bannerImageDir = config('banner.banner_image_dir');
    @endphp
    <section class="front-page-landing">
        <div class='landing-page-banner mobile-banner visible-xs' style="background-image: url({{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImageMobile }})"></div>
        <div class='landing-page-banner desktop-banner hidden-xs' style="background-image: url({{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }})"></div>
        <div class="container">
        <div class="row">
            <div class="tag-line">
                <h1>Enjoy life</h1>
                <h2>Alcohol, Drinks, snacks — Store to door</h2>
            </div>
            <div class="col-xs-12 zip-form-wrap">
                <form action="{{ route('customer.postcode.validate') }}" method="post">
                    <input type="text" id="postcode" name="postcode" placeholder="Postcode e.g. N4 2PG">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
                    <input type="submit" value="Under 1h Delivery" id="start_button" disabled="disabled"/>
                    @php
                        $isCartEmpty = CommonHelper::isCartEmpty();
                    @endphp
                    @if(!$isCartEmpty)
                        <div class="suggestion-text light">{{trans('messages.postcode_reset_cart_error')}}</div>
                    @endif
                </form>
                <blockquote class="news-advert">
                    We bring the bottle. You make the  fun. Drink responsibly.
                </blockquote>
            </div>
        </div>
    </div>
</section>
@include('partials.work-with-us')
@include('partials.login')
<div id="invalidZip" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="{{ url('alchemy/images') }}/zip-error.svg">
                <h3>Oh no!</h3>
                @php 
                  $postcode_msg = Session::get('postcode_msg');      
                @endphp
                @if(!empty($postcode_msg))
                <p><?php echo $postcode_msg; ?></p>
                @else
                <p>This postcode is not available.<br>Please try again or just browse the site.</p>
                @endif
            </div>
            <div class="modal-footer">
                <div class="action-buttons btn-count-2">
                    {{ link_to_route('home.index', 'Browse')}}
                    <button type="button" data-dismiss="modal">try again</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script src="{{ url('js') }}/login.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>   
    var checkStoreTimeUrl              = "{!! route('customer.site.time')!!}";
    var isServiceable = '<?php echo $isServiceable; ?>';
    if (!isServiceable) {
        var selPostCode = "<?php echo $postcode; ?>";
        var eventName = "postcode_search_no_products_" + selPostCode;
        //console.log(eventName);
        $('#invalidZip').modal();
        Intercom('trackEvent', eventName);
    }
    var registerUrl                   = "{!! route('home.index'); !!}";
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
    $("#postcode").keyup(function() {
        var searchedPostCode = $(this).val();
        if (searchedPostCode.length > 0) {
            $("#start_button").prop("disabled", false);
        } else {
            $("#start_button").prop("disabled", true);
        }
    });
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#postcode" );
            $( "#postcode" ).scrollTop( 0 );
        }
        $( "#postcode" ).autocomplete({
          source: validPostCodeUrl,
          minLength: 1,
          select: function( event, ui ) {
            log( ui.item);
          }
        });
    });
</script>
@endsection
