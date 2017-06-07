@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/products.map') }}</h1>

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
{!! Form::model($products, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'POST', 'route' => array('admin.products.map.store', $products->Pid))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$products->name. "--(". CommonHelper::formatProductDescription($products->description). ")"), array('class'=>'form-control','disabled' => 'disabled',)) !!}
        
    </div>
</div>

<div class="form-group">
    {!! Form::label('category', 'Category', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        @foreach($catGroup[0]['name'] as $catIndex => $category)
            <?php $categoryId = $catGroup[0]['id'][$catIndex]; ?>
        <ul>
            <li>
                {{ Form::checkbox('category[]', $categoryId, in_array($categoryId, $prodMapping['category'])) }} {{$category}}
            @if (isset($catGroup[$categoryId]))
            <ul>
                @foreach($catGroup[$categoryId]['name'] as $subCatIndex => $subCategory)
                <?php $subCategoryId = $catGroup[$categoryId]['id'][$subCatIndex]; ?>
                <li>
                    {{ Form::checkbox('category[]', $subCategoryId, in_array($subCategoryId, $prodMapping['category'])) }} {{$subCategory}}
                    @if (isset($catGroup[$subCategoryId]))
                    <ul>
                        @foreach($catGroup[$subCategoryId]['name'] as $subSubCatIndex => $subSubCategory)
                        <?php $subSubCategoryId = $catGroup[$subCategoryId]['id'][$subSubCatIndex]; ?>
                        <li>
                            {{ Form::checkbox('category[]', $subSubCategoryId, in_array($subSubCategoryId, $prodMapping['category'])) }} {{$subSubCategory}}
                        </li>
                        @endforeach
                    </ul>
                    @endif
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
    {!! Form::label('event', 'Events', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        <ul>
        @foreach($events as $event)
            <li>
                {{ Form::checkbox('event[]', $event->id, in_array($event->id, $prodMapping['event'])) }}  {{$event->name}}
            </li>
        @endforeach
        </ul>
    </div>
</div>
--}}
<div class="form-group">
    {!! Form::label('event', 'Events', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10 category-listing">
        @foreach($eventGroup[0]['name'] as $eventIndex => $event)
            <?php $eventId = $eventGroup[0]['id'][$eventIndex]; ?>
        <ul>
            <li>
                {{ Form::checkbox('event[]', $eventId, in_array($eventId, $prodMapping['event'])) }} {{$event}}
            @if (isset($eventGroup[$eventId]))
            <ul>
                @foreach($eventGroup[$eventId]['name'] as $subEventIndex => $subEvent)
                <?php $subEventId = $eventGroup[$eventId]['id'][$subEventIndex]; ?>
                <li>
                    {{ Form::checkbox('event[]', $subEventId, in_array($subEventId, $prodMapping['event'])) }} {{$subEvent}}
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
            {{ Form::checkbox('occasion[]', $occasion->id, in_array($occasion->id, $prodMapping['occasion'])) }} {{$occasion->name}}
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
                {{ Form::checkbox('occasion[]', $occasionId, in_array($occasionId, $prodMapping['occasion'])) }} {{$occasion}}
            @if (isset($occasionGroup[$occasionId]))
            <ul>
                @foreach($occasionGroup[$occasionId]['name'] as $subOccasionIndex => $subOccasion)
                <?php $subOccasionId = $occasionGroup[$occasionId]['id'][$subOccasionIndex]; ?>
                <li>
                    {{ Form::checkbox('occasion[]', $subOccasionId, in_array($subOccasionId, $prodMapping['occasion'])) }} {{$subOccasion}}
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
      {!! Form::submit(trans('admin/products.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.products.index', trans('admin/products.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection