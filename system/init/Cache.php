<?php


class Cache
{
	
	private $rootDir;
	private $pageFile;
	private $pageContent;
	
	function __construct( $rootDir = '' )
	{
		if ( $rootDir == '' )
		{
			global $_CACHE_DIRECTORY;
			$rootDir = $_CACHE_DIRECTORY.'pages/';
		}
		
		if ( substr( $rootDir, -1, 1 ) != '/' )
		{
			$rootDir .= '/';
		}
		$this->rootDir = $rootDir;
		
		
		global $_SYSTEM_DIRECTORY;
        include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';
		
		
		//global $_CACHE_DIRECTORY;		
		$this->pageFile = /*$_CACHE_DIRECTORY.*/Url::getURICacheID();
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
		//global $_CACHE_DIRECTORY;		
		return file_exists( $this->rootDir.$this->pageFile );
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
		
		//global $_CACHE_DIRECTORY;		
		readfile( $this->rootDir.$this->pageFile );
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
		//global $_CACHE_DIRECTORY;
        return self::getNumPages( $this->rootDir ) < $_MAX_PAGE_CACHE;
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
	 * @param string $file
	 */
	public function writesCache( &$newContent = '', $file = '' )
	{
		
		if ( $newContent == '' )
		{
			$pageContent = $this->pageContent;
		}
		else
		{
			$pageContent = $newContent;
		}
		
		if ( $file == '' )
		{
			//global $_CACHE_DIRECTORY;
			$file = $this->rootDir.$this->pageFile;//$_CACHE_DIRECTORY;
		}
		
		global $_SYSTEM_DIRECTORY;
		include_once $_SYSTEM_DIRECTORY.'helpers/TemplateUtils.php';
		$page = TemplateUtils::getInstance()->getCurrentPage();
		if ( $page->getCachable() )
		{
			$this->writesCacheFile( $pageContent, $file );
		}
	}
	
	/**
	 * 
	 * @param string $pageContent
	 * @param string $fileName
	 */
	public function writesCacheFile( &$pageContent, $fileName = '' )
	{
		if( $fileName == '' )
		{
			$fileName = $this->rootDir.$this->pageFile;
		}
		
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

		$file = $path[0];
		if ( count( explode( ".", $file ) ) > 1 )
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
