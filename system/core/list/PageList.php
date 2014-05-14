<?php

/**
 * Stores all the pages of the Website.
 */
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
	public function getDefaultPageId() { return $this->_defaultPageId; }
	protected $_error404PageId;
	public function getError404PageId() { return $this->_error404PageId; }
	
	protected $_initialised;
	public function getInitialised() { return $this->_initialised; }


	final private function __construct()
    {
        $this->reset();
	}
	
	public function reset()
	{
		$this->_initialised = false;
		$this->_pagesByUrl = array();
		$this->_requestsByUrl = array();
	}

	/**
	 * 
	 * @param string $id
	 */
	public function addDefaultPage( $id )
    {
		$this->_defaultPageId = $id;
        $this->addPage($id);
    }

	/**
	 * 
	 * @param string $id
	 */
    public function addError404Page( $id )
    {
        $this->_error404PageId = $id;
		foreach ( $this->addPage($id) as $page)
		{
			$this->makeError404Page( $page );
		}
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
        $pages = array();
        
        $language = LanguageList::getInstance();
        $langs = $language->getList();
        
        foreach ( $langs as $lang )
        {
            $filename = _CONTENT_DIRECTORY.$folderName.'/'.$lang.'-init.php';
            
            if( file_exists ( $filename ) )
            {
				
				$page = new Page( $folderName );
				$page->setLanguage( $lang );
				//Gateway::getInstance()->clearPageVo();
                $page = $this->initPage( $page, $filename );
				
				$buildFile = _CONTENT_DIRECTORY.$folderName.'/'.$lang.'-build.php';
				if( file_exists ( $buildFile ) ) { $page->setBuildFile($buildFile); }
				
				
				// ADD THE PAGE'S URL
				
					$pageUrl = $page->getUrl();
					if ( $this->hasUrl( $pageUrl ) || $this->hasUrlRequest( $pageUrl ) )
					{
						trigger_error( 'The URL '.$pageUrl.' of the page [id:'.$page->getId().', lang:'.$page->getLanguage().', url:'.$page->getUrl().'] already exist', E_USER_ERROR );
					}
					$this->_pagesByUrl[$pageUrl] = $page;
				
					
				// ADD THE REQUESTS'S URL
					
					$requests = $page->getRequests();
					foreach ( $requests as $requestUrl => $requestContent )
					{
						if ( $this->hasUrl( $requestUrl ) || $this->hasUrlRequest( $requestUrl ) )
						{
							trigger_error( 'The URL '.$pageUrl.' of the request [id:'.$page->getId().', lang:'.$page->getLanguage().', url:'.$page->getUrl().'] already exist', E_USER_ERROR );
						}
						$this->_requestsByUrl[$requestUrl] = $page;
					}
				
				// ------
				
				
				array_push( $pages, $page );
            }
        }
        
		return $pages;
    }
	
	/**
	 * 
	 * @param string $folderName
	 * @param string $url
	 * @param string $lang
	 * @param * $vo
	 */
	public function addDynamicPage( $folderName, $url, $lang, $vo, $name = '' )
    {
		$filename = _CONTENT_DIRECTORY.$folderName.'/'.$lang.'-init.php';
		if ( file_exists ( $filename ) )
		{
			$page = new DynamicPage( $folderName, $url, $vo );
			$page->setLanguage( $lang );
			$page->setName( $name );

			$page = $this->initPage( $page, $filename );

			$buildFile = _CONTENT_DIRECTORY.$folderName.'/'.$lang.'-build.php';
			if( file_exists ( $buildFile ) )
			{
				$page->setBuildFile( $buildFile );
			}

			// ADD THE PAGE'S URL
				$pageUrl = $page->getUrl();
				if ( $this->hasUrl( $pageUrl ) || $this->hasUrlRequest( $pageUrl ) )
				{
					trigger_error( 'The URL '.$pageUrl.' of the page [id:'.$page->getId().', lang:'.$page->getLanguage().', url:'.$page->getUrl().'] already exist', E_USER_ERROR );
				}
				$this->_pagesByUrl[$pageUrl] = $page;
			// ------
		}
        
    }
	
	/**
	 * 
	 * @param string $folderName
	 * @param string $lang
	 * @param string $name
	 * @return \DynamicPage
	 */
	public function getDynamicPage( $folderName, $lang, $name )
    {
		foreach ( $this->_pagesByUrl as $page )
		{
			if (	$page instanceof DynamicPage &&
					$page->getId() === $folderName &&
					$page->getLanguage() === $lang &&
					$page->getName() === $name )
			{
				return $page;
			}
		}
		
		trigger_error( 'The dynamic page [dir:'.$folderName.' lang:'.$lang.' name:'.$name.'] dont\'t exist', E_USER_ERROR );
    }
	
	/**
	 * 
	 * @param string $folderName
	 * @param array $listUrl
	 * @param array $listLang
	 * @param array $listVo
	 */
	public function addDynamicPages( $folderName, $listUrl, $listLang, $listVo )
    {
		foreach ( $listLang as $id => $lang )
		{
			$this->addDynamicPage( $folderName, $listUrl[$id], $lang, $listVo[$id] );
		}
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
		if( $page->getBuildFile() == '' )
		{
			return $page;
		}
		
		ob_start();
		$page = $this->initPage( $page, $page->getBuildFile() );
		$page->startBuild();
		//$body = ob_get_clean();
		//$page->setBody( InitUtil::getInstance()->mustache($body, $page) );
		$page->setBody( ob_get_clean() );
		
		return $page;
	}

	
	private function initPage( &$page, $filename )
    {
		if ( $page instanceof DynamicPage )
		{
			$vo = $page->getVo();
		}
		
		include $filename;
		
        if ( isset($url) )			{ $page->setUrl ($url); }
        if ( isset($template) )		{ $page->setTemplate ($template); }
        if ( isset($visible) )		{ $page->setVisible($visible); }
		if ( isset($title) )		{ $page->setTitle($title); }
        if ( isset($description) )	{ $page->setDescription($description); }
		if ( isset($tags) )			{ $page->addTags($tags); }
        if ( isset($cachable) )		{ $page->setCachable($cachable); }
		if ( isset($phpHeader) )	{ $page->setPhpHeader($phpHeader); }

        if ( isset($body) )
		{
			//$page->setBody ( InitUtil::getInstance()->mustache($body, $page) );
			$page->setBody ( $body );
		}
		if ( isset($header) )
		{
			//$page->setHeader ( InitUtil::getInstance()->mustache($header, $page) );
			$page->setHeader ( $header );
		}
        if ( isset($contents) )
		{
			foreach( $contents as $label => $value )
			{
				//$page->addContent( $label, InitUtil::getInstance()->mustache($value, $page) );
				$page->addContent( $label, $value );
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
				//$page->getRequest( $url )->setContent( InitUtil::getInstance()->mustache( $content, $page ) );
				$page->getRequest( $url )->setContent( $content );
			}
		}
		
        return $page;
    }
	
	/**
	 * 
	 * @param string $tag
	 * @param string $lang
	 * @return array
	 */
	public function getPagesByTag( $tag, $lang )
    {
		if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getPagesByTag() method', E_USER_ERROR );
		}
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
			if( $page->getLanguage() === $lang && $page->hasTag($tag) )
			{
				array_push( $pages, $page );
			}
        }
		return $pages;
    }
	
	/**
	 * 
	 * @param array $tags
	 * @param string $lang
	 * @return array
	 */
    public function getPagesByTags( $tags, $lang )
    {
		if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getPagesByTags() method', E_USER_ERROR );
		}
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
            foreach ( $tags as $tag )
            {
                if(		$page->getLanguage() === $lang &&
						$page->hasTag($tag) )
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
            if ( $url === $urlTemp || $url === $urlTemp.'/' )
			{
				$page->setCall( Page::$CALL_PAGE );
				return $page;
			}
        }
		foreach ( $this->_requestsByUrl as $page )
        {
            $urlTemp = $page->getUrl();
            if ( $url === $urlTemp || $url === $urlTemp.'/' )
			{
				$page->setCall( Page::$CALL_REQUEST );
				return $page;
			}
        }
        
        // IS DEFAULT PAGE
		if( $url === '' || $url === '/' )
        {
			$this->getDefaultPage()->setCall( Page::$CALL_PAGE );
			return $this->getDefaultPage();
        }
        
        // IS ERROR 404
		$lang = $this->getLanguageByUrl( $url );
		if ( !empty( $this->_error404PageId ) )
		{
			foreach ( $this->_pagesByUrl as $page )
			{
				$idTemp = $page->getId();
				$langTemp = $page->getLanguage();
				if ( $idTemp === $this->_error404PageId && $langTemp === $lang )
				{
					header('HTTP/1.0 404 Not Found');
					$page->setCall( Page::$CALL_PAGE );
					return $page;
				}
			}
		}
        
        // CREATE PAGE ERROR 404
			$page = new Page(0);
			$page->setHeader( '<title>Error 404 - Not found</title>
					<meta name="robots" content="noindex,nofollow" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' );
			$page->setBody( '<h1>Error 404 - Not found</h1>' );
			$this->makeError404Page($page);
			$page->setCall( Page::$CALL_PAGE );
			return $page;
        //
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
		
		$id = $this->_defaultPageId;
        
        if ( $lang == '' )
		{
			$lang = LanguageList::getInstance()->getLangByNavigator();
		}
		
        foreach ( $this->_pagesByUrl as $page )
        {
			if (	$page->getId() == $id &&
					$page->getLanguage() == $lang )
            {
                return $page;
            }
        }
		
		/* IF THE LANGUAGE OF THE DEFAULT PAGE DON'T EXIST */
		foreach ( $this->_pagesByUrl as $page )
        {
            if ( $page->getId() == $id )
            {
                return $page;
            }
        }
		
		/* IF THE DEFAULT PAGE DON'T EXIST */
		foreach ( $this->_pagesByUrl as $page )
        {
            if ( $page->getLanguage() == $lang )
            {
                return $page;
            }
        }
		
		/* ELSE */
		foreach ( $this->_pagesByUrl as $page )
        {
            return $page;
        }
    }
    
	/**
	 * 
	 * @param string $lang
	 * @return array
	 */
    public function getAllPages( $lang )
    {
        if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getAllPages() method', E_USER_ERROR );
		}
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
			if (	$page->getVisible() &&
					($page->getLanguage() == $lang || $lang == 'all') )
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
		if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getPage() method', E_USER_ERROR );
		}
		
		foreach ( $this->_pagesByUrl as $page )
        {
            if (	$page->getId() === $id &&
					$page->getLanguage() === $lang )
            {
                return $page;
            }
        }
        return $this->getDefaultPage();
    }
    
	public function getPages( $id )
    {
		if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getPages() method', E_USER_ERROR );
		}
		
		$pages = array();
		foreach ( $this->_pagesByUrl as $page )
        {
            if ( $page->getId() === $id )
            {
                array_push($pages, $page);
            }
        }
        return $pages;
    }
	
	/**
	 * 
	 * @param string $id
	 * @param string $lang
	 * @return boolean
	 */
	public function hasPage( $id, $lang )
    {
		if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use hasPage() method', E_USER_ERROR );
		}
		
		foreach ( $this->_pagesByUrl as $page )
        {
            if (	$page->getId() === $id &&
					$page->getLanguage() === $lang )
            {
                return true;
            }
        }
        return false;
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
        if ( !$this->_initialised )
		{
			trigger_error( 'All pages must be initialised after use getLanguageByUrl() method', E_USER_ERROR );
		}
		
		if ( isset( $this->_pagesByUrl[$url] ) )
        {
            $page = $this->_pagesByUrl[$url];
            return $page->getLanguage();
        }
        
		return LanguageList::getInstance()->getLangByNavigator();
    }

	/**
	 * 
	 * @return string
	 */
	public function getSave()
	{
		$obj = get_object_vars($this);
		$output = 'PageList::update(';
		$output .= SaveUtil::arrayToStrConstructor($obj);
		$output .= ')';
		
		return $output;
	}
	
	/**
	 * 
	 * @param array $save
	 */
	public static function update( $save )
	{
		$pageList = PageList::getInstance();
		foreach ($save as $key => $value)
		{
			$pageList->$key = $value;
		}
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
