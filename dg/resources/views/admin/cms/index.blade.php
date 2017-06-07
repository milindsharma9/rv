@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/cms.manage_cms') }}</h1>
<p>{!! link_to_route('admin.cms.create', trans('admin/cms.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/cms.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>User Type</th>
                                    <th>Published</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cms as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->user_type }}</td>
                                    <td>{{ $row->published }}</td>
                                    <td>
                                        {!! link_to_route('admin.cms.edit', trans('admin/cms.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection