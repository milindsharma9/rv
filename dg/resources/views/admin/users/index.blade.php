@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/users.manage_users') }}</h1>
<p> {!! link_to_route('admin.users.create', 'Add New User', null, array('class' => 'btn btn-success')) !!}</p>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
    </ul>
</div>
@endif
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">{{ trans('admin/users.list') }}</div>
    </div>
    <div class="portlet-body">
        <div class="table-responsive">
            {!! Form::open(['method'=>'GET','route' => 'admin.users.index','class'=>'navbar-form navbar-left','role'=>'search'])  !!}
            
            <div class="input-group">
        <input type="text" class="form-control" name="email" placeholder="email">
    </div>
            <div class="input-group">
        <input type="text" class="form-control" name="companyname" placeholder="companyname">
    </div>
            <div class="input-group">
        <input type="text" class="form-control" name="mobile" placeholder="mobile">
    </div>
            <div class="input-group">
        <input type="text" class="form-control" name="name" placeholder="name"> <span class="input-group-btn">
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search">Search</span></button>
        </span>
    </div>
            {!! Form::close() !!}
        <!--table class="table table-striped table-hover table-responsive datatable table_layout_fixed users_table" id="datatable-users" -->
            <table class="table table-striped table-hover table-responsive  table_layout_fixed users_table" id="datatable-users">
            <thead>
                <tr>
                    <th>User Id</th>
                    <th>Name</th>
                    <th>Company Name</th>
                    <th>Mobile Number</th>
                    <th>Email Address</th>
                    <th>Address</th>
					<th>Status</th>
                   
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>
                      @if($row->fullname != "")
                        {{ $row->fullname }}
                      @else
                      N/A
                      @endif
                    </td>
                    <td>
                      @if($row->companyname != "")
                        {{ $row->companyname }}
                      @else
                      N/A
                      @endif
                    </td>
                    <td>
                      @if($row->mobile != "")
                      {{ $row->mobile }}
                      @else
                      N/A
                      @endif
                    </td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->address }}</td>
                   <td>
                      @if($row->activated == 1)
                       Active
                      @else
                      Inactive
                      @endif
                    </td>
                    <td>
                      {!! link_to_route('admin.users.edit', trans('admin/users.edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        
               
                {{ $users->links() }}
             
    </div>
</div>
@endsection

@section('javascript')
<script>
    $('.hidden').hide();
     /*$('#datatable-users').dataTable({
            retrieve: true,
            paging: false, // set paging false, not require datatable pagination in this case
            "iDisplayLength": 100,
            "aaSorting": [],
            "aoColumnDefs": [
                {'bSortable': false, 'aTargets': [0]}
            ]
        }); */
  /*  $('#datatable-users').dataTable({
        retrieve: true,
        "iDisplayLength": 100,
        "aaSorting": [],
        "order": [[ 0, "desc" ]],
    });*/
</script>
@stop