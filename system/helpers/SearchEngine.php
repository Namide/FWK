<?php

class SearchEngine
{
	
	private static $_instances = null;

	private $_dir;
	private $_pathSummary;
	
	final private function __construct()
    {
        $this->_dir = _CACHE_DIRECTORY.'search-datas/';
		$this->_pathSummary = $this->_dir.'summary.php';
		
		if ( !file_exists($this->_pathSummary) )
		{
			$this->reset();
		}
	}
	
	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	 * @return SearchEngine
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
