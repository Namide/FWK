<?php

include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/zipRep.php';
$rootDir = 'temp-init/';
$zipName = 'contentInit.zip';
if( zipper_repertoire_recursif( $zipName, $rootDir ) )
{
	header( 'Location: '._ROOT_URL.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';
delTree( $rootDir );
exit;