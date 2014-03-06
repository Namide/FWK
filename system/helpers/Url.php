<?php


class Url
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
	 * If you change it, change to the Url::BASE_PAGE_URL() and the root .htaccess
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
            $this->url = htmlentities( $_GET[self::$_arg] );//filter_input('INPUT_GET', 'page', 'FILTER_SANITIZE_URL');
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

				header( 'Location:'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl() ) );
				exit();
			}
        }
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
	public static function hasArg( $label )
	{
		return isset( $_GET[$label] );
	}
	
	/**
	 * 
	 * @param string $arg
	 * @return string
	 */
	public static function getArg( $label )
	{
		if( self::hasArg( $label ) ) return htmlentities( $_GET[$label] );
		return NULL;
	}


	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
	/**
	* @return Url
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
