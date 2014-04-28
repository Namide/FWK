<?php

function getFooter()
{
    //global $templateUtils;
    global $page;
    
	$lang = BuildUtil::getInstance()->getLang();
    
    $output = '<ul>';
    
    if( $lang == 'all' )
    {
    	$output .= '<li><a href="'.InitUtil::getInstance()->getAbsUrlByIdLang( 'basic/homepage', 'en' ).'">en</a></li>';
    	$output .= '<li><a href="'.InitUtil::getInstance()->getAbsUrlByIdLang( 'basic/homepage', 'fr' ).'">fr</a></li>';
    }
    else if ( $lang == 'fr' )
    {
    	$output .= '<li><a href="'.InitUtil::getInstance()->getAbsUrlByIdLang( $page->getId(), 'en' ).'">en</a></li>';
    }
    else
    {
    	$output .= '<li><a href="'.InitUtil::getInstance()->getAbsUrlByIdLang( $page->getId(), 'fr' ).'">fr</a></li>';
    }
    
    $output .= '</ul>';
    return $output;
}