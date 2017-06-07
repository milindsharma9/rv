<section class="create-event-wrap featured-wrapper single-type-page">
    <div class="big-banner hidden-xs">
        <h1>What's the theme?</h1>
    </div>
    <div class="banner-caption hidden-xs">
        <p>We should all enjoy life more often, and there's always an excuse to celebrate or relax. Here are some our favourite themes to get you in the mood.</p>
    </div>
    <h3 class="title center visible-xs">what’s the theme?</h3>
    @include('customer.partials.event-blocks')
</section>
@include('customer.partials.cart_js')
@section('javascript')
<script>
    var moodUrl         = "{!! route('search.mood')!!}";
    var loadingImgUrl   = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl     = "{!! route('search.occasion')!!}";
</script>
@endsection