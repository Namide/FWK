<?php

$body = <<<EOF
		
<article><h1>Bienvenue sur FWK</h1>
<p>Voici votre page d'accueil.</p>
<img width="" height="" src="{{pathCurrentPage:img/example.png}}" alt="image example">
</article>
		
EOF;

$requestsBuild = array('request/test01'=>'First AJAX content');