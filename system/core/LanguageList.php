<?php

class LanguageList
{
   
    private static $instances = array();
 
    private $_languages;
    public function getList() { return $this->_languages; }
    
    private $_defaultLanguage;
    
    final private function __construct()
    {
        $this->_languages = array('all');
    }
    
    public function addDefaultLanguage( $lang )
    {
        $this->_defaultLanguage = $lang;
        $this->addLanguage( $lang );
    }
    
	public function getDefaultLanguage()
    {
        return $this->_defaultLanguage;
    }
	
    public function addLanguage( $lang )
    {
        array_push( $this->_languages, $lang );
    }
    
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
