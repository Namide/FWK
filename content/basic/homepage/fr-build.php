<?php

	$dynamicData = (isset($vo)?$vo:'FWK');
	$requestsContent = array( 'request/test01' => 'First AJAX content' );

?>

<article>
	<h1>Bienvenue sur <?=$dynamicData?></h1>
	<p>Voici votre page d'accueil.</p>
	<img width="" height="" src="{{pathCurrentPage:img/example.png}}" alt="image example">
	<br>
	<a href="{{idPageToAbsoluteUrl:basic/homepage}}?test=getter ok">Test GETTER</a>
	<br>
	<?= (UrlUtil::getInstance()->hasGet('test')?UrlUtil::getInstance()->getGet('test'):'' ) ?>
</article>
		
