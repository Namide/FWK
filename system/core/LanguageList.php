<?php

class LanguageList
{
   
    private static $instances = array();
 
    private $_languages;
	/**
	 * 
	 * @return array
	 */
    public function getList() { return $this->_languages; }
    
    private $_defaultLanguage;
    
    final private function __construct()
    {
        $this->_languages = array('all');
    }
    
	/**
	 * 
	 * @param string $lang
	 */
    public function addDefaultLanguage( $lang )
    {
        $this->_defaultLanguage = $lang;
        $this->addLanguage( $lang );
    }
    
	/**
	 * 
	 * @return string
	 */
	public function getDefaultLanguage()
    {
        return $this->_defaultLanguage;
    }
	
	/**
	 * 
	 * @param string $lang
	 */
    public function addLanguage( $lang )
    {
        array_push( $this->_languages, $lang );
    }
    
	/**
	 * 
	 * @param string $lang
	 * @return boolean
	 */
	public function hasLanguage( $lang )
	{
		return array_key_exists( $this->_languages, $lang );
	}
	
	/**
	 * 
	 * @return string
	 */
    public function getLangByNavigator()
    {
        $lang = explode( ',', htmlentities($_SERVER['HTTP_ACCEPT_LANGUAGE']) );
        $lang = strtolower(substr(chop($lang[0]),0,2));

        foreach ($this->_languages as $value)
        {
            if( $value == $lang )
            {
                return $value;
            }
        }
        
        return $this->_defaultLanguage;
    }
    
    final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	 * 
	 * @return LanguageList
	 */
    final public static function getInstance()
    {
        $c = get_called_class();
 
        if(!isset(self::$instances[$c]))
        {
            self::$instances[$c] = new $c;
        }
 
        return self::$instances[$c];
    }
}
