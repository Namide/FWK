<?php

$language = LanguageList::getInstance();
include_once $_CONTENT_DIRECTORY.'languages.php';

$pageList = PageList::getInstance();
include_once $_CONTENT_DIRECTORY.'pages.php';
$pageList->go();


// HELPERS FOR TEMPLATES
$pagesInitialised = TRUE;	// TemplateUtils crash if $pagesInitialised != TRUE
include_once $_SYSTEM_DIRECTORY.'helpers/TemplateUtils.php';
$templateUtils = TemplateUtils::getInstance();
