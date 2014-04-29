<?php

class Element
{
    
    private $_language;
	/**
	 * 
	 * @param string $language
	 */
    public function setLanguage( $language )
	{
		if ( !LanguageList::getInstance()->hasLanguage($language) )
		{
			trigger_error( 'The Language '.$language.' don\'t exist', E_USER_ERROR );
		}
		$this->_language = $language;
	}
	/**
	 * 
	 * @return string
	 */
    public function getLanguage() { return $this->_language; }

    private $_id;
	/**
	 * 
	 * @param string $id
	 */
    public function setId( $id ) { $this->_id = $id; }
	/**
	 * 
	 * @return string
	 */
    public function getId() { return $this->_id; }
	
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
	
    private $_tags;
	/**
	 * 
	 * @param string $tag
	 */
    public function addTag( $tag )
	{
		$this->_tags[] = $tag;
	}
	
	/**
	 * 
	 * @param array $tags
	 */
    public function addTags( $tags )
    {
        foreach ( $tags as $tag )
        {
            $this->addCategory( $tag );
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
	 * @param array $tag
	 * @return boolean
	 */
    public function hasTags( $tags )
    {
		foreach ($tags as $tag)
		{
			if ( !$this->hasTag($tag) )
			{
				return false;
			}
		}
		return true;
    }
	
	/**
	 * 
	 * @return array
	 */
	public function getTags()
    {
        return $this->_tags;
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
	 * @return string
	 */
	public function getSave()
	{
		$obj = get_object_vars($this);
		$output = 'Element::update(new Page("'.$this->_name.'"),';
		$output .= SaveUtil::arrayToStrConstructor($obj);
		$output .= ')';
		
		return $output;
	}
	
	/**
	 * 
	 * @param Element $element
	 * @param array $save
	 * @return Element
	 */
	public static function update( &$element, $save )
	{
		foreach ($save as $key => $value)
		{
			$element->$key = $value;
		}
		return $element;
	}
	
	
	
	public function __construct( $name, $lang = NULL )
    {
        $this->_name = $name;
		
		if ( $lang === NULL )
		{
			$lang = LanguageList::getInstance()->getDefaultLanguage();
		}
		
		$this->_language = $lang;
		$this->_contents = array();
    }
    
}
