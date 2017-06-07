@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/brand.manage_brand') }}</h1>
<p>{!! link_to_route('admin.brand.create', trans('admin/brand.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/brand.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Url Path</th>
                                    <th>Active</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brand as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->url_path }}</td>
                                    <td>{{ $row->active }}</td>
                                    <td>
                                        {!! link_to_route('admin.brand.edit', trans('admin/brand.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection