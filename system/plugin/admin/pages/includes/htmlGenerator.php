<?php

function generateHtml( &$pagesListDebug, $dirParent = '' )
{
	if ( $dirParent == '' )
	{
		$dirParent = _CACHE_DIRECTORY.'standalone-html';
	}
	
	foreach( $pagesListDebug as $pageDebugPage )
	{

		include_once _SYSTEM_DIRECTORY.'init/Cache.php';

		//_URL_REWRITING = TRUE;
		$GLOBALS["_URL_REWRITING"] = TRUE;
		
		foreach ( $_GET as $key => $value )
		{
			unset($_GET[$key]);
		}
		$_GET[UrlUtil::getPageGetArg()] = $pageDebugPage->getUrl();
		$urlPath = explode( '/', $pageDebugPage->getUrl() );
		$newRootRelativeUrl = '../';
		while ( count( $urlPath ) > 1 ) 
		{
			$newRootRelativeUrl .= '../';
			array_pop( $urlPath );
		}

		$cache = new Cache($dirParent);
		if( $pageDebugPage->getType() != Page::$TYPE_ERROR_404 )
		{
			UrlUtil::getInstance()->reset();
			$templateUtils = BuildUtil::getInstance();
			$templateUtils->reset();
			$page = BuildUtil::getInstance()->getCurrentPage();
			PageList::getInstance()->updatePage( $page );
			
			$cache->startSaveCache();
				getHtmlPage( $page );
			$cache->stopSaveCache();
			$cacheContent = $cache->getSavedCache();
			$cacheContent = str_replace( _ROOT_URL.UrlUtil::$BASE_PAGE_URL, $newRootRelativeUrl, $cacheContent );
			$cacheContent = str_replace( _ROOT_URL, $newRootRelativeUrl, $cacheContent );
			$cache->writesCache( $cacheContent, '' );
		}
	}

	
	foreach( $pagesListDebug as $pageDebugPage )
	{
		
		foreach( $page->getRequests() as $request )
		{
			include_once _SYSTEM_DIRECTORY.'init/Cache.php';

			$GLOBALS["_URL_REWRITING"] = TRUE;
		
			foreach ( $_GET as $key => $value )
			{
				unset($_GET[$key]);
			}
			$_GET[UrlUtil::getPageGetArg()] = $request->getUrl();
			$urlPath = explode( '/', $request->getUrl() );
			$newRootRelativeUrl = '../';
			while ( count( $urlPath ) > 1 ) 
			{
				$newRootRelativeUrl .= '../';
				array_pop( $urlPath );
			}

			$cache = new Cache($dirParent);
			if( $pageDebugPage->getType() != Page::$TYPE_ERROR_404 )
			{
				UrlUtil::getInstance()->reset();
				$templateUtils = BuildUtil::getInstance();
				$templateUtils->reset();
				$page = BuildUtil::getInstance()->getCurrentPage();
				PageList::getInstance()->updatePage( $page );

				$cache->startSaveCache();
					getHtmlPage( $page );
				$cache->stopSaveCache();
				$cacheContent = $cache->getSavedCache();
				$cacheContent = str_replace( _ROOT_URL.UrlUtil::$BASE_PAGE_URL, $newRootRelativeUrl, $cacheContent );
				$cacheContent = str_replace( _ROOT_URL, $newRootRelativeUrl, $cacheContent );
				$cache->writesCache( $cacheContent, '' );
			}
		}
	}
	
	
	if ( !file_exists($dirParent.'/index.html') )
	{
		$newRootRelativeUrl = '';
		$indexPage = '<!doctype html><html><head><meta http-equiv="Refresh" content="0;url=';
		$indexPage .= InitUtil::getInstance()->getAbsUrlByIdLang( PageList::getInstance()->getDefaultPage()->getId(), LanguageList::getInstance()->getDefaultLanguage() );
		$indexPage .= '"></head><body></body></html>';
		$indexPage = str_replace( _ROOT_URL.UrlUtil::$BASE_PAGE_URL, $newRootRelativeUrl, $indexPage );
		$indexPage = str_replace( _ROOT_URL, $newRootRelativeUrl, $indexPage );
		$cache->writesCacheFile( $indexPage, $dirParent.'/index.html' );
	}
	
	
	copyDir( _CONTENT_DIRECTORY, $dirParent.'/'._CONTENT_DIRECTORY );
	copyDir( _TEMPLATE_DIRECTORY, $dirParent.'/'._TEMPLATE_DIRECTORY );
}

function getHtmlPage( &$page )
{
	$output = '';
	if ( $page->getCall() == Page::$CALL_PAGE )
	{
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