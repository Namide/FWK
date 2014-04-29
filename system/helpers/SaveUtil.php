<?php

class SaveUtil
{
	/**
	 * 
	 * @param array $array
	 * @return string
	 */
	public static function arrayToStrConstructor( $data )
	{
		$output = 'array(';
		$first = TRUE;
		foreach ($data as $key => $value)
		{
			if ( !$first ) $output .= ',';
			
			$output .= '"'.$key.'"=>';
			if ( gettype ($value) === "array" ) 
			{
				$output	.= self::arrayToStrConstructor($value);
			}
			else if ( gettype($value) === "object" )
			{
				if ( get_class($value) === "Page" )
				{
					$output .= $value->getSave();
				}
				elseif ( get_class($value) === "RequestPage" )
				{
					$output .= $value->getSave();
				}
				elseif ( get_class($value) === "PageList" )
				{
					$output .= $value->getSave();
				}
				elseif ( get_class($value) === "DynamicPage" )
				{
					$output .= $value->getSave();
				}
				elseif ( get_class($value) === "LanguageList" )
				{
					$output .= $value->getSave();
				}
				else
				{
					$output .= self::arrayToStrConstructor( $value );
				}
			}
			elseif( gettype ($value) === "string" )
			{
				$output	.= SaveUtil::escQuot($value);
			}
			else
			{
				$output	.= '"'.$value.'"';
			}
			
			if ( $first ) $first = FALSE;
		}
		$output .= ')';
		return $output;
	}
	
	
	protected static function escQuot( $text )
	{
		return '"' . str_replace('"', '\"', $text ) .'"';
		//return 'str_replace(\'\"\', \'"\', "' . str_replace('"', '\"', $text ) .'" )';
	}
	
}
