<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
    <title>Alchemy Wings Blog Feeds</title>
    <link rel="self" href="{{ env('PUBLIC_URL')}}"/>
    <updated><?php echo date('Y-m-d H:i:s');?></updated>
    <blogs>
    @foreach ($blogsData['blog'] as $blogData)
        <blog>
            <g:id>{{ $blogData->id }}</g:id>
            <g:title><![CDATA[{{ $blogData->title }}]]></g:title>
            <g:sub_title><![CDATA[{{ $blogData->sub_title }}]]></g:sub_title>
            <g:description><![CDATA[{{ trim($blogData->description) }}]]></g:description>
            <g:posted_on><![CDATA[{{ $blogData->created_at }}]]></g:posted_on>
            <g:link><![CDATA[{{ route('common.blog', $blogData->url_path)}}]]></g:link>
            <g:image_link><![CDATA[{{ url('uploads/blog/'). "/" .$blogData->image }}]]></g:image_link>
            <g:image_thumb_link><![CDATA[{{ url('uploads/blog/'). "/" .$blogData->image_thumb }}]]></g:image_thumb_link>
        </blog>
    @endforeach
    </blogs>
    <places>
    @foreach ($blogsData['place'] as $blogData)
        <place>
            <g:id>{{ $blogData->id }}</g:id>
            <g:title><![CDATA[{{ $blogData->title }}]]></g:title>
            <g:description><![CDATA[{{ trim($blogData->description) }}]]></g:description>
            <g:link><![CDATA[{{ route('common.places', $blogData->url_path)}}]]></g:link>
            <g:place_name><![CDATA[{{ $blogData->location}}]]></g:place_name>
            <g:image_link><![CDATA[{{ url('uploads/blog/') . "/" . $blogData->image }}]]></g:image_link>
            <g:image_thumb_link><![CDATA[{{ url('uploads/blog/'). "/" .$blogData->image_thumb }}]]></g:image_thumb_link>
        </place>
    @endforeach
    </places>
    <events>
    @foreach ($blogsData['event'] as $blogData)
        <event>
            <g:id>{{ $blogData->id }}</g:id>
            <g:title><![CDATA[{{ $blogData->title }}]]></g:title>
            <g:start_date><![CDATA[{{ $blogData->start_date }}]]></g:start_date>
            <g:end_date><![CDATA[{{ $blogData->end_date }}]]></g:end_date>
            <g:description><![CDATA[{{ trim($blogData->description) }}]]></g:description>
            <g:link><![CDATA[{{ route('common.events', $blogData->url_path)}}]]></g:link>
            <g:image_link><![CDATA[{{ url('uploads/blog'). "/" . $blogData->image }}]]></g:image_link>
            <g:image_thumb_link><![CDATA[{{ url('uploads/blog/'). "/" .$blogData->image_thumb }}]]></g:image_thumb_link>
        </event>
    @endforeach
    </events>
</feed>
