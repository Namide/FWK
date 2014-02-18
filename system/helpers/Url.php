<?php


class Url
{
    private static $_instances = null;

    private $url = '';
    public function getUrl() { return $this->url; }
    
    private static $_arg = 'page';
	public static function getPageGetArg() { return self::$_arg; }
	
	
	public static $BASE_PAGE_URL = 'index.php?page=';
	//public static function getMainArgName() { return Url::$arg; }
    
    final private function __construct()
    {
    	if( isset( $_GET[self::$_arg] ) )
        {
            $this->url = htmlentities( $_GET[self::$_arg] );//filter_input('INPUT_GET', 'page', 'FILTER_SANITIZE_URL');
        }
        // if no URL -> redirection to good url
        else
        {
            $pages = PageList::getInstance();
            $page = $pages->getDefaultPage();
            $this->url = $page->getUrl();
            
            header( 'Location:'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl() ) );
            exit();
        }
    }

	public static function getURICacheID()
	{
		if( isset( $_GET[self::$_arg] ) )
		{
			$invalid = array( '/'=>'-', '\\'=>'-', ':'=>'-', '?'=>'-', '"'=>'-', '*'=>'-', '<'=>'-', '>'=>'-', '|'=>'-' );
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
				$url .= '.'.$key.'.'.$value;
			}
		}

		return $url;
	}
	
	public static function hasArg( $arg )
	{
		return isset( $_GET[$arg] );
	}
	public static function getArg( $arg )
	{
		if( self::hasArg( $arg ) ) return htmlentities( $_GET[$arg] );
		return NULL;
	}


	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
 
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
