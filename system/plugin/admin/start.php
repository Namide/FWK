<?php

if ( $_DEBUG ) error_reporting(E_ALL);

include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';
if ( isset( $_GET[Url::getPageGetArg()] ) ) $_GET[Url::getPageGetArg()] = 'admin';



include_once $_SYSTEM_DIRECTORY.'init/imports.php';
include_once $_SYSTEM_DIRECTORY.'init/loadPages.php';
include_once $_SYSTEM_DIRECTORY.'init/buildPage.php';
//include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';

include_once $_SYSTEM_DIRECTORY.'plugin/admin/Login.php';
$login = new Login();

include_once $_SYSTEM_DIRECTORY.'plugin/admin/template/default.php';

if ( $_DEBUG ) echo '<!-- execute PHP time: ',number_format( microtime(true) - $timestart , 3),'s -->';
