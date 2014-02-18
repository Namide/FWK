<!DOCTYPE html>
<html lang="<?php echo TemplateUtils::getInstance()->getLanguage(); ?>">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="You" />

	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo PageUtils::getTemplateAbsoluteUrl( 'css/default.css' ); ?>" />
    <link rel="stylesheet" media="print" type="text/css" href="<?php echo PageUtils::getTemplateAbsoluteUrl( 'css/print.css' ); ?>" />
	
    <link rel="icon" type="image/png" href="<?php echo PageUtils::getTemplateAbsoluteUrl( 'img/favicon.png' ); ?>" /> 
    
    <meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
	
    <?php echo $page->getHeader(); ?>
    
</head>

<body>
    
    <header>
        <h1><a href="<?php echo TemplateUtils::getInstance()->getAbsoluteUrl( 'basic/homepage' ); ?>">FWK</a></h1>
        <nav>
            <?php echo getMenu(); ?>
        </nav>
    </header>
    
    <div id="content">
    
        <?php
	        echo TemplateUtils::getInstance()->getCurrentPage()->getBody();
        ?>
		
    </div>
    
    <footer>
        <?php echo getFooter(); ?>
    </footer>
	
</body>

</html>


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

?>