<?php

class SearchEngine
{
	
	private static $_instances = null;
	private static $_on_reset = false;
	
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
	
	public function reset()
	{
		if ( self::$_on_reset ) return;
		
		self::$_on_reset = true;
		$pages = PageList::getInstance()->getPagesByUrl();
		foreach ($pages as $pageTemp)
		{
			PageList::getInstance()->updatePage( $pageTemp );
			$fileName = UrlUtil::getURICacheID( $pageTemp->getUrl() ).'.txt';
			$body = $pageTemp->getBody();
			
			$body = preg_replace('/<[^>]*>/', ' ', $body);
			$body = str_replace("\t", ' ', $body);
			$body = str_replace("\n", ' ', $body);
			$body = str_replace("\r", ' ', $body);
			while( strpos($body, '  ') !== false )
			{
				$body = str_replace('  ', ' ', $body);
			}
			FileUtil::writeFile( $body, $this->_dir.$fileName );
		}
		self::$_on_reset = false;
		
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
