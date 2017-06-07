@extends('admin.layouts.master')
@section('content')
<h1>{{ trans('admin/faqcategory.manage_faq') }}</h1>
     @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
<p>{!! link_to_route('admin.faq.category.add', trans('admin/faqcategory.add_new') , null, array('class' => 'btn btn-success')) !!}</p>
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/faqcategory.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqCat as $row)
                                <tr>
                                        <td>{{ $row->category_name }}</td>
                                        <td>
                                            {!! link_to_route('admin.faq.category.edit', trans('admin/faqcategory.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                            {!! Form::close() !!}
                                        </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>            
            
            {!! Form::close() !!}
        </div>
</div>
@endsection
