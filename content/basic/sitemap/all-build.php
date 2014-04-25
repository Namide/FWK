<?php


echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';


$pageList = PageList::getInstance();
foreach( $pageList->getAllPages( 'all' ) as $pageTemp ) { ?>

	<url>
		<loc><?=PageUtils::urlPageToAbsoluteUrl( $pageTemp->getUrl() )?></loc>
		<?php if ( $pageTemp->hasContent('sitemapPriority') ) { ?>
			<priority><?=$pageTemp->getContent('sitemapPriority')?></priority>
		<?php } else { ?>
			<priority>0.5</priority>
		<?php } ?>

	</url>

<?php } ?>

</urlset>

