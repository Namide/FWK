<?php

function getMenu()
{
	$templateUtils = BuildUtil::getInstance();
   
   	if ( $templateUtils->getLang() == 'all' ) return '';
	
    
	$lang = $templateUtils->getLang();
	$pageList = PageList::getInstance();

	$output = '<ul>';
	foreach( $pageList->getAllPages($lang) as $pageTemp )
	{
		$output .= '<li><a href="'.InitUtil::getInstance()->urlPageToAbsUrl( $pageTemp->getUrl() ).'">';
		$output .= $pageTemp->getTitle().'</a></li>';
	}
	$output .= '</ul>';

    return $output;
}
