<?php

// initialize the pages

$pageList = PageList::getInstance();

$pageList->addDefaultPage( 'basic/homepage' );
$pageList->addError404Page( 'basic/error404' );

$pageList->addPage( 'basic/sitemap' );
$pageList->addDynamicPages( 'basic/homepage', ['fr/dp/1'], ['fr'], ['test french'] );
$pageList->addDynamicPage( 'basic/homepage', 'fr/dp/2', 'en', 'test english' );