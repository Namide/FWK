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
// Load the object for add your pages
$pageList = PageList::getInstance();

// Simple pages
$pageList->addDefaultPage( 'homepage' );
$pageList->addError404Page( 'error404' );
$pageList->addPage( 'sitemap' );

// Dynamic pages, they use a simple page
$pageList->addDynamicPages( 'homepage', ['home-alternative-1'], ['fr'], ['test french'] );
$pageList->addDynamicPage( 'homepage', 'home-alternative-2', 'en', 'test english' );
```


Configure page
------------------------

##### initialization
`content/homepage/{language}-init.php`

```php
// URL
$url = 'en/homepage';

// title of the page
$title = 'Homepage';

// description of the page
$description = 'FWK is a really fun framework!';

// Name of the template
$template = 'default';

// Additional tags in the head (like CSS, JS, meta...)
$header = '	<meta name="robots" content="all" />';

// Is the page visible ? (in the sitemap...)
$visible = TRUE;

// Is the page cachable ? (dynamics page aren't cachable)
$cachable = TRUE;

// Add tags to the page
//$tags = array( 'home', 'info' );

// Arguments to the php function header() of the page (for other type than HTML, like XML)
//$phpHeader = 'Content-Type: application/xml; charset=utf-8';

// Additional contents accessible (from other pages or with the template)
//$contents = array( 'resume'=>'The homepage is a [...]' );

// Used for an additional URL of page loaded by XMLHttpRequest
// new RequestPage( $url, $cachable = FALSE );
//$requests = array( new RequestPage( 'request/test01', TRUE ) );
```

##### content
`content/homepage/{language}-build.php`

```php
// If your page is dynamic you recover the velue object here
// $dynamicData = (isset($vo)?$vo:'FWK');

// Used for an additional content of page loaded by XMLHttpRequest
//$requestsContent = array( 'request/test01' => 'First AJAX content' );
```

```html
<article>
	<h1>Welcome on <?=$dynamicData?></h1>
	<p>It's you home page.</p>
	<img width="" height="" src="{{pathCurrentPage:img/example.png}}" alt="image example">
</article>
```

Internals URL
------------------------

Used in the build page `content/homepage/{language}-build.php`

```php
{{urlPageToAbsoluteUrl:en/post/min-max}}
{{idPageToAbsoluteUrl:basic/homepage}}
{{pathTemplate:css/alternative-slideshow.css}}
{{pathContent:img/test.jpg}}
{{pathCurrentPage:img/test.jpg}}
```
