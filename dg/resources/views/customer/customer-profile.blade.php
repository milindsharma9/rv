@section('title')
Alchemy - Dashboard
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="user-profile-info">
    <div class="user-cover" style="background-image: url({{ asset('alchemy/images/profile-banner.jpg')}})"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 user-description">
                <div class="user-description-wrapper">
                    {!! Form::open(array('files' => true,  'id' => 'image-upload', 'method' => 'POST','route' => array('customer.uploadImage'))) !!}
                    <span class="action-pic-upload">
                        <input type="file" onchange="this.form.submit()" name="image">
                    </span>
                    {!! Form::hidden('image_w', 8192) !!}
                    {!! Form::hidden('image_h', 8192) !!}
                    {!! Form::close() !!}
                    @if(isset($userData->image) && $userData->image != '')
                    <div style="background-image: url({{ asset('uploads/'.$fileSubDir) . '/'. $userData->image }});" class="user-image"></div>
                    @elseif ($userData->image == '')
                    <div style="background-image: url({{ asset('alchemy/images/default-store.png')}})" class="user-image"></div>
                    @endif
                    <h3 class="user-name">
                            {!! $userData['first_name'] !!} {!! $userData['last_name'] !!} <span class="hidden-xs">{!! $userData['email'] !!}</span>
                        </h3>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="user-orders">
    <h3 class="section-title"><span>My Orders</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 latest-order visible-xs">
                @if(isset($history))
                <ul>
                    @foreach($history AS $key => $value)
                    <li class="item">
                        <div class="order-info">
                            <span class="orderId">#{!! $value['orderId'] !!}</span>
                            <span class="orderCount">{!! $value['quantity'] !!} items</span>
                            <span class="orderCost">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($value['totalPrice']) !!}</span>
                        </div>
                        <div class="order-thumb">
                            @foreach($value['productDetail'] AS $key => $productDetail)
                            <span><img src="{!! CommonHelper::getProductImage($productDetail->id, true) !!}"></span>
                            @endforeach
                        </div>
                        <a href="{{url('customer/trackorder', [$value['orderId']])}}" class="order-summary-link"></a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            <div class="col-xs-12 latest-order hidden-xs">
             @if(isset($history))
                <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                    <thead>
                        <tr>
                            <th class="hidden">Order Id</th>
                            <th data-orderable="false">Status</th>
                            <th>ID</th>
                            <th data-orderable="false">Date</th>
                            <th data-orderable="false">Time</th>
                            <th data-orderable="false">Total</th>
                            <th data-orderable="false">Store Name/s</th>
                            <th data-orderable="false">Products</th>  
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history AS $key => $value) 
                        @if ($value['orderStatus'] != 'Completed' && $value['orderStatus'] != 'Refunded')
                            @php    $class = 'orderStatusNew'; 
                                    $status = 'New'; 
                            @endphp
                        @else 
                            @php    $class = 'orderStatus' .$value['orderStatus']; 
                                    $status = $value['orderStatus']; 
                            @endphp
                        @endif
                        <tr>
                            <td class="hidden">{{ $value['id_sales_order'] }}</td>
                            <td> <a href="{{url('customer/trackorder', [$value['orderId']])}}" class="order-summary-link"><span class="{!! $class !!}">{!! $status !!}</span></a></td>
                            <td> <span class="orderId">{!! $value['orderId'] !!}</span></td>
                            <td> <span class="orderdate">{!! $value['date'] !!}</span></td>
                            <td> <span class="orderTime">{!! $value['time'] !!}</span></td>
                            <td> <span class="orderCost">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($value['totalPrice']) !!}</span></td>
                            <td> <span class="orderStore">{!! $value['store'] !!}</span></td>
                            <td> <span class="orderCount">{!! $value['quantity'] !!}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="user-account-setting">
    <h3 class="section-title"><span>My Account</span></h3>
    <div class="container">
        <div class="row">
            <div class="store-info-links">
                <ul>
                    @include('customer.partials.dashboard.customer-profile')
                    @include('customer.partials.dashboard.customer-address')
                    @include('customer.partials.dashboard.customer-change-password')
                    @include('customer.partials.dashboard.customer-payment')
                    <li class="menu-legal tree-view">
                        <a href="#">Legal</a>
                        <ul class="tree-child">
                            @foreach($userLegaldata as $legalPage)
                            <li>
                                <a href="{{route($legalPage->user_type.'.page', $legalPage->url_path)}}" >{{$legalPage->title}}</a>
                            </li>
                            @endforeach
                            <li>
                                <?php $checked = $class =''; ?>
                                @if(isset($subscribe) && $subscribe == 1)
                                <?php
                                $checked = "checked='checked'";
                                $class = 'checked';
                                ?>
                                @endif
                                <a>
                                    <label class="check-option {{$class}}">
                                        <input name="suscribe" {{$checked}} type="checkbox" value="suscribed" id="suscribe">  I agree to be contacted for direct marketing purposes.
                                    </label>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-help">
                        <a href="{!! route('api.faq') !!}">Help & FAQs</a>
                    </li>
                    <li class="menu-logout">
                        <a href="{!! url('/logout') !!}">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/customer-profile.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<script src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    var subscribeUrl            = "{{ route('customer.update.subscribe') }}";
    var saveProfileURL          = "{!! route('customer.saveProfile')!!}";
    var saveAddressURL          = "{!! route('customer.saveAddress')!!}";
    var savePasswordURL         = "{!! route('customer.changePassword.post')!!}";
    var checkedStatus;
    $(function () {
        $('input[name=suscribe]').on('change', function () {
            if ($(this).is(":checked")) {
                checkedStatus = 1;
            } else {
                checkedStatus = 0;
            }
            $.ajax({
                url: subscribeUrl,
                type: 'POST',
                data: {is_subscribed: checkedStatus},
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                success: function (data) {
                    if (!data)
                        alert("some error occured please try again!!");
                },
                error: function (data) {
                    alert("some error occured please try again!!");
                }
            });
        });
    });
</script>
<script>
    if($('#datatable tbody tr').length>7){
        $('#datatable').dataTable({
            retrieve: true,
            paging: false,
            bFilter: false,
            bInfo: false,
            scrollY: "400px",
            "iDisplayLength": 100,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/English.json"
            },
            "order": [[ 0, "desc" ]],
        });
    }
    else{
        $('#datatable').dataTable({
            retrieve: true,
            paging: false,
            bFilter: false,
            bInfo: false,
            "iDisplayLength": 100,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/English.json"
            },
            "order": [[ 0, "desc" ]],
        });
    }
</script>
<script>
    var saveCardDefault = "{{ route('customer.savecarddefault') }}";
    $(document).ready(function () {
        $('input:radio[name=card]').change(function () {
            if ($(this).is(':checked')) {
                var $cardId = ($(this).closest("div.card-info").find("input[name='cardId']").val());
                $.ajax({
                    url: saveCardDefault,
                    type: 'POST',
                    data: {'cardId': $cardId},
                    headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                    success: function (data) {
                        if (!data.status) {
                            $('#error-popup').html(data.error);
                            console.log(data.error);
                        }
                    },
                    error: function (data) {
                        console.log(data);
                        alert("some error occured please try again!!");
                    }
                });
            }
        });
    });
</script>
@endsection