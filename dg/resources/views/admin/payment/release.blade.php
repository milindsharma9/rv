@extends('admin.layouts.master')

@section('content')
<h1>Release Payment</h1>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">Payment Release</div>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    <div class="portlet-body">
        @if(isset($pendingStore))
        <table class="table table-striped table-hover table-responsive datatable" id="datatable">
            <thead>
                <tr>
                    <th>Store Name</th>
                    <th>Store Email </th>
                    <th>Amount</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingStore as $row)
                <tr>
                    <td>{{ $row['store_name'] }}</td>
                    <td>{{ $row['email'] }}</td>
                    <td>{{ $row['totalPrice'] }}</td>
                    <td>
                        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('"."Are you sure want to Release Payment?"."');",  'route' => ['admin.payment.release',$row['id']])) !!}
                        {!! Form::submit('Release Payment', array('class' => 'btn btn-xs btn-danger')) !!}
                        {!! Form::close() !!}
                    </td>
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