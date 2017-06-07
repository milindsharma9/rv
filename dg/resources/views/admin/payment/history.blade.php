@extends('admin.layouts.master')

@section('content')
<h1>Dashboard</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">Payment Release</div>
    </div>
    <div class="portlet-body">
        @if(isset($transHistory))
        <table class="table table-striped table-hover table-responsive datatable" id="datatable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name </th>
                    <th>Quantity</th>
                    <th>Store Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php $total =0;?>
                @foreach ($transHistory as $row)
                <?php $total += $row->totalPrice; ?>
                <tr>
                    <td>{{ $row->orderId }}</td>
                    <td>{{ $row->productName }}</td>
                    <td>{{ $row->quantity }}</td>
                    <td>{{ $row->store_price }}</td>
                    <td>{{ $row->totalPrice }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        No Record Found
        @endif
    </div>
    <div class="portlet-title">
            <div class="caption">Total Payment</div>
        </div>
        <div class="portlet-body"><?php echo number_format($total,2)?></div>
</div>
@endsection