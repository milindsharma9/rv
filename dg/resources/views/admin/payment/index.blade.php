@extends('admin.layouts.master')

@section('content')
<h1>Dashboard</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">Welcome Back Admin</div>
    </div>
    <div class="portlet-body">
        
    </div>
<!--    <div class="portlet-title">
        <div class="caption">Payment Release This Month</div>
    </div>
    <div class="portlet-body">
        @if(isset($releasedStore))
        <table class="table table-striped table-hover table-responsive datatable" id="datatable">
            <thead>
                <tr>
                    <th>Store Name</th>
                    <th>Store Email </th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($releasedStore as $row)
                <tr>
                    @if($row->store_name == '')
                    <?php $row->store_name = $row->email; ?>
                    @endif
                    <td>{!! link_to_route('admin.payment.storeHistory', $row->store_name, array($row->id), array('class' => 'btn btn-xs btn-info')) !!}</td>
                    <td>{{ $row->email }}</td>
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
        <div class="caption">Payment Pending This Month</div>
    </div>
    <div class="portlet-body">
        @if(isset($pendingStore))
        <table class="table table-striped table-hover table-responsive datatable" id="datatable">
            <thead>
                <tr>
                    <th>Store Name</th>
                    <th>Store Email </th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingStore as $row)
                <tr>
                    @if($row['store_name'] == '')
                    <?php $row['store_name'] = $row['email']; ?>
                    @endif
                    <td>{!! link_to_route('admin.payment.storeHistory', $row['store_name'], array($row['id']), array('class' => 'btn btn-xs btn-info')) !!}</td>
                    <td>{{ $row['email'] }}</td>
                    <td>{{ $row['totalPrice'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        No Record Found
        @endif
    </div>-->
</div>
@endsection