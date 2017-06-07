@section('title', ' Alchemy Wings - FAQ - Your Questions, Answered')
@section('meta_description', ' All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('store.layouts.products')
@section('header')
FAQs
@endsection
@section('content')
<section class="store-content-section section-faq">
        @include('partials.faq', ['isStore' => true])
</section>
@endsection