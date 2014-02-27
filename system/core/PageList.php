<?php

class PageList
{
    
    private static $_instances = array();
        
    protected $_pagesByUrl;
	public function getPagesByUrl()
	{
		return $this->_pagesByUrl;
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
		$this->pages = array();
		$this->_initialised = FALSE;
		
		$this->_pagesByUrl = array();
	}


	public function addDefaultPage( $id )
    {
        $this->defaultPageId = $id;
        $this->addPage($id);
    }

    public function addError404Page( $id )
    {
        $this->error404PageId = $id;
        $pages = $this->addPage($id);
        
        foreach( $pages as $page )
        {
            //$page = $this->$page;
        }
    }
	
	private function makeError404Page( &$page )
	{
		$page->setVisible( FALSE );
		$page->setType( Page::$TYPE_ERROR_404 );
		$page->setCachable( FALSE );
		$page->setPhpHeader( 'HTTP/1.0 404 Not Found' );
		return $page;
	}
    

    public function addPage( $folderName )
    {
        $pages = array();
        
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
                
				$file2 = $_ROOT_DIRECTORY.$_CONTENT_DIRECTORY.$folderName.'/'.$lang.'-build.php';
				if( file_exists ( $file2 ) ) { $page->setFile2($file2); }
				
                $pageUrl = $page->getUrl();
                $this->_pagesByUrl[$pageUrl] = $page;
                array_push( $pages, $page );
            }
            
        }
        
		return $pages;
    }
	
	public function go()
	{
		$this->_initialised = TRUE;
	}


	public function updatePage( &$page )
	{
		if( $page->getFile2() == '' ) return $page;
		
		$file2 = $page->getFile2();
		$page->setFile2('');
		include $file2;
		
		if ( isset($url) )			$page->setUrl ($url);
		if ( isset($template) )		$page->setTemplate ($template);
		if ( isset($visible) )		$page->setVisible($visible);
		if ( isset($title) )		$page->setTitle($title);
		if ( isset($categories) )	$page->addCategories($categories);
		if ( isset($cachable) )		$page->setCachable($cachable);
		if ( isset($phpHeader) )	$page->setPhpHeader($phpHeader);

		if ( isset($body) )			$page->setBody ( PageUtils::mustache($body, $page) );
		if ( isset($header) )		$page->setHeader ( PageUtils::mustache($header, $page) );
		if ( isset($preface) )		$page->setPreface ( PageUtils::mustache($preface, $page) );
		if ( isset($contents) )
		{
			foreach( $contents as $label => $value )
			{
				$page->addContent( $label, PageUtils::mustache($value, $page) );
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
        if ( isset($categories) )	$page->addCategories($categories);
        if ( isset($cachable) )		$page->setCachable($cachable);
        if ( isset($phpHeader) )	$page->setPhpHeader($phpHeader);

        if ( isset($body) )			$page->setBody ( PageUtils::mustache($body, $page) );
        if ( isset($header) )		$page->setHeader ( PageUtils::mustache($header, $page) );
		if ( isset($preface) )		$page->setPreface ( PageUtils::mustache($preface, $page) );
        if ( isset($contents) )
		{
			foreach( $contents as $label => $value )
			{
				$page->addContent( $label, PageUtils::mustache($value, $page) );
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
    
	public function getPagesByCategory( $category, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPagesByCategory() method', E_USER_ERROR );
		
		$pages = array();
        
		foreach ( $this->_pagesByUrl as $page )
        {
			if( $page->getLanguage() == $lang && $page->hasCategory($category) )
			{
				array_push( $pages, $page );
			}
        }
		return $pages;
    }
	
    public function getPagesByCategories( $categories, $lang )
    {
		if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPagesByCategories() method', E_USER_ERROR );
		
		$pages = array();
        foreach ( $this->_pagesByUrl as $page )
        {
            foreach ( $categories as $category )
            {
                if( $page->getLanguage() == $lang && $page->hasCategory($category) )
                {
                    array_push( $pages, $page );
                    break 1;
                }
            }
        }
        return $pages;
    }
    
    public function getPageByUrl( $url )
    {
        if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getPageByUrl() method', E_USER_ERROR );
		
		// EXIST
		if( $this->hasUrl( $url ) ) return $this->_pagesByUrl[$url];
		
		// EXIST WITHOUT "/" AT THE END
		foreach ( $this->_pagesByUrl as $page )
        {
            $urlTemp = $page->getUrl();
            if ( $url == $urlTemp || $url == $urlTemp.'/' ) { return $page; }
        }
        
        // IS DEFAULT PAGE
		$lang = $this->getLanguageByUrl( $url );
        //$pathUrl = explode ('/', $url);
        if( $url == '' || $url == '/' )
        {
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
				if ( $idTemp == $this->error404PageId && $langTemp == $lang )
				{
					header('HTTP/1.0 404 Not Found');
					return $page;
				}
			}
		}
        
        
        if( !isset($this->_page) )
        {
			//$_CACHE = FALSE;
        	//trigger_error( 'Page "error404" not declared', E_USER_ERROR );
			
			$page = new Page(0);
			$page->setHeader( '<title>Error 404 - Not found</title>
					<meta name="robots" content="noindex,nofollow" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' );
			$page->setBody( '<h1>Error 404 - Not found</h1>' );
			$this->makeError404Page($page);
			return $page;
			
        	/*header('HTTP/1.0 404 Not Found');
			echo '<html>
				<head>
					<title>Error 404 - Not found</title>
					<meta name="robots" content="noindex,nofollow" />
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				</head>
				<body>
					<h1>Error 404 - Not found</h1>
				</body>
			</html>';
        	exit();*/
        }
    }
	
	/**
	 * 
	 * @param string $lang
	 * @return Page
	 */
    public function getDefaultPage( $lang = '' )
    {
        if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getDefaultPage() method', E_USER_ERROR );
		
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
	
	public function hasUrl( $url )
    {
		return !empty( $this->_pagesByUrl[$url] );
    }
	
    private function getLanguageByUrl( $url )
    {
        if ( !$this->_initialised ) trigger_error( 'All pages must be initialised after use getLanguageByUrl() method', E_USER_ERROR );
		
		if ( isset( $this->_pagesByUrl[$url] ) )
        {
            $page = $this->pagesByUrl[$url];
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
