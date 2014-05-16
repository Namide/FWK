<?php

/**
 * Datas for a request.
 * For example, you can use it to download data with AJAX.
 */
class RequestPage
{
    
    
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
	
    private $_content;
	/**
	 * 
	 * @param string $content
	 */
    public function setContent( $content ) { $this->_content = $content; }
	/**
	 * Content with mustache's process
	 * 
	 * @return string
	 */
    public function getContentFinal() { return InitUtil::getInstance()->mustache( $this->_content, $this ); }
	
	/**
	 * Content without mustache's process
	 * 
	 * @return string
	 */
	public function getContent() { return $this->_content; }

	
	/**
	 * 
	 * @return string
	 */
	public function getSave()
	{
		$obj = get_object_vars($this);
		$output = 'RequestPage::update(new RequestPage("'.$this->_url.'"),';
		$output .= SaveUtil::arrayToStrConstructor($obj);
		$output .= ')';
		
		return $output;
	}
	
	
	/**
	 * 
	 * @param RequestPage $requestPage
	 * @param array $save
	 * @return RequestPage
	 */
	public static function update( &$requestPage, $save )
	{
		foreach ($save as $key => $value)
		{
			$requestPage->$key = $value;
		}
		return $requestPage;
	}
	
	public function __construct( $url, $cachable = FALSE )
    {
        $this->_url = $url;
        
        // DEFAULT
        $this->_content = '';
		$this->_cachable = $cachable;
		$this->_phpHeader = '';
    }
    
}
