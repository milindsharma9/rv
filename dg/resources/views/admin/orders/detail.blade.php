@extends('admin.layouts.master')

@section('content')
<div class="row order-summary">
    <div class="col-xs-12">
        <h1>Order : {{$orderDetails['orderNumber']}}</h1>
    </div>
    <div class="col-sm-6 col-xs-12">
        <p class="order-sum">{{ config('appConstants.currency_sign') . $orderDetails['orderTotal']}}</p>
    </div>
    <div class="col-sm-6 col-xs-12">
        <div class="customer-name">{{$orderDetails['customer_name']}}</div>
        <div class="customer-address">{{$orderDetails['orderAddress']}}</div>
    </div>
</div>
<h1>{{ trans('admin/orders.order_items') }}</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">{{ trans('admin/orders.list') }}</div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    <div class="portlet-body">
        <table class="table table-striped table-hover table-responsive datatable" id="datatable-store">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>StoreName</th>
                    <th>Store Address</th>
                    <th>IsBundle</th>
                </tr>
            </thead>
            
            <tbody>
                @foreach ($orderDetails['products'] as $row)
                <tr>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['count'] }}</td>
                    <td>{{ $row['store_name'] }}</td>
                    <td>{{ $row['store_address'] }}</td>
                    <td>False</td>
                </tr>
                @endforeach
                @foreach ($orderDetails['bundle'] as $bundleRow)
                    @foreach ($bundleRow['product'] as $row)
                        <tr>
                            <td>{{ $row['description'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{$row['store_name']}}</td>
                            <td>{{$row['store_address']}}</td>
                            <td>True {{$bundleRow['name']}}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<h1>{{ trans('admin/orders.product_collection') }}</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">{{ trans('admin/orders.list') }}</div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    <div class="portlet-body">
        <table class="table table-striped table-hover table-responsive datatable" id="datatable-store">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Store ID</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderDetails['collected_products_details'] as $row)
                <tr>
                    <td>{{ $row['driver_email'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['count'] }}</td>
                    <td>{{ $row['store_name'] }}</td>
                    <td>{{ $row['collected_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('javascript')
<script>
    $('.hidden').hide();
    $('#datatable-order').dataTable({
        retrieve: true,
        "iDisplayLength": 100,
        "aaSorting": [],
        "order": [[ 0, "desc" ]],
    });
</script>
@stop