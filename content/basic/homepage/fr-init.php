<?php

$url = 'fr';       
$title = ( isset($vo) ? $vo : 'Accueil' );
$description = 'L\'framework qui déchire!';
$template = 'default';
$header = ' <meta name="robots" content="all" />';
$requests = array( new RequestPage( 'request/test01', TRUE ) );
$contents = array( 'aaa'=>'yeah l\'framework', "bbb"=>"Tests sur l'slash !" );
