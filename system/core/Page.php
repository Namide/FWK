<?php

class Page
{
    
	public static $TYPE_ERROR_404 = 'error404';
	
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

    private $_preface;
	/**
	 * 
	 * @param string $preface
	 */
    public function setPreface( $preface ) { $this->_preface = $preface; }
	/**
	 * 
	 * @return string
	 */
    public function getPreface() { return $this->_preface; }

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

	private $_file2;
	/**
	 * 
	 * @param string $file
	 */
    public function setFile2( $file ) { $this->_file2 = $file; }
	/**
	 * 
	 * @return string
	 */
    public function getFile2() { return $this->_file2; }
	
    private $_categories;
	/**
	 * 
	 * @param string $category
	 */
    public function addCategory( $category )
	{
		array_push($this->_categories, $category);
	}
	
	/**
	 * 
	 * @param array $categories
	 */
    public function addCategories( $categories )
    {
        foreach ( $categories as $category )
        {
            $this->addCategory( $category );
        }
    }
	
	/**
	 * 
	 * @param string $category
	 * @return boolean
	 */
    public function hasCategory( $category )
    {
        return in_array($category, $this->_categories );
    }
	
	/**
	 * 
	 * @return array
	 */
	public function getCategories()
    {
        return $this->_categories;
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
        foreach ( $this->_contents as $labelTemp => $contentTemp )
        {
            if ( $labelTemp == $label ) { return TRUE; }
        }
        return FALSE;
    }
	
	/**
	 * 
	 * @param string $label
	 * @return string
	 */
	public function getContent( $label )
    {
        return $this->_contents[$label];
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
        $this->_categories = array();
		$this->_contents = array();
        $this->_visible = TRUE;
        
        // DEFAULT
        $this->_linkTitle = $id;
        $this->_title = $id;
        //$this->_template = 'default';
		$this->_file2 = '';
		$this->_cachable = TRUE;
		$this->_template = '';
		$this->_phpHeader = '';
    }
    
}
