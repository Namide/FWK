<?php

class ElementList
{
    
    private static $_instances = array();
	
	
    protected $_elements;
	/**
	 * 
	 * @return array
	 */
	public function getElementsById()
	{
		return $this->_elements;
	}
	
	final private function __construct()
    {
        $this->reset();
	}
	
	public function reset()
	{
		$this->_elements = array();
	}

	/**
	 * 
	 * @param Element $element
	 */
    public function addElement( $element )
    {
		
        $lang = $element->getLanguage();//$language->getList();
        
		if( $this->hasElement( $element->getName(), $lang ) )
		{
			trigger_error( 'The element ['.$element->getName().', '.$lang.'] already exist', E_USER_ERROR );
		}
		array_push($this->_elements, $element);
    }
	
	/**
	 * 
	 * @param string $name
	 * @param string $lang
	 * @return Element
	 */
	public function getElementByName( $name, $lang )
	{
		foreach ( $this->_elements as $element )
        {
			if( ($element->getLanguage() == $lang || $lang == 'all') && $element->getName() == $name )
			{
				return $element;
			}
        }
		
		trigger_error( 'The Element ['.$name.'] don\'t exist in the language '.$lang, E_USER_ERROR );
	}
		
		
	/**
	 * 
	 * @param string $tag
	 * @param string $lang
	 * @return array
	 */
	public function getElementsByTag( $tag, $lang )
    {
		$elements = array();
        
		foreach ( $this->_elements as $element )
        {
			if( ($element->getLanguage() == $lang || $lang == 'all') && $element->hasTag($tag) )
			{
				array_push( $elements, $element );
			}
        }
		return $elements;
    }
	
	/**
	 * 
	 * @param array $tags
	 * @param string $lang
	 * @return array
	 */
    public function getElementsByTags( $tags, $lang )
    {
		
		$elements = array();
        foreach ( $this->_elements as $element )
        {
            foreach ( $tags as $category )
            {
                if( ($element->getLanguage() == $lang || $lang == 'all') && $element->hasTag($category) )
                {
                    array_push( $elements, $element );
                    break 1;
                }
            }
        }
        return $elements;
    }
    
	
	/**
	 * 
	 * @param string $lang
	 * @return array
	 */
    public function getAllElements( $lang )
    {
        
		$elements = array();
        foreach ( $this->_elements as $element )
        {
            $langTemp = $element->getLanguage();
            if ( ( $langTemp == $lang || $lang == 'all' ) )
            {
                array_push( $elements, $element );
            }
        }
        return $elements;
    }
	
	/**
	 * 
	 * @param string $id
	 * @param string $lang
	 * @return boolean
	 */
	public function hasElement( $name, $lang )
    {
		foreach ( $this->_elements as $element )
        {
            $nameTemp = $element->getName();
            $langTemp = $element->getLanguage();
            if ( $nameTemp === $name && $langTemp === $lang )
            {
                return TRUE;
            }
        }
        return FALSE;
    }
	
	/**
	 * 
	 * @param string $url
	 * @return boolean
	 */
	public function hasName( $url )
    {
		foreach ( $this->_elements as $element )
        {
            $nameTemp = $element->getName();
            if ( $nameTemp === $name )
            {
                return TRUE;
            }
        }
		return FALSE;
    }
	
    final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	 * 
	 * @return ElementList
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
