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
		
		include_once $_SYSTEM_DIRECTORY.'init/imports.php';
		include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
		include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
		
		$cache->startSaveCache();
			echoPage( $page ); //include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';
		$cache->stopSaveCacheAndEcho();
		
		if ( $_DEBUG ) echo '<!-- execute PHP and write cache time: ', number_format( microtime(true) - $timestart , 3), 's -->';
		
		exit();
	}
}

include_once $_SYSTEM_DIRECTORY.'init/imports.php';
include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
echoPage( $page );

if ( $_DEBUG ) echo '<!-- execute PHP time: ',number_format( microtime(true) - $timestart , 3),'s -->';


function echoPage( &$page )
{
	global $_TEMPLATE_DIRECTORY;
	
	if ( !empty( $page->getPhpHeader() ) )
	{
		header( $page->getPhpHeader() );
	}
	
	if ( !empty( $page->getTemplate() ) && $page->getTemplate() != '' )
	{
		include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';
	}
	else
	{
		echo '<!doctype html>';
		echo '<html><head>' , $page->getHeader();
		echo '</head><body>' , $page->getBody();
		echo '</body></html>';
	}
}