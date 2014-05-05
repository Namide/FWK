<?php

include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/zipRep.php';
include_once _SYSTEM_DIRECTORY.'helpers/FileUtil.php';

$zipName = _TEMP_DIRECTORY.'content.zip';
FileUtil::writeDirOfFile($zipName);
if( zipper_repertoire_recursif( $zipName, _CONTENT_DIRECTORY ) )
{
	header( 'Location: '._ROOT_URL.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
exit;