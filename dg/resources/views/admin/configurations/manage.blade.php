@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-12">
        <h1>{{ trans('admin/configurations.manage') }}</h1>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
            </ul>
        </div>
        @endif
    </div>
    <div class="col-sm-4 col-sm-12">
        <p>{!! link_to_route('admin.site.time', trans('Set Opening Time') , null , array('class' => 'btn btn-success')) !!}</p>
        <p>{!! link_to_route('admin.configurations.tookan.show', trans('Tookan Availability') , null , array('class' => 'btn btn-success')) !!}</p>
        <p>&nbsp;</p>
    </div>
</div>
{!! Form::open(array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'POST', 'route' => array('admin.configurations.manage'))) !!}

@foreach($aConfigurations as $configuration)
@foreach($configuration as $configurationKey => $configurationValues)
<div class="form-group">
    {!! Form::label($configurationKey, $configurationValues['label'], array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text($configurationKey, old($configurationKey,$configurationValues['value']), array('class'=>'form-control')) !!}
    </div>
</div>
@endforeach
@endforeach


<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::submit(trans('admin/configurations.update'), array('class' => 'btn btn-primary')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection