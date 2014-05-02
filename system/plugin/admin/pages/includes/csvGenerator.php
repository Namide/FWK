<?php

function generateCsv( $csvName )
{
	include_once _SYSTEM_DIRECTORY.'init/imports.php';
	include_once _SYSTEM_DIRECTORY.'init/loadPages.php';
	include_once _SYSTEM_DIRECTORY.'init/Cache.php';
	
	
	$cache = new Cache();
	$empty = '';
	$cache->writesCacheFile($empty, $csvName);
	
	$i = 0;
	$contentCsv = array();
	
	$csv = fopen($csvName, 'w');
	
	fputcsv($csv, array('type','!id','!lang','url','title','description','template','header','visible','cachable','tags','phpHeader','contents','requests','!vo'), ';' );
	
	foreach (PageList::getInstance()->getPagesByUrl() as $page)
	{
		//$contentCsv .= getCsvLineByPage( $page );
		fputcsv($csv, getCsvLineByPage( $page ), ';', '"');
	}
	
	fclose($csv);

	/*$cache = new Cache();
	$cache->writesCacheFile($contentCsv, $csvName);
	
	$contentCsv = 'type;!id;!lang;url;title;description;template;header;visible;cachable;tags;phpHeader;contents;requests;!vo'."\r\n";
	foreach (PageList::getInstance()->getPagesByUrl() as $page)
	{
		$contentCsv .= getCsvLineByPage( $page );
	}*/
	
	
}

function getCsvLineByPage( &$page )
{
	//type;!id;!lang;url;title;description;template;header;visible;cachable;tags;phpHeader;contents;requests;!vo
	include_once _SYSTEM_DIRECTORY.'core/Page.php';
	include_once _SYSTEM_DIRECTORY.'core/DynamicPage.php';
	
	$type = ($page instanceof DynamicPage) ? 'dynamic' : 'static';
	
	$output = array();
	if ( $type == 'static' &&
		 $page->getId() == PageList::getInstance()->getDefaultPageId() )
	{
		array_push( $output, writeField('default') );
	}
	elseif ( $type == 'static' &&
			 $page->getId() == PageList::getInstance()->getError404PageId() )
	{
		array_push( $output, writeField('error404') );
	}
	else
	{
		array_push( $output, writeField($type) );
	}
	array_push( $output, writeField($page->getId()) );
	array_push( $output, writeField($page->getLanguage()) );
	array_push( $output, writeField($page->getUrl()) );
	array_push( $output, writeField($page->getTitle()) );
	array_push( $output, writeField($page->getDescription()) );
	array_push( $output, writeField($page->getTemplate()) );
	array_push( $output, writeField($page->getHeader()) );
	array_push( $output, writeField($page->getVisible()) );
	array_push( $output, writeField($page->getCachable()) );
	array_push( $output, writeField($page->getTags()) );
	array_push( $output, writeField($page->getPhpHeader()) );
	array_push( $output, writeField($page->getContents()) );
	array_push( $output, writeField($page->getRequests()) );
	
	if ($type === 'dynamic')	array_push( $output, writeField($page->getVo()) );
	else						array_push( $output, writeField('') );
	
	return $output;
}

function writeField( $content )
{
	if ( gettype ($content) == "array" || gettype($content) == "object" ) 
	{
		if (count($content)<1)	return hackAccent('');
		
		return hackAccent(generateConstructor($content));
	}
	elseif ( gettype ($content) == "boolean"  )
	{
		return hackAccent( ($content) ? "true" : "false" );
	}
	return hackAccent( $content );
}

function hackAccent($text)
{
	return $text;//mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8');
}
	
function generateConstructor( $data )
{
	if (	gettype($data) != "array" &&
			gettype($data) != "object" ) 
	{
		return '"'.escQuot($data).'"';
	}
	
	$output = 'array(';
	$first = TRUE;
	
	$isAssoc = isAssoc($data);
	
	foreach ($data as $key => $value)
	{
		if ( !$first ) $output .= ',';
		if ( $isAssoc ) $output .= '"'.escQuot($key).'"=>';
		
		if ( gettype ($value) === "array" )
		{
			$output	.= generateConstructor($value);
		}
		elseif ( gettype($value) === "object" )
		{
			if( get_class($value) === "RequestPage" )
			{
				$output .= $value->getSave();
			}
			else
			{
				$output .= generateConstructor( $value );
			}
		}
		elseif ( gettype ($value) === "string" )
		{
			$output	.= '"'.escQuot($value).'"';
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

function escQuot( $text )
{
	return /*'"' .*/ str_replace('"', '\"', $text ) /*.'"'*/;
}

function isAssoc($arr)
{
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/*
function getCsvLineByPage( &$page )
{
	//type;!id;!lang;url;title;description;template;header;visible;cachable;tags;phpHeader;contents;requests;!vo
	include_once _SYSTEM_DIRECTORY.'core/Page.php';
	include_once _SYSTEM_DIRECTORY.'core/DynamicPage.php';
	
	$type = ($page instanceof DynamicPage) ? 'dynamic' : 'static';
	
	$output = writeField($type);
	$output .= writeField($page->getId());
	$output .= writeField($page->getLanguage());
	$output .= writeField($page->getUrl());
	$output .= writeField($page->getTitle());
	$output .= writeField($page->getDescription());
	$output .= writeField($page->getTemplate());
	$output .= writeField($page->getHeader());
	$output .= writeField($page->getVisible());
	$output .= writeField($page->getCachable());
	$output .= writeField($page->getTags());
	$output .= writeField($page->getPhpHeader());
	$output .= writeField($page->getContents());
	$output .= writeField($page->getRequests());
	
	if ($type === 'dynamic')	$output .= writeField($page->getVo());
	else						$output .= writeField('');
	
	return $output."\r\n";
}

function writeField( $content )
{
	if ( gettype ($content) == "array" || gettype($content) == "object" ) 
	{
		return '"'.generateConstructor($content).'";';
	}
	elseif ( gettype ($content) == "boolean"  )
	{
		return ($content)?'"true";':'"false";';
	}
	return '"'.str_replace('"', '""', $content).'";';
}

function generateConstructor( $data )
{
	//include_once _SYSTEM_DIRECTORY.'core/RequestPage.php';
	
	if ( gettype ($data) != "array" && gettype($data) != "object" ) 
	{
		return str_replace('"', '""', '"'.escQuot($data).'"');
	}
	
	
	$output = 'array(';
	$first = TRUE;
	foreach ($data as $key => $value)
	{
		if ( !$first ) $output .= ',';

		$output .= '""'.$key.'""=>';
		if ( gettype ($value) === "array" ) 
		{
			$output	.= generateConstructor($value);
		}
		else if ( gettype($value) === "object" )
		{
			if( get_class($value) === "RequestPage" )
			{
				$output .= $value->getSave();
			}
			else
			{
				$output .= generateConstructor( $value );
			}
		}
		elseif( gettype ($value) === "string" )
		{
			$output	.= escQuot($value);
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

function escQuot( $text )
{
	return '"' . str_replace('"', '\"', $text ) .'"';
}
*/