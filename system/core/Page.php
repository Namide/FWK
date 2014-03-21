<?php

class Page
{
    
	public static $TYPE_ERROR_404 = 'type-error404';
	
	public static $CALL_PAGE = 'call-page';
	public static $CALL_REQUEST = 'call-request';
	
    private $_id;
	/**
	 * 
	 * @return string
	 */
    public function getId() { return $this->_id; }
    
	private $_phpHeader;
	/**
	 * 
	 * @param string $phpHeader
	 */
    public function setPhpHeader( $phpHeader ) { $this->_phpHeader = $phpHeader; }
	/**
	 * 
	 * @return string
	 */
    public function getPhpHeader() { return $this->_phpHeader; }
	
    private $_visible;
	/**
	 * 
	 * @param boolean $visible
	 */
    public function setVisible( $visible ) { $this->_visible = $visible; }
	/**
	 * 
	 * @return boolean
	 */
    public function getVisible() { return $this->_visible; }

    private $_cachable;
	/**
	 * 
	 * @param boolean $cachable
	 */
	public function setCachable( $cachable ) { $this->_cachable = $cachable; }
	/**
	 * 
	 * @return boolean
	 */
    public function getCachable() { return $this->_cachable; }

    protected $_url;
	/**
	 * 
	 * @param string $url
	 */
    public function setUrl( $url ) { $this->_url = $url; }
	/**
	 * 
	 * @return string
	 */
    public function getUrl() { return $this->_url; }

    private $_language;
	/**
	 * 
	 * @param string $language
	 */
    public function setLanguage( $language ) { $this->_language = $language; }
	/**
	 * 
	 * @return string
	 */
    public function getLanguage() { return $this->_language; }

    private $_header;
	/**
	 * 
	 * @param string $header
	 */
    public function setHeader( $header ) { $this->_header = $header; }
	/**
	 * 
	 * @return string
	 */
    public function getHeader() { return $this->_header; }

	private $_type;
	/**
	 * 
	 * @param string $type
	 */
    public function setType( $type ) { $this->_type = $type; }
	/**
	 * 
	 * @return string
	 */
    public function getType() { return $this->_type; }
	
	private $_call;
	/**
	 * 
	 * @param string $call
	 */
    public function setCall( $call ) { $this->_call = $call; }
	/**
	 * 
	 * @return string
	 */
    public function getCall() { return $this->_call; }
	
    private $_body;
	/**
	 * 
	 * @param string $body
	 */
    public function setBody( $body ) { $this->_body = $body; }
	/**
	 * 
	 * @return string
	 */
    public function getBody() { return $this->_body; }

    private $_title;
	/**
	 * 
	 * @param string $title
	 */
    public function setTitle( $title ) { $this->_title = $title; }
	/**
	 * 
	 * @return string
	 */
    public function getTitle() { return $this->_title; }

	private $_description;
	/**
	 * 
	 * @param string $_description
	 */
    public function setDescription( $description ) { $this->_description = $description; }
	/**
	 * 
	 * @return string
	 */
    public function getDescription() { return $this->_description; }
	
    //private $_preface;
	/**
	 * 
	 * @param string $preface
	 */
    //public function setPreface( $preface ) { $this->_preface = $preface; }
	/**
	 * 
	 * @return string
	 */
    //public function getPreface() { return $this->_preface; }

    private $_template;
	/**
	 * 
	 * @param string $template
	 */
    public function setTemplate( $template ) { $this->_template = $template; }
	/**
	 * 
	 * @return string
	 */
    public function getTemplate() { return $this->_template; }

	private $_buildFile;
	/**
	 * 
	 * @param string $buildFile
	 */
    public function setBuildFile( $buildFile ) { $this->_buildFile = $buildFile; }
	/**
	 * 
	 * @return string
	 */
    public function getBuildFile() { return $this->_buildFile; }
	public function startBuild() { $this->_buildFile = ''; }
	
	
    private $_tags;
	/**
	 * 
	 * @param string $tag
	 */
    public function addTag( $tag )
	{
		array_push($this->_tags, $tag);
	}
	
	/**
	 * 
	 * @param array $tags
	 */
    public function addCategories( $tags )
    {
        foreach ( $tags as $tag )
        {
            $this->addTag( $tag );
        }
    }
	
	/**
	 * 
	 * @param string $tag
	 * @return boolean
	 */
    public function hasTag( $tag )
    {
        return in_array( $tag, $this->_tags );
    }
	
	/**
	 * 
	 * @return array
	 */
	public function getTags()
    {
        return $this->_tags;
    }
	
	
	private $_requests;
	/**
	 * 
	 * @param string $url
	 * @param string $content
	 */
	/*public function initRequest( $url )
	{
		if ( $this->RequestIsInitialised( $url ) )
		{
			trigger_error( 'This request already exist: '.$url.' ('.$this->_id.', '.$this->_language.')', E_USER_ERROR );
		}
		$this->_requests[$url] = new RequestPage( $url );
	}*/
	
	/**
	 * 
	 * @param RequestPage $requestPage
	 * @param string $content
	 */
	public function addRequest( &$requestPage )
	{
		if ( $this->hasRequest( $requestPage->getUrl() ) )
		{
			trigger_error( 'This request already exist: '.$requestPage->getUrl().' ('.$this->_id.', '.$this->_language.')', E_USER_ERROR );
		}
		$this->_requests[$requestPage->getUrl()] = $requestPage;
	}
	
	/**
	 * 
	 * @param string $url
	 * @param string $content
	 */
	/*public function buildRequest( $url, $content )
	{
		if ( !$this->RequestIsInitialised( $url ) )
		{
			trigger_error( 'The request '.$url.' ('.$this->_id.', '.$this->_language.') must be initialized in first', E_USER_ERROR );
		}
		$this->_requests[$url]->setContent( $content );
	}*/
	
	/**
	 * 
	 * @param array $arrayOfUrl
	 */
    /*public function initRequests( $arrayOfUrl )
    {
        foreach ( $arrayOfUrl as $url )
        {
            $this->initRequest( $url );
        }
    }*/
	
	/**
	 * 
	 * @param array $arrayOfContentByUrl
	 */
    /*public function buildRequests( $arrayOfContentByUrl )
    {
		foreach ( $arrayOfContentByUrl as $url => $content )
        {
            $this->buildRequest( $url, $content );
        }
    }*/
	
	/**
	 * 
	 * @param string $url
	 * @return boolean
	 */
    public function hasRequest( $url )
    {
        return array_key_exists( $url, $this->_requests );
    }
	
	/**
	 * 
	 * @param string $url
	 * @return boolean
	 */
    /*public function RequestIsInitialised( $url )
    {
		if ( $this->hasRequest( $url ) )
		{
			return TRUE;
		}
        return FALSE;
    }*/
	
	/**
	 * 
	 * @param string $url
	 * @return RequestPage
	 */
	public function getRequest( $url )
    {
		if ( !$this->hasRequest($url) )
		{
			trigger_error( 'This request don\'t exist: '.$url.' ('.$this->_id.', '.$this->_language.')', E_USER_ERROR );
		}
        return $this->_requests[$url];
    }
	
	/**
	 * 
	 * @return array
	 */
	public function getRequests()
    {
        return $this->_requests;
    }
	
	
	private $_contents;
    /**
	 * 
	 * @param string $label
	 * @param string $value
	 */
	public function addContent( $label, $value )
	{
		if ( $this->hasContent($label) )
		{
			trigger_error( 'This content already exist: '.$label.' ('.$this->id.', '.$this->language.')', E_USER_ERROR);
		}
		$this->_contents[$label] = $value;
	}
	
	/**
	 * 
	 * @param array $arrayOfContentByLabel
	 */
    public function addContents( $arrayOfContentByLabel )
    {
        foreach ( $arrayOfContentByLabel as $label => $content )
        {
            $this->addContent( $label, $content );
        }
    }
	
	/**
	 * 
	 * @param string $label
	 * @return boolean
	 */
    public function hasContent( $label )
    {
		return array_key_exists( $label, $this->_contents );
    }
	
	/**
	 * 
	 * @param string $label
	 * @return string
	 */
	public function getContent( $label )
    {
        return $this->_contents[ $label ];
    }
	
	/**
	 * 
	 * @return string
	 */
	public function getContents()
    {
        return $this->_contents;
    }
	
	/**
	 * 
	 * @global string $_ROOT_URL
	 * @global string $_CONTENT_DIRECTORY
	 * @param string $file
	 * @return string
	 */
	public function getAbsoluteUrl( $file )
    {
        global $_ROOT_URL;
        global $_CONTENT_DIRECTORY;
        return $_ROOT_URL.$_CONTENT_DIRECTORY.$this->getId().'/'.$file;
    }
    
	public function __construct( $id )
    {
        $this->_id = $id;
        $this->_tags = array();
		$this->_contents = array();
		$this->_requests = array();
        $this->_visible = TRUE;
        
        // DEFAULT
        $this->_linkTitle = $id;
        $this->_title = $id;
		$this->_description = $id;
        //$this->_template = 'default';
		$this->_file2 = '';
		$this->_cachable = TRUE;
		$this->_template = '';
		$this->_phpHeader = '';
    }
    
}
