@include('partials.check-store-site-timing')
@include('customer.partials.selected-location-postcode')
@include('customer.partials.footer.social')
@include('partials.login')
@include('customer.partials.cart-modal')
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
        var cartAddUrl              = "{!! route('customer.cart.add')!!}";
        var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
        $(function() {
            function log1( message ) {
                $( "<div>" ).text( message ).prependTo( "#search" );
                $( "#search" ).scrollTop( 0 );
            }
            /*$( "#search" ).autocomplete({
              source: searchAutosuggestUrl,
              minLength: 2,
              select: function( event, ui ) {
                log1( ui.item);
              }
            }).autocomplete( "widget" ).addClass( "search-autocomplete" );*/

            /*$(document).on('keyup','#search',function(){
                if($(this).val().length > 4){
                    $('.search-result').addClass('show-search');
                    //$('.search-result').addClass('loading');
                }
                else {
                    $('.search-result').removeClass('show-search');
                }
            });*/
            
            //
            
            var textInput = document.getElementById('search');
            // Init a timeout variable to be used below
            var timeout = null;
            // Listen for keystroke events
            textInput.onkeyup = function (e) {
                // Clear the timeout if it has already been set.
                // This will prevent the previous task from executing
                // if it has been less than <MILLISECONDS>
                clearTimeout(timeout);
                // Make a new timeout set to go off in 800ms
                timeout = setTimeout(function () {
                    //console.log('Input Value:', textInput.value);
                    // fn starts
                    if(textInput.value.length > 3){
                        $('.search-result').addClass('show-search');
                        $('.search-result').addClass('loading');
                        $.ajax({
                            url: searchAutosuggestUrl +"?term="+textInput.value,
                            method: 'GET',
                            success: function(result) {
                                if (result.status) {
                                    $('#search_content_data').html(result.html_content);
                                } else {
                                    $('#search_content_data').html(result.html_content);
                                   //console.log(result);
                                   //$('.search-result').addClass('loading');
                                }
                                $('.search-result').removeClass('loading');
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                //alert("Some error. Please try refreshing page.");
                                $('.search-result').removeClass('loading');
                            }
                        });
                    }
                    // fn ends
                }, 500);
            };
            
            //
            
        });
    </script>
    <link rel="stylesheet" href="{{ url('css') }}/jquery-ui.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    @yield('javascript')
    @yield('footer-scripts')
    @include('partials.intercom')
</body>
</html>