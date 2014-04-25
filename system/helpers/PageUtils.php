<?php

class PageUtils
{
    
    final private function __construct()
    {
        
    }
    
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
	public static function getRootAbsoluteUrl( $file )
    {
        return _ROOT_URL.$file;
    }
	
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public static function getTemplateAbsoluteUrl( $file )
    {
       return _ROOT_URL._TEMPLATE_DIRECTORY.$file;
    }
    
	/**
	 * 
	 * @param string $file
	 * @return string
	 */
    public static function getContentAbsoluteUrl( $file )
    {
       return _ROOT_URL._CONTENT_DIRECTORY.$file;
    }
	
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public static function urlPageToAbsoluteUrl( $url )
    {
	    return _ROOT_URL.( (!_URL_REWRITING) ? (Url::$BASE_PAGE_URL) : '' ).$url;
    }
    
	/**
	 * 
	 * @param string $idPage
	 * @param string $lang
	 * @return string
	 */
    public static function getAbsoluteUrl( $idPage, $lang = NULL )
    {
		$pagesClass = PageList::getInstance();
		if ( $lang === NULL ) $lang = TemplateUtils::getInstance()->getLanguage();
        $page = $pagesClass->getPage( $idPage, $lang );
		
         return _ROOT_URL.( (!_URL_REWRITING) ? (Url::$BASE_PAGE_URL) : '' ).$page->getUrl();
    }
    
	/**
	 * 
	 * @param string $url
	 * @return string
	 */
    public static function urlToLanguage( $url )
    {
        $pagesClass = PageList::getInstance();
        $pageClass = $pagesClass->getPageByUrl( $url );
        return $pageClass->getLanguage();
    }
	
	/**
	 * 
	 * @param string $text
	 * @param Page $page
	 * @return Page
	 */
	public static function mustache( $text, &$page )
    {
		$replacePage = preg_replace('/\{\{pathCurrentPage:(.*?)\}\}/', $page->getAbsoluteUrl('$1'), $text);
        $replacePage = preg_replace('/\{\{urlPageToAbsoluteUrl:(.*?)\}\}/', PageUtils::urlPageToAbsoluteUrl('$1'), $replacePage);
        $replacePage = preg_replace('/\{\{pathTemplate:(.*?)\}\}/', PageUtils::getTemplateAbsoluteUrl('$1'), $replacePage);
		$replacePage = preg_replace('/\{\{pathContent:(.*?)\}\}/', PageUtils::getContentAbsoluteUrl('$1'), $replacePage);

		//variables substitution (es. {$title})
		/*$replacePage = self::var_replace( $replacePage, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $loop_level = 0, $echo = true );*/
		
		$pageList = PageList::getInstance();
		if ( $pageList->getInitialised() )
		{
			$replacePage = preg_replace_callback( '/\{\{idPageToAbsoluteUrl:(.*?)\}\}/', function ($matches)
			{
				$lang = TemplateUtils::getInstance()->getLanguage();
				return PageUtils::getAbsoluteUrl( $matches[1], $lang );
			}, $replacePage );
		}

        return $replacePage;
    }
	
	
	final public function __clone()
    {
        trigger_error( 'You can\'t clone.', E_USER_ERROR );
    }
	
	
	/*protected static function var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level = null, $echo = null ){

		//all variables
		if( preg_match_all( '/' . $tag_left_delimiter . '\$(\w+(?:\.\${0,1}[A-Za-z0-9_]+)*(?:(?:\[\${0,1}[A-Za-z0-9_]+\])|(?:\-\>\${0,1}[A-Za-z0-9_]+))*)(.*?)' . $tag_right_delimiter . '/', $html, $matches ) )
		{

			for( $parsed=array(), $i=0, $n=count($matches[0]); $i<$n; $i++ )
				$parsed[$matches[0][$i]] = array('var'=>$matches[1][$i],'extra_var'=>$matches[2][$i]);

			foreach( $parsed as $tag => $array ){

					//variable name ex: news.title
					$var = $array['var'];

					//function and parameters associate to the variable ex: substr:0,100
					$extra_var = $array['extra_var'];

					// check if there's any function disabled by black_list
					self::function_check( $tag );

					$extra_var = self::var_replace( $extra_var, null, null, null, null, $loop_level );

					// check if there's an operator = in the variable tags, if there's this is an initialization so it will not output any value
					$is_init_variable = preg_match( "/^[a-z_A-Z\.\[\](\-\>)]*=[^=]*$/", $extra_var );

					//function associate to variable
					$function_var = ( $extra_var and $extra_var[0] == '|') ? substr( $extra_var, 1 ) : null;

					//variable path split array (ex. $news.title o $news[title]) or object (ex. $news->title)
					$temp = preg_split( "/\.|\[|\-\>/", $var );

					//variable name
					$var_name = $temp[ 0 ];

					//variable path
					$variable_path = substr( $var, strlen( $var_name ) );

					//parentesis transform [ e ] in [" e in "]
					$variable_path = str_replace( '[', '["', $variable_path );
					$variable_path = str_replace( ']', '"]', $variable_path );

					//transform .$variable in ["$variable"] and .variable in ["variable"]
					$variable_path = preg_replace('/\.(\${0,1}\w+)/', '["\\1"]', $variable_path );

					// if is an assignment also assign the variable to $this->var['value']
					if( $is_init_variable )
						$extra_var = "=\$this->var['{$var_name}']{$variable_path}" . $extra_var;



					//if there's a function
					if( $function_var ){

							// check if there's a function or a static method and separate, function by parameters
							$function_var = str_replace("::", "@double_dot@", $function_var );


							// get the position of the first :
							if( $dot_position = strpos( $function_var, ":" ) ){

								// get the function and the parameters
								$function = substr( $function_var, 0, $dot_position );
								$params = substr( $function_var, $dot_position+1 );

							}
							else{

								//get the function
								$function = str_replace( "@double_dot@", "::", $function_var );
								$params = null;

							}

							// replace back the @double_dot@ with ::
							$function = str_replace( "@double_dot@", "::", $function );
							$params = str_replace( "@double_dot@", "::", $params );
					}
					else
							$function = $params = null;

					//if it is inside a loop
					if( $loop_level ){
							//verify the variable name
							if( $var_name == 'key' )
									$php_var = '$key' . $loop_level;
							elseif( $var_name == 'value' )
									$php_var = '$value' . $loop_level . $variable_path;
							elseif( $var_name == 'counter' )
									$php_var = '$counter' . $loop_level;
							else
									$php_var = '$' . $var_name . $variable_path;
					}else
							$php_var = '$' . $var_name . $variable_path;

					// compile the variable for php
					if( isset( $function ) )
							$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
					else
							$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

					$html = str_replace( $tag, $php_var, $html );


			}
		}

		return $html;
	}
	
	static $black_list = array( '\$this', 'raintpl::', 'self::', '_SESSION', '_SERVER', '_ENV',  'eval', 'exec', 'unlink', 'rmdir' );
	protected static function function_check( $code ){

		$preg = '#(\W|\s)' . implode( '(\W|\s)|(\W|\s)', self::$black_list ) . '(\W|\s)#';

		// check if the function is in the black list (or not in white list)
		if( count(self::$black_list) && preg_match( $preg, $code, $match ) ){

			// find the line of the error
			$line = 0;
			$rows=explode("\n",$this->tpl['source']);
			while( !strpos($rows[$line],$code) )
				$line++;

			// stop the execution of the script
			$e = new RainTpl_SyntaxException('Unallowed syntax in ' . $this->tpl['tpl_filename'] . ' template');
			throw $e->setTemplateFile($this->tpl['tpl_filename'])
				->setTag($code)
				->setTemplateLine($line);
		}

	}*/
	
}