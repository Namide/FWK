<?php

if ( $_DEBUG ) error_reporting(E_ALL);

if ( $_CACHE )
{
	include_once $_SYSTEM_DIRECTORY.'init/Cache.php';
	$cache = new Cache();
	
	
	if( $cache->isCached() )
	{
		$cache->echoCache();
		
		if ( $_DEBUG ) echo '<!-- load cache time: ', number_format( microtime(true) - $timestart , 3) , 's -->';
		
		exit();
	}
	elseif( $cache->isCachable() )
	{
		
		$cache->startSaveCache();
			include_once $_SYSTEM_DIRECTORY.'init/imports.php';
			include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
			include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
			include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';
		$cache->stopSaveCacheAndEcho();
		
		if ( $_DEBUG ) echo '<!-- execute PHP and write cache time: ',number_format( microtime(true) - $timestart , 3),'s -->';
		
		exit();
	}
}

include_once $_SYSTEM_DIRECTORY.'init/imports.php';
include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';

if ( $_DEBUG ) echo '<!-- execute PHP time: ',number_format( microtime(true) - $timestart , 3),'s -->';
