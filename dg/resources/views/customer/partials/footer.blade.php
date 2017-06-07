	@include('partials.login')
        @include('partials.social-links')
    </div>
    @if(CommonHelper::checkVisitor() == TRUE)
        @include('partials.cookies-policy')
    @endif
<!-- JavaScripts -->
    <script src="{{ url('alchemy/js') }}/jquery.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/bootstrap.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/owl.carousel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/main.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/login.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/jquery-ui.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script>
        var registerUrl                   = "{!! route('home.index'); !!}";
        var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
        var cartSetDeliveryPostcodeUrl    = "{!! route('customer.delivery.postcode.set')!!}";
    </script>
    <!--
    // AUtosuggest for search
    -->
    <script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/jquery-ui.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script>
        var searchAutosuggestUrl              = "{!! route('customer.search.matched'); !!}";
        $(function() {
            function log1( message ) {
                $( "<div>" ).text( message ).prependTo( "#search" );
                $( "#search" ).scrollTop( 0 );
            }
            $( "#search" ).autocomplete({
              source: searchAutosuggestUrl,
              minLength: 2,
              select: function( event, ui ) {
                log1( ui.item);
              }
            }).autocomplete( "widget" ).addClass( "search-autocomplete" );
        });
    </script>
    <link rel="stylesheet" href="{{ url('css') }}/jquery-ui.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    @yield('javascript')   
    @include('partials.intercom')
</body>
</html>