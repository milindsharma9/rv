@include('customer.partials.header')
@include('customer.partials.occasion')
@include('customer.partials.cart_js')
@section('javascript')
<script>
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
</script>
@endsection
@include('customer.partials.footer')