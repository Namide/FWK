<?php

class DynamicPage extends Page
{
	
	private $_vo;
	/**
	 * 
	 * @param type $vo
	 */
    public function setVo( $vo ) { $this->_vo = $vo; }
	/**
	 * 
	 * @return type
	 */
    public function getVo() { return $this->_vo; }
	/**
	 * 
	 * @param string $url
	 */
	public function setUrl($url)
	{
		// disables the url
	}
	
	private $_name;
	/**
	 * 
	 * @param string $name
	 */
    public function setName( $name ) { $this->_name = $name; }
	/**
	 * 
	 * @return string
	 */
    public function getName() { return $this->_name; }
	
	
	public function __construct( $id, $url, $vo )
    {
		parent::__construct( $id );
		$this->_url = $url;
		$this->_vo = $vo;
    }
}
