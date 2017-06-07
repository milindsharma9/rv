<body class="layout-full-width">
    @include('partials.google_tag')
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