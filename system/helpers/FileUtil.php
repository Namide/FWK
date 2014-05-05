<?php

/**
 * Utils to write directories or files.
 *
 * @author Namide
 */
class FileUtil
{
	
	/**
	 * Writes the content in a file.
	 * If the directory doesn't exist, it's automatically created.
	 * 
	 * @param string &$content
	 * @param string $fileName
	 */
	public static function writeFile( &$content, $fileName )
	{
		self::writeDirOfFile( $fileName );
		file_put_contents( $fileName, $content, LOCK_EX );
	}
	
	/**
	 * Writes recursively the directories of a files if it doesn't exist.
	 * 
	 * @param string $fileName
	 */
	public static function writeDirOfFile( $fileName )
	{
		$dir = explode( '/', $fileName );
		array_pop( $dir );
		self::writeDir( implode($dir, '/') );
	}
	
	/**
	 * Writes a directory if it doesn't exist.
	 * It works recursively.
	 * 
	 * @param string $dir
	 */
	public static function writeDir( $dir )
	{
		$path = explode( '/', $dir );
		
		$dir = '';
		while ( count($path) > 0 )
		{
			$dir .= $path[0].'/';
			if ( !file_exists($dir) )
			{
				mkdir( $dir, 0777 );
			}
			array_shift($path);
		}
		
	}
	
	/**
	 * 
	 * @param string $directory
	 * @return float
	 */
	public static function getDirSize($directory)
	{
		$size = 0;
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
		{
			$size += $file->getSize();
		}
		return $size;
	} 
	
	/**
	 * 
	 * @param string $path
	 * @param bool $color
	 * @return string
	 */
	public static function getFormatedSize( $path, $round = 2 )
	{
		$size = self::getDirSize($path);
		
		//Size must be bytes!
		$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;

		return round($size,$round).' '.$sizes[$i];
		
		/*if ( !$color ) return $sizeChar;

		if ( $i < 2 && $size < 150 ) 		return '<span style="color:green">'.$sizeChar.'</span>';
		else if ( $i > 1 || ($i == 1 && $size > 700) ) return '<strong style="color:red">'.$sizeChar.'</strong>';
		return $sizeChar;*/
	}
	
	/**
	 * 
	 * @param string $dir
	 * @return int
	 */
	public static function delDirRecursively( $dir )
	{
		if ( !file_exists($dir) )
		{
			return 0;//echo 'error: No such file or directory "'.$dir.'"';
		}

		$files = array_diff( scandir($dir), array('.','..') );
		foreach ($files as $file)
		{
			if (is_dir($dir.'/'.$file))
			{
				self::delDirRecursively($dir.'/'.$file);
			}
			else
			{
				unlink($dir.'/'.$file);
			}
		}
		return rmdir($dir);
	}
	
	/**
	 * Delete all files and directories and return the number of file deleted.
	 * 
	 * @param string $dir
	 * @return int
	 */
	public static function delEmptyDirRecursively( $dir )
	{
		$numChilds = 0;

		if ( !file_exists($dir) )	{ return 0; }
		if ( is_file($dir) )		{ return 1; }

		$files = array_diff( scandir($dir), array( '.', '..', '.DS_Store', 'Thumbs.db' ) );
		foreach ($files as $file)
		{
			if (is_dir($dir.'/'.$file))
			{
				$numChilds += self::delEmptyDirRecursively($dir.'/'.$file);
			}
			else
			{
				$numChilds++;
			}
		}

		if ( $numChilds < 1 )
		{
			rmdir($dir);
		}

		return $numChilds;
	}
	
	/**
	 * Copy the directory ($dir2copy) to the directory ($dir_paste)
	 * 
	 * @param string $dir2copy
	 * @param string $dir_paste
	 */
	public static function copyDir( $dir2copy, $dir_paste )
	{
		if ( is_dir($dir2copy) )
		{

			if ( $dh = opendir($dir2copy) )
			{     
				while ( ($file = readdir($dh)) !== false )
				{
					if (!is_dir($dir_paste))
					{
						mkdir ($dir_paste, 0777);
					}
					
					if(is_dir($dir2copy.$file) && $file != '..'  && $file != '.')
					{
						copyDir ( $dir2copy.$file.'/' , $dir_paste.$file.'/' ); 
					}
					elseif( $file != '..' &&
							$file != '.' )
					{
						copy ( $dir2copy.$file , $dir_paste.$file ); 
					}
				}

				closedir($dh);
			}
		}
	}
	
	/**
	 * Copy the directory ($dir2copy) to the directory ($dir_paste)
	 * 
	 * @param string $dir2copy
	 * @param string $dir_paste
	 */
	public static function copyDirWithoutPhpFiles( $dir2copy, $dir_paste )
	{
		if ( is_dir($dir2copy) )
		{

			if ( $dh = opendir($dir2copy) )
			{     
				while ( ($file = readdir($dh)) !== false )
				{
					if (!is_dir($dir_paste))
					{
						mkdir ($dir_paste, 0777);
					}
					
					if(is_dir($dir2copy.$file) && $file != '..'  && $file != '.')
					{
						self::copyDirWithoutPhpFiles ( $dir2copy.$file.'/' , $dir_paste.$file.'/' ); 
					}
					elseif( $file != '..' &&
							$file != '.' &&
							substr($file, -4, 4) != '.php' &&
							substr($file, -5, 4) != '.php' )
					{
						copy ( $dir2copy.$file , $dir_paste.$file ); 
					}
				}

				closedir($dh);
			}
		}
	}
}
