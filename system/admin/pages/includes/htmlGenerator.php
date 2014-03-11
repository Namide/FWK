<?php

function generateHtml( &$pagesListDebug, $dirParent = '' )
{
	global $_CACHE_DIRECTORY;
	global $_SYSTEM_DIRECTORY;
	global $_ROOT_URL;
	global $_CONTENT_DIRECTORY;
	global $_TEMPLATE_DIRECTORY;
	
	if ( $dirParent == '' )
	{
		$dirParent = $_CACHE_DIRECTORY.'standalone-html';
	}
	
	foreach( $pagesListDebug as $pageDebugPage )
	{

		include_once $_SYSTEM_DIRECTORY.'init/Cache.php';

		$_URL_REWRITING = TRUE;

		foreach ( $_GET as $key => $value )
		{
			unset($_GET[$key]);
		}
		$_GET[Url::getPageGetArg()] = $pageDebugPage->getUrl();
		$urlPath = explode( '/', $pageDebugPage->getUrl() );
		$newRootRelativeUrl = '../';
		//$_ROOT_URL = '';
		while ( count( $urlPath ) > 1 ) 
		{
			$newRootRelativeUrl .= '../';
			array_pop( $urlPath );
		}



		$cache = new Cache($dirParent);
		if( /*$cache->isCachable() &&*/ $pageDebugPage->getType() != Page::$TYPE_ERROR_404 )
		{
			Url::getInstance()->reset();
			$templateUtils = TemplateUtils::getInstance();
			$templateUtils->reset();
			$page = TemplateUtils::getInstance()->getCurrentPage();
			PageList::getInstance()->updatePage( $page );
			
			$cache->startSaveCache();
				global $_TEMPLATE_DIRECTORY;
				if ( $pageDebugPage->getTemplate() != '' )
				{
					include $_TEMPLATE_DIRECTORY.$pageDebugPage->getTemplate().'.php';
				}
				else
				{
					echo '<!doctype html>';
					echo '<html><head>' , $pageDebugPage->getHeader();
					echo '</head><body>' , $pageDebugPage->getBody();
					echo '</body></html>';
				}
			$cache->stopSaveCache();
			$cacheContent = $cache->getSavedCache();
			$cacheContent = str_replace( $_ROOT_URL.Url::$BASE_PAGE_URL, $newRootRelativeUrl, $cacheContent );
			$cacheContent = str_replace( $_ROOT_URL, $newRootRelativeUrl, $cacheContent );
			$cache->writesCache( $cacheContent, '' );
		}
	}

	
	if ( !file_exists($dirParent.'/index.html') )
	{
		$newRootRelativeUrl = '';
		$indexPage = '<!doctype html><html><head><meta http-equiv="Refresh" content="0;url=';
		$indexPage .= PageUtils::getAbsoluteUrl( PageList::getInstance()->getDefaultPage()->getId(), LanguageList::getInstance()->getDefaultLanguage() );
		$indexPage .= '"></head><body></body></html>';
		$indexPage = str_replace( $_ROOT_URL.Url::$BASE_PAGE_URL, $newRootRelativeUrl, $indexPage );
		$indexPage = str_replace( $_ROOT_URL, $newRootRelativeUrl, $indexPage );
		$cache->writesCacheFile( $indexPage, $dirParent.'/index.html' );
	}
	
	
	copyDir( $_CONTENT_DIRECTORY, $dirParent.'/'.$_CONTENT_DIRECTORY );
	copyDir( $_TEMPLATE_DIRECTORY, $dirParent.'/'.$_TEMPLATE_DIRECTORY );
}