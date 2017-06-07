@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('store.layouts.products')
@section('header')
Courier Agreement
@endsection
@section('content')
<section class="store-content-section store-product-section">
     <div class="container">
        <div class="row">
             <div class="cms_content">
                <?php 
                    if (!empty($cmsData->description)) {
                        echo $cmsData->description;
                    }
                ?>
            </div>
        </div>
     </div>
</section>
@endsection