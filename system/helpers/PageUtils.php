<?php

class PageUtils
{
    
    final private function __construct()
    {
        
    }
    
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
	public static function getRootAbsoluteUrl( $file )
    {
        return _ROOT_URL.$file;
    }
	
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public static function getTemplateAbsoluteUrl( $file )
    {
       return _ROOT_URL._TEMPLATE_DIRECTORY.$file;
    }
    
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public static function getContentAbsoluteUrl( $file )
    {
       return _ROOT_URL._CONTENT_DIRECTORY.$file;
    }
	
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public static function urlPageToAbsoluteUrl( $url )
    {
	    return _ROOT_URL.( (!_URL_REWRITING) ? (Url::$BASE_PAGE_URL) : '' ).$url;
    }
    
	/**
	 * 
	 * @param string $idPage
	 * @param string $lang
	 * @return string
	 */
    public static function getAbsoluteUrl( $idPage, $lang = NULL )
    {
		$pagesClass = PageList::getInstance();
		if ( $lang === NULL ) $lang = TemplateUtils::getInstance()->getLanguage();
        $page = $pagesClass->getPage( $idPage, $lang );
		
         return _ROOT_URL.( (!_URL_REWRITING) ? (Url::$BASE_PAGE_URL) : '' ).$page->getUrl();
    }
    
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public static function urlToLanguage( $url )
    {
        $pagesClass = PageList::getInstance();
        $pageClass = $pagesClass->getPageByUrl( $url );
        return $pageClass->getLanguage();
    }
	
	/**
	 * 
	 * @param string $text
	 * @param Page $page
	 * @return Page
	 */
	public static function mustache( $text, &$page )
    {
		$replacePage = preg_replace('/\{\{pathCurrentPage:(.*?)\}\}/', $page->getAbsoluteUrl('$1'), $text);
        $replacePage = preg_replace('/\{\{urlPageToAbsoluteUrl:(.*?)\}\}/', PageUtils::urlPageToAbsoluteUrl('$1'), $replacePage);
        $replacePage = preg_replace('/\{\{pathTemplate:(.*?)\}\}/', PageUtils::getTemplateAbsoluteUrl('$1'), $replacePage);
		$replacePage = preg_replace('/\{\{pathContent:(.*?)\}\}/', PageUtils::getContentAbsoluteUrl('$1'), $replacePage);

		$pageList = PageList::getInstance();
		if ( $pageList->getInitialised() )
		{
			$replacePage = preg_replace_callback( '/\{\{idPageToAbsoluteUrl:(.*?)\}\}/', function ($matches)
			{
				$lang = TemplateUtils::getInstance()->getLanguage();
				return PageUtils::getAbsoluteUrl( $matches[1], $lang );
			}, $replacePage );
		}

        return $replacePage;
    }
	
	
	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
	
}