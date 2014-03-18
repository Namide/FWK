<?php

if ( $_DEBUG ) { error_reporting(E_ALL); }

if ( $_CACHE )
{
	include_once $_SYSTEM_DIRECTORY.'init/Cache.php';
	$cache = new Cache( $_CACHE_DIRECTORY.'pages/' );
	
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
			echoPage( $page );
		$cache->stopSaveCache();
		$cache->writesCache();
		echo $cache->getSavedCache();
	
		
		if ( $_DEBUG && $page->getCall() == Page::$CALL_PAGE )
		{
			echo '<!-- execute PHP and write cache time: ', number_format( microtime(true) - $timestart , 3), 's -->';
		}
		
		exit();
	}
}

include_once $_SYSTEM_DIRECTORY.'init/imports.php';
include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
echoPage( $page );

if ( $_DEBUG && $page->getCall() == Page::$CALL_PAGE )
{
	echo '<!-- execute PHP time: ', number_format( microtime(true) - $timestart , 3),'s -->';
}

/**
 * 
 * @global string $_TEMPLATE_DIRECTORY
 * @param Page $page
 */
function echoPage( &$page )
{
	
	if ( $page->getCall() == Page::$CALL_PAGE )
	{
		global $_TEMPLATE_DIRECTORY;
	
		if ( $page->getPhpHeader() != '' )
		{
			header( $page->getPhpHeader() );
		}

		if ( $page->getTemplate() != '' )
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
	elseif ( $page->getCall() == Page::$CALL_REQUEST )
	{
		
		$url = Url::getInstance()->getUrl();
		$request = $page->getRequest($url);
		
		if ( $request->getPhpHeader() != '' )
		{
			header( $request->getPhpHeader() );
		}
		
		$content = $request->getContent();//$page->getRequest( $url );
		echo $content;
	}
	
}