<article>
<h1>Plan du site</h1>


<ul>
	
<?php
	$lang = 'fr';
	$pageList = PageList::getInstance();
	foreach( $pageList->getAllPages($lang) as $pageTemp )
	{
		echo '<li><a href="',BuildUtil::getInstance()->urlPageToAbsUrl( $pageTemp->getUrl() ),'">';
		echo $pageTemp->getTitle(),'</a></li>';
	}
?>

</ul>

</article>
