<?php

/**
 * All the datas of a page.
 */
class Page
{
    
	public static $TYPE_ERROR_404 = 'type-error404';
	
	public static $CALL_PAGE = 'call-page';
	public static $CALL_REQUEST = 'call-request';
	
    protected $_id;
	/**
	 * 
	 * @return string
	 */
    public function getId() { return $this->_id; }
    
	protected $_phpHeader;
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
	
    protected $_visible;
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

    protected $_cachable;
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

    protected $_language;
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

    protected $_header;
	/**
	 * 
	 * @param string $header
	 */
    public function setHeader( $header ) { $this->_header = $header; }
	/**
	 * Header content with mustache's process
	 * 
	 * @return string
	 */
    public function getHeader() { return InitUtil::getInstance()->mustache($this->_header, $this); }
	/**
	 * Header content without mustache's process
	 * 
	 * @return type
	 */
	public function getHeaderSrc() { return $this->_header; }

	protected $_type;
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
	
	protected $_call;
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
	
    protected $_body;
	/**
	 * 
	 * @param string $body
	 */
    public function setBody( $body ) { $this->_body = $body; }
	/**
	 * Body content with mustache's process
	 * 
	 * @return string
	 */
    public function getBody() { return InitUtil::getInstance()->mustache($this->_body, $this); }
	/**
	 * Body content without mustache's process
	 * 
	 * @return type
	 */
	public function getBodySrc() { return $this->_body; }

    protected $_title;
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

	protected $_description;
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
	
    protected $_template;
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

	protected $_buildFile;
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
	
	
    protected $_tags;
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
	 * @param array $tag
	 */
    public function addTags( $tags )
	{
		$this->_tags = array_merge((array)$this->_tags, (array)$tags);
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
	
	protected $_requests;
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
	 * @return boolean
	 */
    public function hasRequest( $url )
    {
        return array_key_exists( $url, $this->_requests );
    }
		
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
        return $this->_requests[ $url ];
    }
	
	/**
	 * 
	 * @return array
	 */
	public function getRequests()
    {
        return $this->_requests;
    }
	
	
	protected $_contents;
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
	 * Content with mustache's process
	 * 
	 * @param string $label
	 * @return string
	 */
	public function getContent( $label )
    {
        return InitUtil::getInstance()->mustache($this->_contents[ $label ], $this);
    }
	
	/**
	 * Content without mustache's process
	 * 
	 * @param type $label
	 * @return type
	 */
	public function getContentSrc( $label )
    {
		return $this->_contents[ $label ];
	}
	
	/**
	 * Contents (in array of string) with mustache's process
	 * 
	 * @return string
	 */
	public function getContents()
    {
		$contents = array();
		foreach ($this->_contents as $label => $content)
		{
			$contents[$label] = InitUtil::getInstance()->mustache($content, $this) ;
		}
        return $contents;
    }
	/**
	 * Contents (in array of string) without mustache's process
	 * 
	 * @return array
	 */
	public function getContentsSrc()
    {
		return $this->_contents;
    }
	
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
	public function getAbsoluteUrl( $file )
    {
        return _ROOT_URL._CONTENT_DIRECTORY.$this->getId().'/'.$file;
    }
    
	
	/**
	 * 
	 * @return string
	 */
	public function getSave()
	{
		$obj = get_object_vars($this);
		$output = 'Page::update(new Page("'.$this->_id.'"),';
		$output .= SaveUtil::arrayToStrConstructor($obj);
		$output .= ')';
		
		return $output;
	}
	
	/**
	 * 
	 * @param Page $page
	 * @param array $save
	 * @return Page
	 */
	public static function update( &$page, $save )
	{
		foreach ($save as $key => $value)
		{
			$page->$key = $value;
		}
		return $page;
	}
	
	
	public function __construct( $id )
    {
        $this->_id = $id;
        $this->_tags = array();
		$this->_contents = array();
		$this->_requests = array();
        $this->_visible = true;
        
        // DEFAULT
        $this->_linkTitle = $id;
        $this->_title = $id;
		$this->_description = $id;
        //$this->_template = 'default';
		$this->_file2 = '';
		$this->_cachable = true;
		$this->_template = '';
		$this->_phpHeader = '';
    }
    
}
