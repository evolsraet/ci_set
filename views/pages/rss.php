<? echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<rss version="2.0">
<channel>
	<title><?=$this->config->item('site_title')?></title>
	<link><?=base_url()?></link>
	<description>RSS <?=$this->config->item('site_title')?></description>
	<language>ko</language>
	<pubDate><?=date(DATE_RFC822)?></pubDate>
	<lastBuildDate><?=$lastBuildDate?></lastBuildDate>

    <? foreach( (array) $feeds as $feed ): ?>
        <item>
            <title><![CDATA[<?= $feed->title ?>]]></title>
            <guid><?= $feed->url ?></guid>
            <link><?= $feed->url ?></link>
            <description>
                <![CDATA[<?= $feed->description ?>]]>
            </description>            	
            <category><?= $feed->category ?></category>
            <pubDate><?= $feed->pubdate ?></pubDate>
        </item>
    <? endforeach; ?>

</channel>
</rss>