<?php


/*
 *		ADMIN
 * 
 * For the release you can delete this file (admin.php) and the repertory (system/admin)
 */


$_ADMIN_IP = [ '127.0.0.1', '::1' ];
$_ADMIN_USERS = [ [ 'Damien', sha1('Damien') ] ];





$timestart = microtime(true);

include_once( 'config.php' );
include_once( $_SYSTEM_DIRECTORY.'admin/start.php' );

if ( $_DEBUG )
{
	echo '<!-- all page php time: ',number_format( microtime(true) - $timestart , 3),'s -->';
}