<?php


/*
 *		ADMIN
 */

//$_ADMIN_URL = 'admin';
$_ADMIN_IP = [ '92.103.8.10' ];
$_ADMIN_USERS = [ [ 'Damien', sha1('Damien') ] ];




$timestart = microtime(true);

include_once( 'config.php' );
include_once( $_SYSTEM_DIRECTORY.'admin/start.php' );


if ( $_DEBUG ) echo '<!-- all page php time: ',number_format( microtime(true) - $timestart , 3),'s -->';