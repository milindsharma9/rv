    @include('partials.social-links')
    </div>

    <!-- JavaScripts -->
    <script src="{{ url('alchemy/js') }}/jquery.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/bootstrap.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/owl.carousel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/main.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/jquery-ui.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    @yield('javascript')
    @yield('javascript_1')
    @include('partials.intercom')
</body>
</html>