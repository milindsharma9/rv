@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/blog.manage_blog') }}</h1>
<p>{!! link_to_route('admin.blog.create', trans('admin/blog.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/blog.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Blog Type</th>
                                    <th>Published</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $blog_type = config('blog.type') @endphp
                                @foreach ($blog as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $blog_type[$row->type] }}</td>
                                    <td>{{ $row->published }}</td>
                                    <td>
                                        {!! link_to_route('admin.blog.edit', trans('admin/blog.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
        </div>
</div>
@endsection