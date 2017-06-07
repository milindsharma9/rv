@section('title', ' Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('layouts.simple-header-layout')

@section('content')
<section class="siteBanner-section-title" style="background-image:url({{ url('alchemy/images') }}/partner-banner.png);">
    <img src="{{ url('alchemy/images') }}/partner-banner.png"/>
    <div class="banner-title">
        <h1>Partners and press</h1>
        <h3>We're always looking for ways to connect with Londoners!</h3>
    </div>
</section>
<section class="contact-info">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                {!! Form::open(array('files' => false, 'route' => 'common.save.contact', 'id' => 'contact-us-form', 'method' => 'POST')) !!}
                <div class="form-group">
                    <label>Name</label>
                    {!! Form::text('name', old('name'), array('placeholder' => 'Your name')) !!}
                </div>
                <div class="form-group">
                    <label>Email</label>
                    {!! Form::text('email', old('email'), array('placeholder' => 'Your email')) !!}
                </div>
                <div class="form-group">
                    <label>Message</label>
                    {!! Form::textarea('message', NULL,array('placeholder' => 'Your message here')) !!}
                </div>
                <div class="form-group">
                    {!! captcha_image_html('ContactCaptcha') !!}
                    <input type="text"id="CaptchaCode" name="CaptchaCode">
                </div>
                {!! Form::submit('Send', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
            <div class="section-title visible-xs"><span>Contact</span></div>
            <div class="col-xs-12 col-sm-6">
                <address>{!! config('appConstants.contact_address')!!}</address>
                <!--<p><strong>Customer Service:</strong><a class="phone-link" href="tel:{!! config('appConstants.contact_service'); !!}">{!! config('appConstants.contact_service')!!}</a></p>-->
                <p><strong>For press enquiries please contact </strong><a href="mailto:{!! config('appConstants.contact_email'); !!}">{!! config('appConstants.contact_email')!!}</a></p>
                <p><strong>or by phone on </strong><a class="phone-link" href="tel:{!! config('appConstants.contact_service'); !!}">{!! config('appConstants.contact_service')!!}</a></p>
            </div>
        </div>
    </div>
</section>
@include('partials.work-with-us')
@include('partials.login')
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    $(function () {
        $('a[title="BotDetect CAPTCHA Library for Laravel"]').remove();
        $("#contact-us-form").validate({
            focusInvalid: true,
            debug: true,
            rules: {
                name: "required",
                email: {required: true, email: true},
                message: 'required',
                CaptchaCode: 'required',
            },
            messages: {
                name: "Name required",
                message: 'Message required',
                email: {
                    required: 'Email required',
                    email: "Invalid email",
                },
                CaptchaCode : "Captcha required"
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    });
</script>
@endsection