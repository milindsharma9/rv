@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/payouts.manage_payout') }}</h1>

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
                                    <th>Amount transferred till date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payOutDetail as $row)
                                <tr>
                                    <td>{{ $row->first_name }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->amountTotal }}</td>
                                    <td>
                                        {!! link_to_route('admin.payout.detail', trans('admin/payouts.view_details'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                        {!! link_to_route('admin.payout', trans('admin/payouts.payout_now'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection