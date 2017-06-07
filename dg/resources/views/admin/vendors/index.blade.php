@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/vendors.manage_vendors') }}</h1>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
    </ul>
</div>
@endif
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">{{ trans('admin/vendors.list') }}</div>
    </div>
    <div class="portlet-body">
        <table class="table table-striped table-hover table-responsive datatable table_layout_fixed" id="datatable">
            <thead>
                <tr>
                    {{--<th>
                                        {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                                    </th>--}}
                    {{--<th></th>--}}
                    <th>UserId </th>
                    <th>First Name</th>
                    <th>Surname</th>
                    <th>Store Name</th>
                    <th>Store Address</th>
                    <th>Phone </th>
                    <th>Email</th>
                    <th>Status</th>
                    <!--<th>OFF/ON</th>
                    <th>Orders</th>
                    <th>TVOP</th>
                    <th>AOV</th>
                    <th>Products</th>-->
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $row)
                <tr>
                    {{--<td>
                        {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                    </td>--}}
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->first_name }}</td>
                    <td>{{ $row->last_name  }}</td>
                    <td>{{ $row->store_name }}</td>
                    <td>{{ $row->address }} {{ $row->town  }},
                        {{ $row->country  }} {{ $row->post_code  }}</td>
                    <td>{{ $row->phone }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->activte_status }}</td>
                    {{--<td>{{ $row->store_status }}</td>
                    <td>{{ $row->salesData->total_order }}</td>
                    <td>{{ $row->salesData->total_value  }}</td>
                    <td>{{ $row->salesData->avg_order  }}</td>
                    <td>{{ $row->product_listed }}</td>--}}
                    <td>
                        {!! link_to_route('admin.vendors.edit', trans('admin/vendors.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                        {!! link_to_route('admin.vendors.stores.list', trans('admin/vendors.store_details'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    {{--<div class="col-xs-12">
        <button class="btn btn-danger" id="deactive">
            {{ trans('Stores (OFF)') }}
        </button>
        <button class="btn btn-danger" id="active">
            {{ trans('Stores (ON)') }}
        </button>
    </div>--}}
</div>
{!! Form::open(['route' => 'admin.vendors.massUpdate', 'method' => 'post', 'id' => 'massUpdate']) !!}
<input type="hidden" id="send" name="toUpdate">
<input type="hidden" id="type" name="type">
{!! Form::close() !!}
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $('#active').click(function() {
            var type = $('#type').val('activate');
            vendor_update()
        });

        $('#deactive').click(function() {
            var type = $('#type').val('deactive');
            vendor_update()

        });
    });
    function vendor_update() {
        if (window.confirm('{{ trans('admin/vendors.are_you_sure') }}')) {
            var send = $('#send');
            var mass = $('.mass').is(":checked");
            var toUpdate = [];
            $('.single').each(function() {
                if ($(this).is(":checked")) {
                    toUpdate.push($(this).data('id'));
                }
            });
            send.val(JSON.stringify(toUpdate));
            if (toUpdate.length == 0) {
                alert('Please select atleast one checkbox.');
                return false;
            }
            $('#massUpdate').submit();
        }
    }
</script>
@endsection
