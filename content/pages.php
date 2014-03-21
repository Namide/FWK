<?php

$pageList = PageList::getInstance();

$pageList->addDefaultPage( 'basic/homepage' );
$pageList->addError404Page( 'basic/error404' );

$pageList->addPage( 'basic/sitemap' );
$pageList->addDynamicPage( 'basic/homepage', ['fr/dp/1'], ['fr'], ['test french'] );