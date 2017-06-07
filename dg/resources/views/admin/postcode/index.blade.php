@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/postcode.manage_postcode') }}</h1>
<p>{!! link_to_route('admin.postcode.create', trans('admin/postcode.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<p>{!! link_to_route('admin.postcode.download', trans('admin/postcode.download_logs') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/postcode.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable_postcode">
                            <thead>
                                <tr>
                                    <th>
                                        {{--{!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}--}}
                                    </th>
                                    <th>Postcode</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($validPostcodes as $row)
                                <tr>
                                    <td>
                                            {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                                        </td>
                                        <td>{{ $row->postcode }}</td>
                                        <td>
                                            {!! link_to_route('admin.postcode.edit', trans('admin/postcode.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/postcode.are_you_sure")."');",  'route' => array('admin.postcode.destroy', $row->id))) !!}
                                            {!! Form::submit(trans('admin/postcode.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                            {!! Form::close() !!}
                                        </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('admin/postcode.delete_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => 'admin.postcode.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
            @if (!empty($validPostcodes))
                    {!! $validPostcodes->links() !!}
            @endif
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $('#datatable_postcode').dataTable({
            retrieve: true,
            paging: false, // set paging false, not require datatable pagination in this case
            "iDisplayLength": 100,
            "aaSorting": [],
            "aoColumnDefs": [
                {'bSortable': false, 'aTargets': [0]}
            ]
        });
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/postcode.are_you_sure') }}')) {
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