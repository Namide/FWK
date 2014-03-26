<?php


/*
 *		ADMIN
 */


$_ADMIN_IP = [ '127.0.0.1', '::1' ];
$_ADMIN_USERS = [ [ 'Damien', sha1('Damien') ] ];




$timestart = microtime(true);

include_once( 'config.php' );
include_once( _SYSTEM_DIRECTORY.'plugin/admin/start.php' );


if ( _DEBUG ) echo '<!-- all page php time: ', number_format( microtime(true) - $timestart , 3), 's -->';