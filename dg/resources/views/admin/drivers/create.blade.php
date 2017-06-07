@extends('admin.layouts.master')

@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="sign-up">
    <div class="col-xs-12">
        <h1>{{ trans('admin/drivers.create-add_new') }}</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
            </div>
        @endif
    </div>
    <div class="col-xs-12">
        @include('partials.driver-apply-form', ['driverFormRoute' => $driverFormRoute])
    </div>
</div>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('alchemy/js') }}/driver_apply.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection
