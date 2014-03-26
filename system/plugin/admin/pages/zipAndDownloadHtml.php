<?php

global $_CACHE_DIRECTORY;
global $_CONTENT_DIRECTORY;
global $_SYSTEM_DIRECTORY;
global $_ROOT_URL;

include_once $_SYSTEM_DIRECTORY.'plugin/admin/pages/includes/zipRep.php';
include_once $_SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';

$zipName = 'html.zip';

cleanDirRecurs( $_CACHE_DIRECTORY.'standalone-html' );
if( zipper_repertoire_recursif( $zipName, $_CACHE_DIRECTORY.'standalone-html', '' ) )
{
	delTree( $_CACHE_DIRECTORY.'standalone-html' );
	header( 'Location: '.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
exit;