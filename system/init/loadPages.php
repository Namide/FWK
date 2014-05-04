<?php


// CACHE THE INITIALISATION
// inconclusive test
if ( false )
{
	include_once _SYSTEM_DIRECTORY.'helpers/Cache.php';
	$cache = new Cache();
	if ( !$cache->isInitCached() )
	{
		$language = LanguageList::getInstance();
		include_once _CONTENT_DIRECTORY.'languages.php';

		$pageList = PageList::getInstance();
		include_once _CONTENT_DIRECTORY.'pages.php';
		$pageList->go();

		$cache->cacheInit();
	}
}
else
{
	$language = LanguageList::getInstance();
	include_once _CONTENT_DIRECTORY.'languages.php';

	$pageList = PageList::getInstance();
	include_once _CONTENT_DIRECTORY.'pages.php';
	$pageList->go();
}


// HELPERS FOR TEMPLATES
include_once _SYSTEM_DIRECTORY.'helpers/BuildUtil.php';
BuildUtil::getInstance();
