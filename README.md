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
`content/languages.php`

```php
$language->addDefaultLanguage('en');
$language->addLanguage('fr');
```

##### list of pages
`content/pages.php`

```php
$pageList->addDefaultPage( 'basic/homepage' );
$pageList->addError404Page( 'basic/error404' );
$pageList->addPage( 'basic/sitemap' );
```


Configure page
------------------------

##### initialisation
`content/your-page/{language}-init.php`

```php
$url
$template
$visible:boolean
$cachable:boolean
$title
$tags:array
$phpHeader

$header
$preface
$requestsInit:array
```

##### content
`content/your-page/{language}-build.php`

Content of the page in `HTML`

```php
$requestsBuild:array
```


Internals URL
------------------------

Used in the build page `content/your-page/{language}-build.php`

```php
{{urlPageToAbsoluteUrl:en/post/min-max}}
{{idPageToAbsoluteUrl:basic/homepage}}
{{pathTemplate:css/alternative-slideshow.css}}
{{pathContent:img/test.jpg}}
{{pathCurrentPage:img/test.jpg}}
```
