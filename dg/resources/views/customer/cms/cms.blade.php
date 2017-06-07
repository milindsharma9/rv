@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('customer.layouts.customer')
@section('content')
    @if(!empty($cmsData->title))
<!--        <div class="cms_title" style="text-align: center">{{$cmsData->title}}</div>-->
    @endif
    <div class="cms_content">
    <?php 
        if (!empty($cmsData->description)) {
            echo $cmsData->description;
        } 
    ?>
</div>
@endsection