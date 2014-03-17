FWK
=========

Lightweight framework PHP without database.

- Quick to install
- No database
- Cache Management
- Simple and permissive urls
- Lightweight


Initialize
------------------------

##### list of languages
`fwk-content/languages.php`

```php
$language->addDefaultLanguage('en');
$language->addLanguage('fr');
```

##### list of pages
`fwk-content/pages.php`

```php
$pageList->addDefaultPage( 'basic/homepage' );
$pageList->addError404Page( 'basic/error404' );
$pageList->addPage( 'basic/sitemap' );
```


Configure page
------------------------

##### initialisation
`{language}-init.php`

```php
$url
$template
$visible:boolean
$cachable:boolean
$title
$categories:array
$phpHeader

$header
$preface
$requestsInit:array
```

##### content
`{language}-build.php`

```php
$body
$requestsBuild:array
```


Internals URL
------------------------

```php
{{urlPageToAbsoluteUrl:en/post/min-max}}
{{idPageToAbsoluteUrl:basic/homepage}}
{{pathTemplate:css/alternative-slideshow.css}}
{{pathCurrentPage:img/test.jpg}}
```
