<?php

/**
 * All simple methods usable in pages building, or templates.
 */
class BuildUtil extends InitUtil
{
    private static $instances = array();
    
    private $_language;
	/**
	 * 
	 * @return string
	 */
    public function getLang() { return $this->_language; }
    
	private $_page;
	/**
	 * @return Page
	 */
    public function getCurrentPage() { return $this->_page; }
    
    /*final private function __construct()
    {
		  $this->reset();
    }*/
	
	public function reset()
	{
		$pagesClass = PageList::getInstance();
        if ( !$pagesClass->getInitialised() )
		{
			trigger_error( 'All pages must be initialised after use BuildUtil class', E_USER_ERROR );
		}
		
        $urlClass = UrlUtil::getInstance();
        $urlString = $urlClass->getUrl();
        
       
        $page = $pagesClass->getPageByUrl( $urlString );
        $this->_page = $page;
        $this->_language = $page->getLanguage();
	}
    
	/**
	 * 
	 * @param string $idPage
	 * @return string
	 */
    public function getAbsUrl( $idPage )
    {
		$lang = $this->getLang();
        //return PageUtils::getAbsoluteUrl($idPage, $lang);
		return parent::getAbsUrlByIdLang($idPage, $lang);
    }
	
	/**
	 * 
	 * @param string $idPage
	 * @param string $tagBefore
	 * @param string $tagAfter
	 * @return string
	 */
	public function getLink( $idPage, $tagBefore = '', $tagAfter = '' )
	{
		$lang = $this->_language;
		$pageList = PageList::getInstance();
		$page = $pageList->getPage( $idPage, $lang );
		//return '<a href="'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl() ).'">'.$tagBefore.$page->getTitle().$tagAfter.'</a>';
		return '<a href="'.$this->urlPageToAbsUrl( $page->getUrl() ).'">'.$tagBefore.$page->getTitle().$tagAfter.'</a>';
	}
    
    /*final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }*/
 
	/**
	 * @return BuildUtil
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
