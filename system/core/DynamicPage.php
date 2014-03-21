<?php

class DynamicPage extends Page
{
	
	private $_vo;
	/**
	 * 
	 * @param boolean $vo
	 */
    public function setVo( $vo ) { $this->_vo = $vo; }
	/**
	 * 
	 * @return boolean
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
	
	public function __construct( $id, $url, $vo )
    {
		parent::__construct( $id );
		$this->_url = $url;
		$this->_vo = $vo;
    }
}
