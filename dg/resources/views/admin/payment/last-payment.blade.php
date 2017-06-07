@extends('admin.layouts.master')

@section('content')
<h1>Payment Release History</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">Payment Release</div>
    </div>
    <div class="portlet-body">
        @if(isset($transHistory))
        <table class="table table-striped table-hover table-responsive datatable" id="datatable">
            <thead>
                <tr>
                    <th>Store Name</th>
                    <th>Store Email </th>
<!--                    <th>Release Date</th>-->
                    <th>Amount Released</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transHistory as $row)
                <tr>
                    @if($row->store_name == '')
                    <?php $row->store_name = $row->email; ?>
                    @endif
                    <td>{!! link_to_route('admin.payment.storePaymentHistory', $row->store_name, array($row->id), array('class' => 'btn btn-xs btn-info')) !!}</td>
                    <td>{{ $row->email }}</td>
<!--                    <td>{{ $row->date }}</td>-->
                    <td>{!! $row->totalPrice!!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        No Record Found
        @endif
    </div>
</div>
@endsection