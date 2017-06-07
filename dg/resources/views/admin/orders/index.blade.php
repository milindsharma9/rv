@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/orders.manage_orders') }}</h1>
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
        <table class="table table-striped table-hover table-responsive datatable table_layout_fixed" id="datatable-order">
            <thead>
                <tr>
                    <th class="hidden">Order Id</th>
                    <th>Order Id</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Delivery Address</th>
                    <th>Total Amount</th>
                    <th>Items Count</th>
                    <th>Order Date</th>
                    <th>Coupon</th>
                    <th>Order Status</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $statusMap = config('orderStatusAdminMap.order_status_map');
                    $statusButtonMap = config('orderStatusAdminMap.order_status_button_map');
                @endphp
                @foreach ($orders as $row)
                <tr>
                    <td class="hidden">{{ $row->id_sales_order }}</td>
                    <td>{{ $row->orderId }}</td>
                    <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->address }},<br/>{{ $row->city }},<br/>{{ $row->state }},{{ $row->pin }}<br/> Phone:{{ $row->phone }}</td>
                    <td>{{ $row->totalPrice }}</td>
                    <td>{{ $row->items_count }}</td>
                    <td>{{ $row->created_at }}</td>
                    <td>{{ $row->coupon }}</td>
                    <td>
                        @if(isset($statusMap[$row->orderStatus]))
                            {{$statusMap[$row->orderStatus]}}
                        @else
                            {{$row->orderStatus}}
                        @endif
                    </td>
                    <td>
                        @foreach ($row->allowed_operations as $operation)
                            @php
                                $operationName = $operation['name'];
                            @endphp
                            @if(isset($statusButtonMap[$operation['name']]))
                                @php
                                    $operationName = $statusButtonMap[$operation['name']];
                                @endphp
                            @endif
                            {!! link_to_route('admin.orders.edit.new', $operationName, array($row->id_sales_order,$operation['id_order_status']), array('class' => 'btn btn-xs btn-info click-loading')) !!}
                        @endforeach
                        {!! link_to_route('admin.orders.items.details.show', trans('admin/orders.detail'), array($row->orderId), array('class' => 'btn btn-xs btn-info')) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('javascript')
<script>
    $(function () {
    $('.hidden').hide();
    $('#datatable-order').dataTable({
        retrieve: true,
        "iDisplayLength": 100,
        "aaSorting": [],
        "order": [[ 0, "desc" ]],
    });
    $('.click-loading').click(function(){
        $(this).addClass('btn-loading');
    });
    });
</script>
@stop