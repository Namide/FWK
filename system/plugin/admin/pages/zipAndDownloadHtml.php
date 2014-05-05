<?php

include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/zipRep.php';
include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';
include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/htmlGenerator.php';

$standaloneDir = _TEMP_DIRECTORY.'standalone-html';
$pages = PageList::getInstance()->getPagesByUrl();
generateHtml( $pages, $standaloneDir );
		
$zipName = _TEMP_DIRECTORY.'html.zip';

FileUtil::delEmptyDirRecursively( $standaloneDir );
if( zipper_repertoire_recursif( $zipName, $standaloneDir, '' ) )
{
	FileUtil::delDirRecursively( $standaloneDir );
	header( 'Location: '.$zipName );
}
else
{
	echo 'Error: Can\'t create the ZIP file';
}
exit;
