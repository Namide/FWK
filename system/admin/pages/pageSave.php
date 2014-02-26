<?php

global $_CONTENT_DIRECTORY;
global $_SYSTEM_DIRECTORY;
global $_ROOT_URL;

include_once $_SYSTEM_DIRECTORY.'admin/pages/includes/zipRep.php';

$zipName = 'content.zip';

if( zipper_repertoire_recursif( $zipName, $_CONTENT_DIRECTORY ) )
{
	header( 'Location: '.$_ROOT_URL.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
exit;