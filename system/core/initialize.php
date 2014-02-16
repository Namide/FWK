<?php


// IMPORT CLASSES
include_once $_SYSTEM_DIRECTORY.'core/LanguageList.php';
include_once $_SYSTEM_DIRECTORY.'core/Page.php';
include_once $_SYSTEM_DIRECTORY.'core/PageList.php';
include_once $_SYSTEM_DIRECTORY.'helpers/Url.php';

include_once $_SYSTEM_DIRECTORY.'helpers/PageUtils.php';


// INITIALIZE
$language = LanguageList::getInstance();
include_once $_CONTENT_DIRECTORY.'languages.php';

$pageList = PageList::getInstance();
include_once $_CONTENT_DIRECTORY.'pages.php';
$pageList->go();


$pagesInitialised = TRUE;

// HELPERS FOR TEMPLATES
include_once $_SYSTEM_DIRECTORY.'helpers/TemplateUtils.php';

$templateUtils = TemplateUtils::getInstance();

$page = $templateUtils->getCurrentPage();
$pageList->updatePage( $page );
include $_TEMPLATE_DIRECTORY.$page->getTemplate().'.php';

