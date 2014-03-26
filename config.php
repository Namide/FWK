<?php

/*
 *		URL AND REPERTORIES
 */

// Absolute URL of the website
define( '_ROOT_URL', 'http://localhost:80/FWK/' );

// Directory name of the system's files
// It is better to be outside of the www directory
define( '_SYSTEM_DIRECTORY', 'system/' );

// Directory name of the content's files
define( '_CONTENT_DIRECTORY', 'content/' );

// Directory name of the template's files
define( '_TEMPLATE_DIRECTORY', 'template/' );

// Directory name of the cache's files
// It is better to be outside of the www directory
define( '_CACHE_DIRECTORY', 'cache/' );



/*
 *		PARAMETERS
 */

// URL rewriting activated => TRUE, deactivated => FALSE
define( '_URL_REWRITING', FALSE );

// Debug mode activated => TRUE, deactivated => FALSE
define( '_DEBUG', TRUE );

// Cache activated => TRUE, deactivated => FALSE
define( '_CACHE', FALSE );

// Maximum number of files in the cache directory
// It is better to have 5 beyond the maximum number of cachable pages
define( '_MAX_PAGE_CACHE', 50 );


