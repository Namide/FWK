<?php

/**
 * URL managment.
 */
class UrlUtil
{
    private static $_instances = null;

    private $url = '';
	/**
	 * 
	 * @return string
	 */
    public function getUrl() { return $this->url; }
    
    private static $_arg = 'u';
	/**
	 * Label of the variable of the GET who content the URL.
	 * If you change it, change to the UrlUtil::BASE_PAGE_URL() and the root .htaccess
	 * 
	 * @return string
	 */
	public static function getPageGetArg() { return self::$_arg; }
	
	public static $BASE_PAGE_URL = 'index.php?u=';
	
    final private function __construct()
    {
    	$this->reset();
    }
	
	public function reset()
	{
		
		if( isset( $_GET[self::$_arg] ) )
        {
			$this->url = $this->getCleanUrl();
        }
        // if no URL -> redirection to good url
        else
        {
			$path = $_SERVER['PHP_SELF'];
			$file = basename($path);
			if ( $file == 'index.php' )
			{
				$pages = PageList::getInstance();
				$page = $pages->getDefaultPage();
				$this->url = $page->getUrl();

				header( 'Location:'.InitUtil::getInstance()->urlPageToAbsUrl( $page->getUrl() ) );
				exit();
			}
        }
	}
	
	private function getCleanUrl()
	{
		$totalUrl = $_GET[self::$_arg];
		
		$urlParts = explode('?', $totalUrl);
		$l = count( $urlParts );
		for( $i = 1; $i < $l; $i++ )
		{
			$microParts = explode('&', $urlParts[$i]);
			$l2 = count($microParts);
			for( $j = 0; $j < $l2; $j++ )
			{
				$this->addGet( $microParts[$j] );
			}
		}
		
		return htmlentities($urlParts[0]);//filter_input('INPUT_GET', 'page', 'FILTER_SANITIZE_URL');
	}
	
	private function addGet( $get )
	{
		$a = explode('=', $get);
		$_GET[$a[0]] = $a[1];
	}

	/**
	 * 
	 * @return string
	 */
	public static function getURICacheID()
	{
		if( isset( $_GET[self::$_arg] ) )
		{
			$invalid = array( /*'/'=>'-',*/ '\\'=>'-', ':'=>'-', '?'=>'-', '"'=>'-', '*'=>'-', '<'=>'-', '>'=>'-', '|'=>'-' );
			$url = str_replace(array_keys($invalid), array_values($invalid), htmlentities( $_GET[self::$_arg] ) );
		}
		else
		{
			$url = 'empty';
		}

		foreach ( $_GET as $key => $value )
		{
			if ( $key != self::$_arg ) 
			{
				$url .= '-'.$key.'-'.$value;
			}
		}

		return $url;
	}
	
	/**
	 * 
	 * @param string $arg
	 * @return boolean
	 */
	public static function hasGet( $label )
	{
		return isset( $_GET[$label] );
	}
	
	/**
	 * 
	 * @param string $arg
	 * @return string
	 */
	public static function getGet( $label )
	{
		if( self::hasGet( $label ) ) return htmlentities( $_GET[$label] );
		return NULL;
	}


	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	* @return UrlUtil
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
