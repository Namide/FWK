<?php

include_once _TEMPLATE_DIRECTORY.'includes/menu.php';
include_once _TEMPLATE_DIRECTORY.'includes/footer.php';

?>


<!DOCTYPE html>
<html lang="<?php echo TemplateUtils::getInstance()->getLanguage(); ?>">

<head>

    <meta charset="utf-8">
	<title><?php echo TemplateUtils::getInstance()->getCurrentPage()->getTitle(); ?> - FWK</title>
    <meta name="description" content="<?php echo TemplateUtils::getInstance()->getCurrentPage()->getDescription(); ?>" />
	
	
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