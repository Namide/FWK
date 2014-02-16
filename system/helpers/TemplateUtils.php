<?php

class TemplateUtils
{
    private static $instances = array();
    
    private $_language;
    public function getLanguage() { return $this->_language; }
    
    private $_page;
    public function getCurrentPage() { return $this->_page; }
    
    final private function __construct()
    {
        global $pagesInitialised;
        if ( !isset($pagesInitialised) ) trigger_error( 'All pages must be initialised after use TemplateUtils class', E_USER_ERROR );
        
        $urlClass = Url::getInstance();
        $urlString = $urlClass->getUrl();
        
        $pagesClass = PageList::getInstance();
        
        $page = $pagesClass->getPageByUrl( $urlString );
        $this->_page = $page;
        $this->_language = $page->getLanguage();
    }
    
    public function getAbsoluteUrl( $idPage )
    {
		$lang = $this->getLanguage();
        return PageUtils::getAbsoluteUrl($idPage, $lang);
    }
	
	public function getLink( $idPage, $tagBefore = '', $tagAfter = '' )
	{
		$lang = $this->getLanguage();
		$pageList = PageList::getInstance();
		$page = $pageList->getPage( $idPage, $lang );
		return '<a href="'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl() ).'">'.$tagBefore.$page->getTitle().$tagAfter.'</a>';
	}
    
    final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
    final public static function getInstance()
    {
        $c = get_called_class();
 
        if(!isset(self::$instances[$c]))
        {
            self::$instances[$c] = new $c;
        }
 
        return self::$instances[$c];
    }
}
