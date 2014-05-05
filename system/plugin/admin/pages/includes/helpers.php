<?php

function getListDir( $dir )
{
	$array = array();
	
	if ( !file_exists($dir) ) { return $array; }
	if ( is_file($dir) ) { return $array; }
	
	array_push($array, $dir );
	
	$files = array_diff( scandir( $dir ), array( '.', '..', '.DS_Store', 'Thumbs.db' ) );
	foreach ( $files as $file )
	{
		if ( is_dir("$dir/$file") )
		{
			$array = array_merge( $array, getListDir( "$dir/$file" ) );
		}
	}
	
	return $array;
}









/*function getJsLinkChecker()
{
	$output = '';
	return $output;
}*/

