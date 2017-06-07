@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/keyword.manage_keyword') }}</h1>
<p>{!! link_to_route('admin.keyword.create', trans('admin/keyword.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/keyword.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable_keyword">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($keywords as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>
                            {!! link_to_route('admin.keyword.edit', trans('admin/keyword.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                        </td>
                    </tr>
                     @endforeach
                </tbody>
            </table>
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $('#datatable_keyword').dataTable({
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
                if (window.confirm('{{ trans('admin/keyword.are_you_sure') }}')) {
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