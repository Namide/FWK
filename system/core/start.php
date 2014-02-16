<?php

include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';

if ( $_CACHE )
{
	
	
	$cacheFile = $_CACHE_DIRECTORY.Url::getURICacheID().'.html';
	
	
	if( file_exists( $cacheFile ) )
	{
		readfile( $cacheFile );
		exit();
	}
	else
	{
		if( !file_exists($_CACHE_DIRECTORY) )
		{
			mkdir( $_CACHE_DIRECTORY, 0777 );
			
			$htaccess = fopen( $_CACHE_DIRECTORY.'.htaccess' , "w" );
			$htaccessContent = 'deny from all
<Files ../index.php>
allow from all
</Files>';
			fwrite($htaccess, $htaccessContent);
			fclose($htaccess); 
			
		}
		
		if( count( glob($_CACHE_DIRECTORY.'*.html') ) < $_MAX_PAGE_CACHE )
		{
			ob_start();

			include_once( $_SYSTEM_DIRECTORY.'core/initialize.php' ); 

			
			$pageContent = ob_get_contents(); // copie du contenu du tampon dans une chaîne
			ob_end_clean(); // effacement du contenu du tampon et arrêt de son fonctionnement

			if ( /*$_CACHE &&*/ $page->getCachable() /*$page->getType() != Page::$TYPE_ERROR_404*/ ) file_put_contents( $cacheFile, $pageContent ); // on écrit la chaîne précédemment récupérée ($page) dans un fichier ($cache) 
			echo $pageContent ; // on affiche notre page :D 
			
			if ( $_DEBUG ) echo '<!-- first load php time: ',number_format( microtime(true) - $timestart , 3),'s -->';
			exit();
		}
		
	}
	
	
}

include_once( $_SYSTEM_DIRECTORY.'core/initialize.php' );
if ( $_DEBUG ) echo '<!-- first load php time: ',number_format( microtime(true) - $timestart , 3),'s -->';
