<?php


$body = '<article>';
$body.= "<h1>Sitemap</h1>";
$lang = 'en';
$pageList = PageList::getInstance();


$body .= '<ul>';
foreach( $pageList->getAllPages($lang) as $pageTemp )
{
    $body .= '<li><a href="'.PageUtils::urlPageToAbsoluteUrl( $pageTemp->getUrl() ).'">';
    $body .= $pageTemp->getTitle().'</a></li>';
}
$body .= '</ul>';

$body .= '</article>';
