<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
    <title>Alchemy Wings Feeds</title>
    <link rel="self" href="{{ env('PUBLIC_URL')}}"/>
    <updated><?php echo date('Y-m-d H:i:s');?></updated> 
    @foreach ($products as $product)
        @php 
            $availability = 'Out of Stock'; 
        @endphp
        @if(in_array($product->id, $productAvailable))
            @php 
                $availability = 'In Stock'; 
            @endphp
        @endif
    <entry>
        <g:id>{{ $product->id }}</g:id>
        <g:title><![CDATA[{{ $product->description }}]]></g:title>
        <g:description><![CDATA[{{ trim($product->product_marketing) }}]]></g:description>
        <g:google_product_category>{{  $categoryData[$product->fk_category_id]['google_id']}}</g:google_product_category>
        <g:product_type>{{ $categoryData[$product->fk_category_id]['name'] }}</g:product_type>
        <g:link><![CDATA[{{ route('products.detail', $product->id)}}]]></g:link>
        <g:image_link><![CDATA[{{ CommonHelper::getProductImage($product->id) }}]]></g:image_link>
        <g:condition>New</g:condition>
        <g:availability>{{ $availability }}</g:availability>
        <g:price>{{ $product->price }}</g:price>
        <g:gtin>{{ $product->barcode }}</g:gtin>
    </entry>
    @endforeach
</feed>
