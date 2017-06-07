@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/locale.manage_locale') }}</h1>
<p>{!! link_to_route('admin.locale.create', trans('admin/locale.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/locale.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Sub Title</th>
                                    <th>Active</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locale as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->sub_title }}</td>
                                    <td>{{ $row->active }}</td>
                                    <td>
                                        {!! link_to_route('admin.locale.edit', trans('admin/locale.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection