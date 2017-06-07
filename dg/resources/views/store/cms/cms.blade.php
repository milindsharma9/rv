@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('store.layouts.products')
@section('content')
<section class="store-content-section store-agreement-section">
    @if(!empty($cmsData->title))
<!--        <div class="cms_title" style="text-align: center">{{$cmsData->title}}</div>-->
    @endif
    <div class="cms_content">
        <?php 
            if (!empty($cmsData->description)) {
                if (!empty($storeDetails)) {
                    echo str_replace(
                            array(
                                "@COMPANY_NAME@",
                                "@COMPANY_NUMBER@",
                                "@COMPANY_ADDRESS@",
                                "@STORE_NAME@",
                                "@STORE_ADDRESS@"
                            ),
                            array(
                                $storeDetails['company_name'],
                                $storeDetails['company_number'],
                                $storeDetails['company_address'],
                                $storeDetails['store_name'],
                                $storeDetails['store_address'],
                            ),
                            $cmsData->description);
                } else {
                    echo $cmsData->description;
                }
            } 
        ?>
    </div>
</section>
@endsection