@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/drivers.manage_drivers') }}</h1>
<p>{!! link_to_route('admin.drivers.create', trans('admin/drivers.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/drivers.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>
                                        {{--{!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}--}}
                                    </th>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Status</th>
<!--                                    <th>Image</th>-->
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drivers as $row)
                                    @php
                                        $activateText = 'Inactive';
                                    @endphp
                                    @if ($row->activated == 1)
                                        @php
                                            $activateText = 'Active';
                                        @endphp
                                    @endif
                                <tr>
                                    <td>
                                            {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                                        </td>
                                        <td>{{ $row->email }}</td>
                                        <td>{{ $row->first_name }} {{ $row->last_name  }}</td>
                                        <td>{{ $row->phone }}</td>
                                        <td>{{ $activateText }}</td>
<!--                                        <td>@if($row->image != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $row->image }}">@endif</td>-->
                                        <td>
                                            {!! link_to_route('admin.drivers.edit', trans('admin/drivers.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/drivers.are_you_sure")."');",  'route' => array('admin.drivers.destroy', $row->id))) !!}
                                            {!! Form::submit(trans('admin/drivers.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                            {!! Form::close() !!}
                                        </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('admin/drivers.delete_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => 'admin.drivers.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/drivers.are_you_sure') }}')) {
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
        });
    </script>
@stop