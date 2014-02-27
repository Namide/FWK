<?php


class Cache
{
	
	private $pageFile;
	private $pageContent;
	
	function __construct()
	{
		global $_SYSTEM_DIRECTORY;
        include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';
		
		global $_CACHE_DIRECTORY;		
		$this->pageFile = $_CACHE_DIRECTORY.Url::getURICacheID();
		$path = explode( "/", $this->pageFile );
		if ( count( explode( ".", array_pop($path) ) ) < 2 )
		{
			$this->pageFile .= '/index.html';
		}
		
    }
	
	/**
	 * 
	 * @return boolean
	 */
	public function isCached()
	{
		return file_exists( $this->pageFile );
	}
	
	public function echoCache()
	{
		$file_extension = strtolower(substr(strrchr( $this->pageFile ,"."),1));

		switch ($file_extension)
		{
			case "xml":
				header('Content-Type: application/xml;');
				break;
			default: $ctype="application/force-download";
		}
		
		readfile( $this->pageFile );
	}
	
	/**
	 * 
	 * @global int $_MAX_PAGE_CACHE
	 * @global string $_CACHE_DIRECTORY
	 * @return boolean
	 */
	public function isCachable()
	{
		global $_MAX_PAGE_CACHE;
		global $_CACHE_DIRECTORY;
        return self::getNumPages( $_CACHE_DIRECTORY ) < $_MAX_PAGE_CACHE;
	}
	
	public function startSaveCache()
	{
		global $_CACHE_DIRECTORY;
		
        if( !file_exists($_CACHE_DIRECTORY) )
		{
			mkdir( $_CACHE_DIRECTORY, 0777 );
		}
		
		if( !file_exists($_CACHE_DIRECTORY.'.htaccess') )
		{
			$htaccess = fopen( $_CACHE_DIRECTORY.'.htaccess' , "w" );
			$htaccessContent = 'deny from all
<Files ../index.php>
allow from all
</Files>';
			fwrite($htaccess, $htaccessContent);
			fclose($htaccess); 
		}

		ob_start();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getSavedCache()
	{
		return $this->pageContent;
	}
	
	public function stopSaveCache()
	{
		$pageContent = ob_get_contents();
		ob_end_clean();

		
		$this->pageContent = $pageContent;
		return $pageContent;
	}
	
	/**
	 * 
	 * @global string $_SYSTEM_DIRECTORY
	 * @param string $newContent
	 * @param string $dir
	 */
	public function writesCache( &$newContent = '', $dir = '' )
	{
		if ( $newContent == '' )
		{
			$pageContent = $this->pageContent;
		}
		else
		{
			$pageContent = $newContent;
		}
		
		global $_SYSTEM_DIRECTORY;
		include_once $_SYSTEM_DIRECTORY.'helpers/TemplateUtils.php';
		$page = TemplateUtils::getInstance()->getCurrentPage();
		if ( $page->getCachable() )
		{
			$this->writesCacheFile( $pageContent, $dir );
		}
	}
	
	/**
	 * 
	 * @param string $pageContent
	 * @param string $baseDir
	 * @param string $fileName
	 */
	public function writesCacheFile( &$pageContent, $baseDir = '', $fileName = '' )
	{
		if( $fileName == '' ) { $fileName = $this->pageFile; }
		
		$path = explode( '/', $fileName );
		if ( $baseDir != '' )
		{
			$path[0] = $baseDir;
			$file = implode( '/', $path );
		}
		else
		{
			$file = $fileName;
		}
		
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

		$file = $path[0];
		if ( count(explode( ".", $file )) > 1 )
		{
			$file = $dir.$file;
		}
		else
		{
			$file = $dir.'index.html';
		}
		
		file_put_contents( $file, $pageContent );
	}
	
	/**
	 * 
	 * @param string $cacheDirectory
	 * @return int
	 */
	public static function getNumPages( $cacheDirectory )
	{
		$dir = $cacheDirectory;
		if ( substr($dir, -1, 1) === '/' ) $dir = substr($dir, 0, -1);
		return self::getNumPageRecurs($dir);
	}
	
	/**
	 * 
	 * @param string $dir
	 * @return int
	 */
	private static function getNumPageRecurs( $dir )
	{
		$num = 0;
		if ( !file_exists($dir) ) return $num;
		
		$MyDirectory = opendir($dir) or die('Erreur');
		while ( $Entry = @readdir($MyDirectory) )
		{
			if ( is_dir($dir.'/'.$Entry) && $Entry != '.' && $Entry != '..' )
			{
				$num += self::getNumPageRecurs($dir.'/'.$Entry);
			}
			elseif ( substr($Entry, 0, 1) != '.' ) 
			{
				$num++;
			}
		}
		closedir($MyDirectory);
		
		return $num;
	}
}
