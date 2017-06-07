@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/payouts.manage_payout_details') }}</h1>

<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/payouts.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>UserName</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Transaction Id</th>
                                    <th>Transaction Date</th>
                                    <th>Transaction Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payOutDetail as $row)
                                <tr>
                                    <td>{{ $row->first_name }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->amount }}</td>
                                    <td>{{ $row->transaction_id }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    @php
                                        $rawData = json_decode($row->rawData, true);
                                        $message = $rawData['ResultMessage'];
                                    @endphp
                                    <td>
                                        {{ $row->status }}
                                        @if (!empty($message))
                                            ({{$message}})
                                        @endif
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection