<?php

class Page
{
    
	public static $TYPE_ERROR_404 = 'error404';
	
    private $_id;
    public function getId() { return $this->_id; }
    
	private $_phpHeader;
    public function setPhpHeader( $phpHeader ) { $this->_phpHeader = $phpHeader; }
    public function getPhpHeader() { return $this->_phpHeader; }
	
    private $_visible;
    public function setVisible( $visible ) { $this->_visible = $visible; }
    public function getVisible() { return $this->_visible; }

    private $_cachable;
	public function setCachable( $cachable ) { $this->_cachable = $cachable; }
    public function getCachable() { return $this->_cachable; }

    protected $_url;
    public function setUrl( $url ) { $this->_url = $url; }
    public function getUrl() { return $this->_url; }

    private $_language;
    public function setLanguage( $language ) { $this->_language = $language; }
    public function getLanguage() { return $this->_language; }

    private $_header;
    public function setHeader( $header ) { $this->_header = $header; }
    public function getHeader() { return $this->_header; }

	private $_type;
    public function setType( $type ) { $this->_type = $type; }
    public function getType() { return $this->_type; }
	
    private $_body;
    public function setBody( $body ) { $this->_body = $body; }
    public function getBody() { return $this->_body; }

    private $_title;
    public function setTitle( $title ) { $this->_title = $title; }
    public function getTitle() { return $this->_title; }

    private $_preface;
    public function setPreface( $preface ) { $this->_preface = $preface; }
    public function getPreface() { return $this->_preface; }

    private $_template;
    public function setTemplate( $template ) { $this->_template = $template; }
    public function getTemplate() { return $this->_template; }

	private $_file2;
    public function setFile2( $file ) { $this->_file2 = $file; }
    public function getFile2() { return $this->_file2; }
	
    private $_categories;
    public function addCategory( $category )
	{
		array_push($this->_categories, $category);
	}
    public function addCategories( $categories )
    {
        foreach ( $categories as $category )
        {
            $this->addCategory( $category );
        }
    }
    public function hasCategory( $category )
    {
        return in_array($category, $this->_categories );
    }
	public function getCategories()
    {
        return $this->_categories;
    }
	private $_contents;
    public function addContent( $label, $value )
	{
		if ( $this->hasContent($label) )
		{
			trigger_error( 'This content already exist: '.$label.' ('.$this->id.', '.$this->language.')', E_USER_ERROR);
		}
		$this->_contents[$label] = $value;
	}
    public function addContents( $arrayOfContentByLabel )
    {
        foreach ( $arrayOfContentByLabel as $label => $content )
        {
            $this->addContent( $label, $content );
        }
    }
    public function hasContent( $label )
    {
        foreach ( $this->_contents as $labelTemp => $contentTemp )
        {
            if ( $labelTemp == $label ) { return TRUE; }
        }
        return FALSE;
    }
	public function getContent( $label )
    {
        return $this->_contents[$label];
    }
	public function getContents()
    {
        return $this->_contents;
    }
	
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
