<?php

class InitUtil
{
    private static $instances = array();
    
    final protected function __construct()
    {
        $this->reset();
    }
	
    protected function reset()
	{
		
	}
	
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
	public function getRootAbsUrl( $file )
    {
        return _ROOT_URL.$file;
    }
	
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public function getTemplateAbsUrl( $file )
    {
       return _ROOT_URL._TEMPLATE_DIRECTORY.$file;
    }
    
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public function getContentAbsUrl( $file )
    {
       return _ROOT_URL._CONTENT_DIRECTORY.$file;
    }
	
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public function urlPageToAbsUrl( $url )
    {
	    return _ROOT_URL.( (!_URL_REWRITING) ? (UrlUtil::$BASE_PAGE_URL) : '' ).$url;
    }
    
	/**
	 * 
	 * @param string $idPage
	 * @param string $lang
	 * @return string
	 */
    public function getAbsUrlByIdLang( $idPage, $lang )
    {
		$pagesClass = PageList::getInstance();
		$page = $pagesClass->getPage( $idPage, $lang );
		
         return _ROOT_URL.( (!_URL_REWRITING) ? (UrlUtil::$BASE_PAGE_URL) : '' ).$page->getUrl();
    }
    
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public function urlToLang( $url )
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
	public function mustache( $text, &$page )
    {
		$replacePage = preg_replace('/\{\{pathCurrentPage:(.*?)\}\}/', $page->getAbsoluteUrl('$1'), $text);
		$replacePage = preg_replace('/\{\{urlPageToAbsoluteUrl:(.*?)\}\}/', $this->urlPageToAbsUrl('$1'), $replacePage);
        $replacePage = preg_replace('/\{\{pathTemplate:(.*?)\}\}/', $this->getTemplateAbsUrl('$1'), $replacePage);
		$replacePage = preg_replace('/\{\{pathContent:(.*?)\}\}/', $this->getContentAbsUrl('$1'), $replacePage);

		$pageList = PageList::getInstance();
		if ( $pageList->getInitialised() )
		{
			$replacePage = preg_replace_callback( '/\{\{idPageToAbsoluteUrl:(.*?)\}\}/', function ($matches) use($page)
			{
				$lang = $page->getLanguage();//$this->_language;//BuildUtil::getInstance()->getLang();
				return InitUtil::getInstance()->getAbsUrlByIdLang( $matches[1], $lang );
			}, $replacePage );
		}

        return $replacePage;
    }
	
	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
	
	/**
	 * @return InitUtil
	 */
    public static function getInstance()
    {
        $c = get_called_class();
 
        if(!isset(self::$instances[$c]))
        {
            self::$instances[$c] = new $c;
        }
 
        return self::$instances[$c];
    }
	
}