<?php

global $_CONTENT_DIRECTORY;
global $_SYSTEM_DIRECTORY;
global $_ROOT_URL;

include_once $_SYSTEM_DIRECTORY.'admin/pages/includes/zipRep.php';
include_once $_SYSTEM_DIRECTORY.'admin/pages/includes/helpers.php';

$zipName = 'html.zip';

if( zipper_repertoire_recursif( $zipName, 'html' ) )
{
	delTree( 'html' );
	header( 'Location: '.$_ROOT_URL.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
exit;