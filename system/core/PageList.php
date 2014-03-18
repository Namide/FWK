<?php

class PageList
{
    
    private static $_instances = array();
	
	
    protected $_pagesByUrl;
	/**
	 * 
	 * @return array
	 */
	public function getPagesByUrl()
	{
		return $this->_pagesByUrl;
	}

	
	protected $_requestsByUrl;
	/**
	 * 
	 * @return array
	 */
	public function getRequestsByUrl()
	{
		return $this->_requestsByUrl;
	}
	
	
	protected $_defaultPageId;
    protected $_error404PageId;
	
	protected $_initialised;
	public function getInitialised() { return $this->_initialised; }


	final private function __construct()
    {
        $this->reset();
	}
	
	public function reset()
	{
		//$this->pages = array();
		$this->_initialised = FALSE;
		$this->_pagesByUrl = array();
		$this->_requestsByUrl = array();
	}

	/**
	 * 
	 * @param string $id
	 */
	public function addDefaultPage( $id )
    {
        $this->defaultPageId = $id;
        $this->addPage($id);
    }

	/**
	 * 
	 * @param string $id
	 */
    public function addError404Page( $id )
    {
        $this->error404PageId = $id;
        $this->addPage($id);
    }
	
	/**
	 * 
	 * @param Page $id
	 */
	private function makeError404Page( &$page )
	{
		$page->setVisible( FALSE );
		$page->setType( Page::$TYPE_ERROR_404 );
		$page->setCachable( FALSE );
		$page->setPhpHeader( 'HTTP/1.0 404 Not Found' );
		return $page;
	}
    
	/**
	 * 
	 * @param string $id
	 */
    public function addPage( $folderName )
    {
        //$pages = array();
        
        global $_ROOT_DIRECTORY;
        global $_CONTENT_DIRECTORY;
        $language = LanguageList::getInstance();
        $langs = $language->getList();
        
        foreach ( $langs as $lang )
        {
            $filename = $_ROOT_DIRECTORY.$_CONTENT_DIRECTORY.$folderName.'/'.$lang.'-init.php';
            
            if( file_exists ( $filename ) )
            {
				
                $page = $this->initPage( $folderName, $lang, $filename );
                
				$buildFile = $_ROOT_DIRECTORY.$_CONTENT_DIRECTORY.$folderName.'/'.$lang.'-build.php';
				if( file_exists ( $buildFile ) ) { $page->setBuildFile($buildFile); }
				
				
				// ADD THE PAGE'S URL
				
					$pageUrl = $page->getUrl();
					if ( $this->hasUrl( $pageUrl ) || $this->hasUrlRequest( $pageUrl ) )
					{
						trigger_error( 'The URL '.$pageUrl.' of the page [id:'.$this->id.', lang:'.$this->language.'] already exist', E_USER_ERROR );
					}
					$this->_pagesByUrl[$pageUrl] = $page;
				
					
				// ADD THE REQUESTS'S URL
					
					$requests = $page->getRequests();
					foreach ( $requests as $requestUrl => $requestContent )
					{
						if ( $this->hasUrl( $requestUrl ) || $this->hasUrlRequest( $requestUrl ) )
						{
							trigger_error( 'The URL '.$pageUrl.' of the request [id:'.$this->id.', lang:'.$this->language.'] already exist', E_USER_ERROR );
						}
						$this->_requestsByUrl[$requestUrl] = $page;
					}
				
				// ------
				
				
				//array_push( $pages, $page );
            }
        }
        
		//return $pages;
    }
	
	
	public function go()
	{
		$this->_initialised = TRUE;
	}

	/**
	 * 
	 * @param Page $page
	 * @return Page
	 */
	public function updatePage( &$page )
	{
		if( $page->getBuildFile() == '' ) return $page;
		
		$buildFile = $page->getBuildFile();
		$page->setBuildFile('');
		include $buildFile;
		
		if ( isset($url) )			$page->setUrl ($url);
		if ( isset($template) )		$page->setTemplate ($template);
		if ( isset($visible) )		$page->setVisible($visible);
		if ( isset($title) )		$page->setTitle($title);
		if ( isset($description) )	$page->setDescription($description);
		if ( isset($categories) )	$page->addCategories($categories);
		if ( isset($cachable) )		$page->setCachable($cachable);
		if ( isset($phpHeader) )	$page->setPhpHeader($phpHeader);
		
		if ( isset($body) )			$page->setBody ( PageUtils::mustache($body, $page) );
		if ( isset($header) )		$page->setHeader ( PageUtils::mustache($header, $page) );
		//if ( isset($preface) )		$page->setPreface ( PageUtils::mustache($preface, $page) );
		if ( isset($contents) )
		{
			foreach( $contents as $label => $value )
			{
				$page->addContent( $label, PageUtils::mustache($value, $page) );
			}
		}
		
		if ( isset($requests) )
		{
			foreach( $requests as $url )
			{
				$page->addRequest( $url );
			}
		}
		if ( isset($requestsContent) )
		{
			foreach( $requestsContent as $url => $content )
			{
				$page->getRequest( $url )->setContent( PageUtils::mustache( $content, $page ) );
				//$page->buildRequest( $url, PageUtils::mustache( $content, $page ) );
			}
		}
		
		return $page;
	}

	
	private function initPage( $folderName, $lang, $filename )
    {
		$page = new Page( $folderName );
        $page->setLanguage( $lang );

        include $filename;
        if ( isset($url) )			$page->setUrl ($url);
        if ( isset($template) )		$page->setTemplate ($template);
        if ( isset($visible) )		$page->setVisible($visible);
		if ( isset($title) )		$page->setTitle($title);
        if ( isset($description) )	$page->setDescription($description);
		if ( isset($categories) )	$page->addCategories($categories);
        if ( isset($cachable) )		$page->setCachable($cachable);
        if ( isset($phpHeader) )	$page->setPhpHeader($phpHeader);

        if ( isset($body) )			$page->setBody ( PageUtils::mustache($body, $page) );
        if ( isset($header) )		$page->setHeader ( PageUtils::mustache($header, $page) );
		//if ( isset($preface) )		$page->setPreface ( PageUtils::mustache($preface, $page) );
        if ( isset($contents) )
		{
			foreach( $contents as $label => $value )
			{
				$page->addContent( $label, PageUtils::mustache($value, $page) );
			}
		}
        
		if ( isset($requests) )
		{
			foreach( $requests as $url )
			{
				$page->addRequest( $url );
			}
		}
		if ( isset($requestsContent) )
		{
			foreach( $requestsContent as $url => $content )
			{
				$page->getRequest( $url )->setContent( PageUtils::mustache( $content, $page ) );
				//$page->buildRequest( $url, PageUtils::mustache( $content, $page ) );
			}
		}
		
		$pageUrl = $page->getUrl();
        if (isset($this->_pagesByUrl[$pageUrl]) )
        {
            trigger_error( 'This page already exist: '.$pageUrl.' ('.$folderName.', '.$lang.')', E_USER_ERROR);
        }
        
        return $page;
    }
	
	

    /*private function mustache( $text, $page )
    {
        $replacePage = preg_replace('/\{\{pathCurrentPage:(.*?)\}\}/', $page->getAbsoluteUrl('$1'), $text);
        //$replacePage = preg_replace('/\{\{urlPageToAbsoluteUrl:(.*?)\}\}/', PageUtils::urlPageToAbsoluteUrl('$1'), $replacePage);
        $replacePage = preg_replace('/\{\{urlPageToAbsoluteUrl:(.*?)\}\}/', PageUtils::urlPageToAbsoluteUrl('$1'), $replacePage);
        $replacePage = preg_replace('/\{\{pathTemplate:(.*?)\}\}/', PageUtils::getTemplateAbsoluteUrl('$1'), $replacePage);
        return $replacePage;
    }*/
    
	/**
	 * 
	 * @param string $category
	 * @param string $lang
	 * @return array
	 */
	public function getPagesByCategory( $category, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPagesByCategory() method', E_USER_ERROR );
		
		$pages = array();
        
		foreach ( $this->_pagesByUrl as $page )
        {
			if( $page->getLanguage() === $lang && $page->hasCategory($category) )
			{
				array_push( $pages, $page );
			}
        }
		return $pages;
    }
	
	/**
	 * 
	 * @param array $categories
	 * @param string $lang
	 * @return array
	 */
    public function getPagesByCategories( $categories, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPagesByCategories() method', E_USER_ERROR );
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
            foreach ( $categories as $category )
            {
                if( $page->getLanguage() === $lang && $page->hasCategory($category) )
                {
                    array_push( $pages, $page );
                    break 1;
                }
            }
        }
        return $pages;
    }
    
	/**
	 * 
	 * @param string $url
	 * @return Page
	 */
    public function getPageByUrl( $url )
    {
        if ( !$this->_initialised ) 
		{
			trigger_error( 'All pages must be initialised after use getPageByUrl() method', E_USER_ERROR );
		}
		
		// EXIST
		if( $this->hasUrl( $url ) )
		{
			$this->_pagesByUrl[$url]->setCall( Page::$CALL_PAGE );
			return $this->_pagesByUrl[$url];
		}
		if( $this->hasUrlRequest( $url ) )
		{
			$this->_requestsByUrl[$url]->setCall( Page::$CALL_REQUEST );
			return $this->_requestsByUrl[$url];
		}
		
		// EXIST WITHOUT "/" AT THE END
		foreach ( $this->_pagesByUrl as $page )
        {
            $urlTemp = $page->getUrl();
            if ( $url == $urlTemp || $url == $urlTemp.'/' ) { return $page; }
        }
        
        // IS DEFAULT PAGE
		$lang = $this->getLanguageByUrl( $url );
        //$pathUrl = explode ('/', $url);
        if( $url === '' || $url === '/' )
        {
			$this->getDefaultPage()->setCall( Page::$CALL_PAGE );
			return $this->getDefaultPage();
        }
        
        // IS ERROR 404
		//global $_CACHE;
		
		if ( !empty( $this->error404PageId ) )
		{
			foreach ( $this->_pagesByUrl as $page )
			{
				$idTemp = $page->getId();
				$langTemp = $page->getLanguage();
				if ( $idTemp === $this->error404PageId && $langTemp === $lang )
				{
					header('HTTP/1.0 404 Not Found');
					$page->setCall( Page::$CALL_PAGE );
					return $page;
				}
			}
		}
        
        
        /*if( !isset($this->_page) )
        {*/
			$page = new Page(0);
			$page->setHeader( '<title>Error 404 - Not found</title>
					<meta name="robots" content="noindex,nofollow" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' );
			$page->setBody( '<h1>Error 404 - Not found</h1>' );
			$this->makeError404Page($page);
			$page->setCall( Page::$CALL_PAGE );
			return $page;
        //}
    }
	
	/**
	 * 
	 * @param string $lang
	 * @return Page
	 */
    public function getDefaultPage( $lang = '' )
    {
        if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getDefaultPage() method', E_USER_ERROR );
		}
		
		$id = $this->defaultPageId;
        
        if ( $lang == '' )
		{
			$languages = LanguageList::getInstance();
			$lang = $languages->getLangByNavigator();
		}
		
        foreach ( $this->_pagesByUrl as $page )
        {
            $idTemp = $page->getId();
            $langTemp = $page->getLanguage();
			
            if ( $idTemp == $id && $langTemp == $lang )
            {
                return $page;
            }
        }
    }
    
	/**
	 * 
	 * @param string $lang
	 * @return array
	 */
    public function getAllPages( $lang )
    {
        if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getAllPages() method', E_USER_ERROR );
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
            $langTemp = $page->getLanguage();
            $visible = $page->getVisible();
            if ( $visible && ($langTemp == $lang || $lang == 'all') )
            {
                array_push( $pages, $page );
            }
        }
        return $pages;
    }
	
	/**
	 * 
	 * @param string $id
	 * @param string $lang
	 * @return Page
	 */
    public function getPage( $id, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPage() method', E_USER_ERROR );
		
		foreach ( $this->_pagesByUrl as $page )
        {
            $idTemp = $page->getId();
            $langTemp = $page->getLanguage();
            if ( $idTemp === $id && $langTemp === $lang )
            {
                return $page;
            }
        }
        return $this->getDefaultPage();
    }
    
	/**
	 * 
	 * @param string $id
	 * @param string $lang
	 * @return boolean
	 */
	public function hasPage( $id, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use hasPage() method', E_USER_ERROR );
		
		foreach ( $this->_pagesByUrl as $page )
        {
            $idTemp = $page->getId();
            $langTemp = $page->getLanguage();
            if ( $idTemp === $id && $langTemp === $lang )
            {
                return TRUE;
            }
        }
        return FALSE;
    }
	
	/**
	 * 
	 * @param string $url
	 * @return boolean
	 */
	public function hasUrl( $url )
    {
		return array_key_exists( $url, $this->_pagesByUrl );
    }
	
	/**
	 * 
	 * @param string $url
	 * @return boolean
	 */
	public function hasUrlRequest( $url )
    {
		return array_key_exists( $url, $this->_requestsByUrl );
    }
	
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    private function getLanguageByUrl( $url )
    {
        if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getLanguageByUrl() method', E_USER_ERROR );
		
		if ( isset( $this->_pagesByUrl[$url] ) )
        {
            $page = $this->_pagesByUrl[$url];
            return $page->getLanguage();
        }
        
        $languages = LanguageList::getInstance();
        return $languages->getLangByNavigator();
    }

    final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	 * 
	 * @return PageList
	 */
    final public static function getInstance()
    {
        $c = get_called_class();
        if(!isset(self::$_instances[$c]))
        {
            self::$_instances[$c] = new $c;
        }
        return self::$_instances[$c];
    }
}
