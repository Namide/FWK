<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WriteUtil
 *
 * @author Damien
 */
class WriteUtil
{
	/**
	 * 
	 * @param string $pageContent
	 * @param string $fileName
	 */
	public static function writeFile( &$content, $fileName )
	{
		$path = explode( '/', $fileName );
		
		$dir = '';
		while ( count($path) > 1 )
		{
			$dir .= $path[0].'/';
			if ( !file_exists($dir) )
			{
				mkdir( $dir, 0777 );
			}
			array_shift($path);
		}

		/*$file = $path[0];
		if ( count( explode( ".", $file ) ) > 1 )
		{
			$file = $dir.$file;
		}
		else
		{
			$file = $dir.'index.html';
		}*/
		
		file_put_contents( $fileName, $content );
	}
}
