<?php


$body = '<article>';
$body.= "<h1>Plan du site</h1>";
$lang = 'fr';
$pageList = PageList::getInstance();


$body .= '<ul>';
foreach( $pageList->getAllPages($lang) as $pageTemp )
{
    $body .= '<li><a href="'.PageUtils::urlPageToAbsoluteUrl( $pageTemp->getUrl() ).'">';
    $body .= $pageTemp->getTitle().'</a></li>';
}
$body .= '</ul>';

$body .= '</article></ul>';
