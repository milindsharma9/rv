@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/bundles.map') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{{--{{ Html::ul($errors->all()) }}--}}
{!! Form::model($bundle, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'POST', 'route' => array('admin.bundles.map.store', $bundle->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$bundle->name), array('class'=>'form-control','disabled' => 'disabled',)) !!}
        
    </div>
</div>

<div class="form-group">
    {!! Form::label('event', 'Events', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        @foreach($eventGroup[0]['name'] as $eventIndex => $event)
            <?php $eventId = $eventGroup[0]['id'][$eventIndex]; ?>
        <ul>
            <li>
                {{ Form::checkbox('event[]', $eventId, in_array($eventId, $bundleMapping['event'])) }} {{$event}}
            @if (isset($eventGroup[$eventId]))
            <ul>
                @foreach($eventGroup[$eventId]['name'] as $subEventIndex => $subEvent)
                <?php $subEventId = $eventGroup[$eventId]['id'][$subEventIndex]; ?>
                <li>
                    {{ Form::checkbox('event[]', $subEventId, in_array($subEventId, $bundleMapping['event'])) }} {{$subEvent}}
                </li>
                @endforeach
                <li>
            </ul>
            @endif
            </li>
        </ul>
        @endforeach
    </div>
</div>
{{--<div class="form-group">
    {!! Form::label('occasion', 'Occasion', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        <ul>
        @foreach($occasions as $occasion)
        <li>
            {{ Form::checkbox('occasion[]', $occasion->id, in_array($occasion->id, $bundleMapping['occasion'])) }} {{$occasion->name}}
        </li>
        @endforeach
        </ul>
    </div>
</div>--}}
<div class="form-group">
    {!! Form::label('occasion', 'Occasion', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        @foreach($occasionGroup[0]['name'] as $occasionIndex => $occasion)
            <?php $occasionId = $occasionGroup[0]['id'][$occasionIndex]; ?>
        <ul>
            <li>
                {{ Form::checkbox('occasion[]', $occasionId, in_array($occasionId, $bundleMapping['occasion'])) }} {{$occasion}}
            @if (isset($occasionGroup[$occasionId]))
            <ul>
                @foreach($occasionGroup[$occasionId]['name'] as $subOccasionIndex => $subOccasion)
                <?php $subOccasionId = $occasionGroup[$occasionId]['id'][$subOccasionIndex]; ?>
                <li>
                    {{ Form::checkbox('occasion[]', $subOccasionId, in_array($subOccasionId, $bundleMapping['occasion'])) }} {{$subOccasion}}
                </li>
                @endforeach
                <li>
            </ul>
            @endif
            </li>
        </ul>
        @endforeach
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/bundles.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.bundles.index', trans('admin/bundles.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection