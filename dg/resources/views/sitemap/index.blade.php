<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($posts as $post)
        <url>
            <loc><![CDATA[{{ $post['url'] }}]]></loc>
            <changefreq>{{ $post['changefreq'] }}</changefreq>
            <priority>{{ $post['priority'] }}</priority>
            @if(isset($post['lastmod']))
                <lastmod>{{ $post['lastmod'] }}</lastmod>
            @endif
        </url>
    @endforeach
</urlset>