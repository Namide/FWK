<?php

class DynamicPage extends Page
{
	
	protected $_vo;
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
	
	protected $_name;
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
	
	
	
	
	/**
	 * 
	 * @return string
	 */
	public function getSave()
	{
		$obj = get_object_vars($this);
		$output = 'DynamicPage::update(new DynamicPage("'.$this->_id.'", "'.$this->_url.'", 0 ),';
		$output .= SaveUtil::arrayToStrConstructor($obj);
		$output .= ')';
		
		return $output;
	}
	
	/**
	 * 
	 * @param DynamicPage $page
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
	
	
	
	public function __construct( $id, $url, $vo )
    {
		parent::__construct( $id );
		$this->_url = $url;
		$this->_vo = $vo;
    }
}
