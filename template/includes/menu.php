<?php

function getMenu()
{
	$templateUtils = TemplateUtils::getInstance();
   
   	if ( $templateUtils->getLanguage() == 'all' ) return '';
	
    
	$lang = $templateUtils->getLanguage();
	$pageList = PageList::getInstance();

	$output = '<ul>';
	foreach( $pageList->getAllPages($lang) as $pageTemp )
	{
		$output .= '<li><a href="'.PageUtils::urlPageToAbsoluteUrl( $pageTemp->getUrl() ).'">';
		$output .= $pageTemp->getTitle().'</a></li>';
	}
	$output .= '</ul>';

    return $output;
}
