<?php

	include_once _TEMPLATE_DIRECTORY.'includes/menu.php';
	include_once _TEMPLATE_DIRECTORY.'includes/footer.php';

?><!DOCTYPE html>
<html lang="<?php echo BuildUtil::getInstance()->getLang(); ?>">

<head>

    <meta charset="utf-8">
	<title><?php echo BuildUtil::getInstance()->getCurrentPage()->getTitle(); ?> - FWK</title>
    <meta name="description" content="<?php echo BuildUtil::getInstance()->getCurrentPage()->getDescription(); ?>" />
	
	
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
	
	<meta name="author" content="Namide" />

	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo InitUtil::getInstance()->getTemplateAbsUrl( 'css/default.css' ); ?>" />
    <link rel="stylesheet" media="print" type="text/css" href="<?php echo InitUtil::getInstance()->getTemplateAbsUrl( 'css/print.css' ); ?>" />
	
    <link rel="icon" type="image/png" href="<?php echo InitUtil::getInstance()->getTemplateAbsUrl( 'img/favicon.png' ); ?>" /> 
    
    
	
    <?php echo $page->getHeaderFinal(); ?>
    
</head>

<body>
    
    <header>
        <h1><a href="<?php echo BuildUtil::getInstance()->getAbsUrl( 'basic/homepage' ); ?>">FWK</a></h1>
        <nav>
            <?php echo getMenu(); ?>
        </nav>
    </header>
    
    <div id="content">
    
        <?php
	        echo BuildUtil::getInstance()->getCurrentPage()->getBodyFinal();
        ?>
		
    </div>
    
    <footer>
        <?php echo getFooter(); ?>
    </footer>
	
</body>

</html>