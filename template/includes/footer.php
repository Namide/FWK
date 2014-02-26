<?php

function getFooter()
{
    //global $templateUtils;
    global $page;
    
	$lang = TemplateUtils::getInstance()->getLanguage();
    
    $output = '<ul>';
    
    if( $lang == 'all' )
    {
    	$output .= '<li><a href="'.PageUtils::getAbsoluteUrl( 'basic/homepage', 'en' ).'">en</a></li>';
    	$output .= '<li><a href="'.PageUtils::getAbsoluteUrl( 'basic/homepage', 'fr' ).'">fr</a></li>';
    }
    else if ( $lang == 'fr' )
    {
    	$output .= '<li><a href="'.PageUtils::getAbsoluteUrl( $page->getId(), 'en' ).'">en</a></li>';
    }
    else
    {
    	$output .= '<li><a href="'.PageUtils::getAbsoluteUrl( $page->getId(), 'fr' ).'">fr</a></li>';
    }
    
    $output .= '</ul>';
    return $output;
}