<?php

$url = 'fr';       
$title = ( isset($vo) ? $vo : 'Accueil' );
$description = 'Un framework qui déchire!';
$template = 'default';
$header = ' <meta name="robots" content="all" />';
$requests = array( new RequestPage( 'request/test01', TRUE ) );
