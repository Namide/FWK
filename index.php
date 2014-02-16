<?php

$timestart = microtime(true);


include_once( 'config.php' );
include_once( $_SYSTEM_DIRECTORY.'core/start.php' );


if ( $_DEBUG ) echo '<!-- all page php time: ',number_format( microtime(true) - $timestart , 3),'s -->';