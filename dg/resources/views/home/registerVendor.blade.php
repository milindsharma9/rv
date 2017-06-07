@extends('layouts.default')
@section('header')
<a href="{{ route('store.dashboard') }}">Alchemy</a>
@endsection
@section('content')
<section class="register-content-section">
    <h3 class="section-title"><span>Register</span></h3>
    <div class="container">
            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
            @endif
            <div class="row">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
            </div>
            @include('partials.register-vendor-form')
        </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('alchemy/js') }}/jquery.mousewheel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('alchemy/js') }}/jquery.jscrollpane.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('alchemy/js') }}/bootstrap-select.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>

<script>
var companyDetailsUrl = "{!! route('store.companydetails'); !!}";
var officerDetailsUrl = "{!! route('store.officerdetails'); !!}";
</script>
<script src="{{ url('js') }}/register-vendor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection
