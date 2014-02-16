FWK
=========

Lightweight framework PHP without database.



Initialize list of `languages` ( fwk-content/languages.php )
------------------------

> $language->addDefaultLanguage('en');
> $language->addLanguage('fr');



Initialize list of `pages` ( fwk-content/pages.php )
--------------------

> $pageList->addDefaultPage( 'basic/homepage' );
> $pageList->addError404Page( 'basic/error404' );
> 
> $pageList->addPage( 'basic/sitemap' );
> $pageList->addPage( 'basic/debug' );



Initialize `page` ( fwk-content/languages.php )
--------------------

List of `properties` to initialisation:

> $url
> $template
> $visible:Bool
> $cachable:Bool
> $title
> $categories:Array
> 
> $header
> $preface


Content `Property`:

> $body

To use internals URL:

> {{urlPageToAbsoluteUrl:en/post/min-max}}
> {{idPageToAbsoluteUrl:basic/homepage}}
> {{pathTemplate:css/alternative-slideshow.css}}
> {{pathCurrentPage:img/test.jpg}}

