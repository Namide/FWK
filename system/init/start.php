<?php

$timestart = microtime(true);
$initTime = 0;

if ( _DEBUG )
{
	if (!ini_get('display_errors'))
	{
		ini_set('display_errors', '1');
	}
	error_reporting(E_ALL);
}

if ( _CACHE )
{
	include_once _SYSTEM_DIRECTORY.'helpers/Cache.php';
	$cache = new Cache( _CACHE_DIRECTORY.'pages/' );
	
	if( $cache->isCached() )
	{
		$cache->echoCache();
		
		if ( _DEBUG ) echo '<!-- load cache time: ', number_format( microtime(true) - $timestart , 3) , 's -->';
		
		exit();
	}
	elseif( $cache->isCachable() )
	{
		
		include_once _SYSTEM_DIRECTORY.'init/imports.php';
		include_once _SYSTEM_DIRECTORY.'init/loadPages.php';
		include_once _SYSTEM_DIRECTORY.'init/buildPage.php';
		
		
		$cache->isCachable( $page );
		if ( $cache->isCachable( $page ) )
		{
			$cache->startSaveCache();
				echoPage( $page );
			$cache->stopSaveCache();
			$cache->writesCache();
			echo $cache->getSavedCache();


			if ( _DEBUG && $page->getCall() == Page::$CALL_PAGE )
			{
				echo '<!-- execute PHP and write cache time: ', number_format( microtime(true) - $timestart , 3), 's -->';
			}
		}
		else
		{
			echoPage( $page );
		}
		
		
		exit();
	}
}

include_once _SYSTEM_DIRECTORY.'init/imports.php';
include_once _SYSTEM_DIRECTORY.'init/loadPages.php';
include_once _SYSTEM_DIRECTORY.'init/buildPage.php';
echoPage( $page );

if ( _DEBUG && $page->getCall() == Page::$CALL_PAGE )
{
	echo '<!-- execute PHP time: ', number_format( microtime(true) - $timestart , 3),'s -->';
}

/**
 * 
 * @param Page $page
 */
function echoPage( &$page )
{
	
	if ( $page->getCall() == Page::$CALL_PAGE )
	{
		if ( $page->getPhpHeader() != '' )
		{
			header( $page->getPhpHeader() );
		}

		if ( $page->getTemplate() != '' )
		{
			include _TEMPLATE_DIRECTORY.$page->getTemplate().'.php';
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
		
		$url = UrlUtil::getInstance()->getUrl();
		$request = $page->getRequest($url);
		
		if ( $request->getPhpHeader() != '' )
		{
			header( $request->getPhpHeader() );
		}
		
		$content = $request->getContent();
		echo $content;
	}
	
}



