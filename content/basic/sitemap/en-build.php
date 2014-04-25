<article>
<h1>Sitemap</h1>


<ul>
	
<?php
	$lang = 'en';
	$pageList = PageList::getInstance();
	foreach( $pageList->getAllPages($lang) as $pageTemp )
	{
		echo '<li><a href="',PageUtils::urlPageToAbsoluteUrl( $pageTemp->getUrl() ),'">';
		echo $pageTemp->getTitle(),'</a></li>';
	}
?>

</ul>

</article>
