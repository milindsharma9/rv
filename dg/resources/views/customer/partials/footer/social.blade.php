</main>
<footer class="siteFooter">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 pull-right social-links">
                <ul>
                    <li><a href="{!! config('appConstants.facebook'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/facebook.svg"></a></li>
                    <li><a href="{!! config('appConstants.instagram'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/instagram.svg"></a></li>
                    <li><a href="{!! config('appConstants.twitter'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/twitter.svg"></a></li>
                    <li><a href="{!! config('appConstants.gplus'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/gplus.svg"></a></li>
                    <li><a href="{!! config('appConstants.linkedin'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/linkedin.svg"></a></li>
                    <li><a href="{!! config('appConstants.pinterest'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/pinterest.svg"></a></li>
                    <li><a href="{!! config('appConstants.youtube'); !!}"><img src="{{ url('alchemy/images') }}/youtube.svg"></a></li>
                </ul>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-3 footer-nav">
                <ul>
                    <li><a href="{!! route('api.faq'); !!}" >Faq</a></li>
                    @php
                        $cmsFooterData = \App\Http\Helper\CommonHelper::getFooterCmsLinks();
                    @endphp
                    @foreach($cmsFooterData as $legalPage)
                        <li>
                            <a href="{{route($legalPage['user_type'].'.page', $legalPage['url_path'])}}" >{{$legalPage['title']}}</a>
                        </li>
                    @endforeach
<!--                    <li><a href="{!! route('search.legalterms'); !!}" >Legal</a></li>
                    <li><a href="{!! route('search.cookies') !!}" >Cookies</a></li>
                    <li><a href="{!! route('search.privacypolicy'); !!}" >Privacy</a></li>-->
                    <li><a href="{!! route('home.sitemap'); !!}" >Sitemap</a></li>
                </ul>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-5 col-lg-3 copyright col-lg-offset-1">
                <img src="{{ url('alchemy/images/powered-by-mangopay2.png') }}"><br>
                <span class="hidden-xs">&copy;2016 Alchemy Wings. &copy;2016 London</span>
            </div>
            <div class="col-xs-12 copyright visible-xs">&copy;2016 Alchemy Wings. &copy;2016 London</div>
        </div>
    </div>
</footer>