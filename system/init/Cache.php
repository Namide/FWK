<?php


class Cache
{
	
	private $pageFile;
	
	function __construct()
	{
		global $_SYSTEM_DIRECTORY;
        include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';
		
		global $_CACHE_DIRECTORY;
        $this->pageFile = $_CACHE_DIRECTORY.Url::getURICacheID().'.cache';
    }
	
	public function isCached()
	{
		return file_exists( $this->pageFile );
	}
	public function echoCache()
	{
		readfile( $this->pageFile );
	}
	
	
	public function isCachable()
	{
		global $_MAX_PAGE_CACHE;
        return $this->getNumPages() < $_MAX_PAGE_CACHE;
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
	public function stopSaveCacheAndEcho()
	{
		global $_SYSTEM_DIRECTORY;
		include_once $_SYSTEM_DIRECTORY.'helpers/TemplateUtils.php';
		$page = TemplateUtils::getInstance()->getCurrentPage();;
		
		
		$pageContent = ob_get_contents();
		ob_end_clean();

		if ( $page->getCachable() ) file_put_contents( $this->pageFile, $pageContent );
		echo $pageContent ;
	}
	
	public function getNumPages()
	{
		global $_CACHE_DIRECTORY;
        return count( glob($_CACHE_DIRECTORY.'*.cache') );
	}
}
