@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/faq.manage_faq') }}</h1>
<p>{!! link_to_route('admin.faq.create', trans('admin/faq.add_new') , null, array('class' => 'btn btn-success')) !!}
    {!! link_to_route('admin.faq.category.list', trans('admin/faq.manage_faq_cat') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/faq.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>
                                        {{--{!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}--}}
                                    </th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faq as $row)
                                <tr>
                                    <td>
                                            {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                                        </td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $faqCat[$row->category] }}</td>
                                        <td>
                                            {!! link_to_route('admin.faq.edit', trans('admin/faq.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/faq.are_you_sure")."');",  'route' => array('admin.faq.destroy', $row->id))) !!}
                                            {!! Form::submit(trans('admin/faq.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                            {!! Form::close() !!}
                                        </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('admin/faq.delete_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => 'admin.faq.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/faq.are_you_sure') }}')) {
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