@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/products.manage_products') }}</h1>
<p>{!! link_to_route('admin.products.create', trans('admin/products.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<p>{!! link_to_route('admin.getproductList', trans('Product List') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/products.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    {{--<th>
                                        {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                                    </th>--}}
                                    <th></th>
                                    <!--<th>Name</th>-->
                                    <th>Primary Description</th>
                                    <th>EAN</th>
                                    <th>RRSP</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $row)
                                <tr>
                                    <td>
                                            {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                                        </td>
                                        <!--<td>{{ $row->name }} --  ({!! CommonHelper::formatProductDescription($row->description) !!})</td>-->
                                        <td>{{ $row->description }}</td>
                                        <td>{{ $row->barcode }}</td>
                                        <td>{{ $row->store_price }}</td>
                                        <td>
                                            @if(empty($row->deleted_at))
                                                Active
                                            @else
                                                InActive
                                            @endif
                                        </td>
                                        <td>
                                          @if(strpos(CommonHelper::getProductImage($row->id),'product_default.png')!== FALSE)
                                            No image uploaded
                                          @else
                                            Yes
                                          @endif
                                        </td>
                                        <td>
                                            {!! link_to_route('admin.products.edit', trans('admin/products.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! link_to_route('admin.products.map', trans('admin/products.map'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! link_to_route('admin.products.image.list', trans('admin/products.images'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {{--{!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/products.are_you_sure")."');",  'route' => array('admin.products.destroy', $row->id))) !!}
                                            {!! Form::submit(trans('admin/products.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                            {!! Form::close() !!}--}}
                                        </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('admin/products.delete_checked') }}
                    </button>
                </div>
            </div><br /><br/>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="active">
                        {{ trans('admin/products.activate_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => 'admin.products.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
                <input type="hidden" id="type" name="type">
            {!! Form::close() !!}
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/products.are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    if (mass == true) {
                        send.val('mass');
                    } else {
                        var toDelete = [];
                        $('.single').each(function () {
                            if ($(this).is(":checked")) {
                                toDelete.push($(this).data('id'));
                            }
                        });
                        send.val(JSON.stringify(toDelete));
                        if (toDelete.length == 0) {
                            alert('Please select atleast one checkbox.');
                            return false;
                        }
                    }
                    $('#massDelete').submit();
                }
            });
            $('#active').click(function () {
                if (window.confirm('{{ trans('admin/products.are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    var type = $('#type').val('activate');
                    var toDelete = [];
                    $('.single').each(function () {
                        if ($(this).is(":checked")) {
                            toDelete.push($(this).data('id'));
                        }
                    });
                    send.val(JSON.stringify(toDelete));
                    if (toDelete.length == 0) {
                        alert('Please select atleast one checkbox.');
                        return false;
                    }
                    $('#massDelete').submit();
                }
            });
        });
    </script>
@stop