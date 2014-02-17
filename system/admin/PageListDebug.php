<?php


class PageListDebug extends PageList
{
	
	
	public function getAnalyse()
	{
		
		global $_DEBUG;
		//global $_ROOT_DIRECTORY;
		global $_CACHE;
		global $_URL_REWRITING;
		global $_MAX_PAGE_CACHE;
		global $_CACHE_DIRECTORY;
		global $_SYSTEM_DIRECTORY;
		
		$pageList = PageList::getInstance();
		$pages = $pageList->pagesByUrl;
		
		
		$results = '<h1>Debug page</h1>';
		
		
		
		$results .= '<h2>Global var</h2>';
		$results .= '<table>';
		$results .= '<tr><th>URL rewriting</th><td>'.(($_URL_REWRITING)?'<span style="color:green;">activated</span>':'<strong style="color:red;">disabled</strong>').'</td></tr>';
		$results .= '<tr><th>Cache</th><td>'.(($_CACHE)?'<span style="color:green;">activated</span>':'<strong style="color:red;">disabled</strong>').'</td></tr>';
		$results .= '<tr><th>Debug mode</th><td>'.(($_DEBUG)?'<strong style="color:red;">activated</strong>':'<span style="color:green;">disabled</span>').'</td></tr>';
		$results .= '</table>';
		
		if ( !$_DEBUG )
		{
			$results .= 'To see this page you must to activate debug mode.';
			return $results;
		}
		
		//$_CACHE = FALSE;
		$results .= '<h2>Global tests</h2>';
		$results .= '<p>The debug mode takes all pages in cache if the cache is activated.<br />';
		$results .= 'To see the real number of the pages in cache you must to reload this page.<br />';
		$results .= 'Pages cached number represent all pages without the uncachables pages.</p>';
		$results .= '<table>';
		$results .= '<tr><th>Pages number</th><td>'.count($pages).'</td></tr>';
		$results .= '<tr><th>Visible pages number</th><td>'.count( $pageList->getAllPages( 'all' ) ).'</td></tr>';
		$results .= '<tr><th>Total weight cache</th><td>';
		if ( file_exists ( $_CACHE_DIRECTORY ) )
		{
			$results .= $this->getFormatedSize( $_CACHE_DIRECTORY, FALSE );
		}
		else
		{
			$results .= '<strong style="color:red;">Cache empty</strong>&nbsp;&nbsp;&nbsp;';
			$results .= '<button onclick="location.reload();">refresh</button>';
		}
		if ( file_exists ( $_CACHE_DIRECTORY ) )
		{
			include_once $_SYSTEM_DIRECTORY.'init/Cache.php';
			
			$color = ( ( $_MAX_PAGE_CACHE - Cache::getNumPages($_CACHE_DIRECTORY) ) < 1 ) ? 'style="color:red;"' : 'style="color:green;"';
			$results .= '<tr><th>Pages cached</th><td><span '.$color.'>'.Cache::getNumPages($_CACHE_DIRECTORY).'/'.$_MAX_PAGE_CACHE.'&nbsp;&nbsp;&nbsp;<button onclick="location.reload();">refresh</button></span></td></tr>';
		}
		
		$results .= '<tr><th>Total weight of content</th><td>'.$this->getFormatedSize( 'content', FALSE ).'</span></td></tr>';
		$results .= '<tr><th>Internal links</th><td><div id="link-checker"><button onclick="processor.go();">check</button></div></td></tr>';
		$results .= '</td></tr>';
		$results .= '</table>';
		
		
		
		
		
		
		$results .= '<h2>Pages</h2>';
		
		$results .= '<table><tr>';
		$results .= '<th>num</th>';
		$results .= '<th>urls</th>';
		$results .= '<th>load</th>';
		$results .= '<th>weight</th>';
		$results .= '<th>links</th>';
		$results .= '</tr>';
		
		
		$i = 0;
		foreach( $pages as $page )
		{
			$results .= '<tr>';
			
			// num
			$results .= '<td>';
			$results .= ++$i;
			$results .= '</td>';
			
			
			// url
			$results .= '<td>';
			$results .= '<a href="'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl()).'" class="checkURL">'.$page->getUrl().'</a><br />';
			$results .= 'content'.'/'.$page->getID().'/';
			$results .= '</td>';
			
			// body
			$results .= '<td>';
			$pageList->updatePage( $page ) or die("Unable to load page $page->getUrl()");;
			$body = $page->getBody();
			$results .= 'OK</td>';
			
			// weight
			$results .= '<td>';
			$results .= $this->getFormatedSize( 'content/'.$page->getId() );
			$results .= '</td>';
			
			
			// links
			/*$pattern = '/([^"]|^)((http|ftp|https):\/\/[\w.?\/=%)(+;&-~#]+)/';
			preg_match($pattern, $body, $links);*/
			
			$regex = "\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))"; // Anchor		
			preg_match_all("%$regex%s",$body, $links);
			$links = $links[0];
			
			$results .= '<td><strong>'.count($links).' links</strong><br />';
			foreach ($links as $link)
			{
				$results .= '<a href="'.$link.'" class="checkURL">'.$link.'</a>';
				//$results .= '<a href="'.$link.'" class="checkURL">'.substr($link, 0, 10).'[...]'.substr($link, -10).'</a>';
				$results .= '<br />';
			}	
			$results .= '</td>';
			
			$results .= '</tr>';
		}
		
		$results .= '</table>';
		
		$results .= '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>';
		//$results .= '<script type="text/javascript" src="'.PageUtils::getRootAbsoluteUrl('system/debug/js/LinkChecker.js').'"></script>';
		$results .= $this->getJsLinkChecker();
		
		$results .= '<script>
			
				// This sneaky bit tries to disable the Same Origin Policy
				/*if (navigator.userAgent.indexOf("Firefox") != -1)
				{
					try
					{
						netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
					} 
					catch (e)
					{
						alert("Permission UniversalBrowserRead denied -- not running Mozilla?");
					}
				}*/

				var errorsNum = 0;
				
				var processor = new LinkChecker.LinkProcessor( document.querySelectorAll("a.checkURL") );
				processor.on(LinkChecker.events.started, function(numberOfLinks) {
					$("div#link-checker").html("Links to check: "+numberOfLinks)
				});
				
				
				processor.on(LinkChecker.events.checked, function(link) {
					
					$("div#link-checker").html( "Internal link checked: " + $(link.elem).html() );

					if(link.broken)
					{
						$(link.elem).addClass("broken-link").css({
							color: "red"
						});
						
						errorsNum++;
					}
					else {
						$(link.elem).css({
							color: "green"
						});
					}
				});
				processor.on(LinkChecker.events.completed, function(link) {
					var char = ( errorsNum < 1 ) ? "<strong style=\"color:green;\" >All urls ok</strong>" : "<strong style=\"color:red;\" >" + errorsNum + " links broken</strong>";
					$("div#link-checker").html( char );
				});
				//processor.go();				
				
				</script>';
		
		
		return $results;
	}
	
	/*protected function getFolderSizeOctets($path)
	{
		$total_size = 0;
		$files = scandir($path);
		
		foreach($files as $t)
		{
			if (is_dir($t))
			{
				if ($t<>"." && $t<>"..")
				{
					$size = foldersize($path . "/" . $t);
					
					$total_size += $size;
				}
			}
			else
			{
				$size = filesize($path . "/" . $t);
				
				$total_size += $size;
			}
		}
		return $total_size;
	}*/
	
	protected function dirSize($directory)
	{
	    $size = 0;
	    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
	    {
	        $size+=$file->getSize();
	    }
	    return $size;
	} 
	
	protected function getFormatedSize( $path , $color = TRUE )
	{
		$size = $this->dirSize($path);
		$round = 2;
		
		//Size must be bytes!
		$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;
		
		
		
		$sizeChar = round($size,$round).' '.$sizes[$i];
		
		if ( !$color ) return $sizeChar;
		
		if ( $i < 2 && $size < 150 ) 		return '<span style="color:green">'.$sizeChar.'</span>';
		else if ( $i > 1 || ($i == 1 && $size > 700) ) return '<strong style="color:red">'.$sizeChar.'</strong>';
		return $sizeChar;
	}
	
	protected function getJsLinkChecker()
	{
		$output = '<script type="text/javascript">
	var LinkChecker = (function( window ) {

    var events;

    /**
     * Encapsulates logic for making Asynchronous HTTP requests
     * @param url
     * @param {object} options options
     * @param {function} [options.success] the success callback
     * @param {function} [options.error] the error callback
     * @param {function} [options.complete] the complete callback
     * @param {string} [options.method="GET"] the HTTP method to utilise
     * @constructor
     */
    function AsyncRequest(url, options) {
        this.url = url;
        this.successCallback = options.success;
        this.errorCallback = options.error;
        this.completeCallback = options.complete;
        this.method = options.method || "GET";
        this.httpRequest = this.getXhr();
    }
    AsyncRequest.prototype = {

        getXhr  : function() {
            var httpRequest;

            if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                httpRequest = new XMLHttpRequest();
            } else if (window.ActiveXObject) { // IE
                try {
                    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch (e) {
                    try {
                        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    catch (e) {}
                }
            }

            return httpRequest;
        },

        request : function() {
            var httpRequest = this.httpRequest,
                self = this;

            if (!httpRequest) {
                return false;
            }
            httpRequest.onreadystatechange = function () {
                if (httpRequest.readyState === 4) {
                    if (httpRequest.status === 200) {
                        self.successCallback && self.successCallback(httpRequest);
                    } else {
                        self.errorCallback && self.errorCallback(httpRequest);
                    }

                    self.completeCallback && self.completeCallback(httpRequest);
                }
            };
            httpRequest.open(this.method, this.url);
            httpRequest.send();
        }
    };

    /**
     * Offers functionality for some basic Pub/Sub
     * @param {object} [target] supply a target for mixin approach
     * @constructor
     */
    function PubSub(target) {
        var self = target || this;

        self.subscriptions = {};

        self.on = function(topic, action, context) {
            if(!self.subscriptions[topic]) {
                self.subscriptions[topic] = [];
            }
            self.subscriptions[topic].push({
                action : action, context: context
            });
        };

        self.off = function(topic) {
            self.subscriptions[topic] = null;
        };

        self.fire = function(topic, params) {
            var i = 0,
                subs = self.subscriptions[topic];

            if(!subs) {
                return;
            }

            for(i; i < subs.length; i++) {
                if(subs[i].context) {
                    subs[i].action.call(subs[i].context, params);
                }
                else {
                    subs[i].action(params);
                }
            }
        };
    }

    /**
     * The events we re exposing
     */
    events = {
        started : "started.linkChecker",
        checked : "checked.linkChecker",
        completed : "completed.linkChecker"
    };

    function Link(elem) {
        this.elem = elem;
        this.getUri();
        this.broken = null;
    }
    Link.prototype = {

        /**
         * gets the uri that the element refers to.
         * currently only supports images and anchors
         * @returns {string} the uri if possible, null otherwise
         */
        getUri : function() {
            var uri;
            switch (this.elem.tagName.toLowerCase()) {
                case "a" :
                    uri = this.elem.getAttribute("href");
                    break;
                case "img" :
                    uri = this.elem.getAttribute("src");
                    break;
            }

            this.uri =  uri;
        },

        /**
         * checks whether the element is pointing to a local resource
         * @returns {boolean} true if local, false otherwise
         */
        isLocal : function() {

            var loc = window.location,
                a = document.createElement("a"),
                isLocal;

            a.href = this.uri ;

            return a.hostname === loc.hostname 
                && a.port === loc.port
                && a.protocol === loc.protocol;
        },

        /**
         * makes AJAX request to url
         * @param {function} callback called when request is complete. Called in context of current object
         */
        check : function(callback) {
            var self = this,
                async,
                targetId;

            if(self.uri.match(/^#/)) {
                targetId = self.uri.replace("#", "");
                self.broken = window.document.getElementById(targetId) === null;
                callback.call(self);
            }
            else {

                async = new AsyncRequest(self.uri, {
                    method : "HEAD",
                    complete : function(httpRequest) {
                        self.broken = httpRequest.status === 404;
                        callback.call(self);
                    }
                });
                async.request();
            }
        }
    };

    /**
     * Handles batch processing of links
     * @param {NodeList} elems the elements to process
     * @constructor
     */
    function LinkProcessor(elems) {
        this.progress = [];
        this.toProcess = [];
        this.itemsProcessed = 0;

        //inherit from PubSub
        PubSub.call(this);

        //create process list of all local links
        var link, length = elems.length;
        for(var i = 0; i < length; i++) {
            link = new Link(elems[i]);
            if( link.isLocal() ) {
                this.toProcess.push(link);
            }
			else
			{
				$(link.elem).addClass("broken-link").css({
							color: "blue"/*,
							"font-weight" : "bold"*/
						});
			}
        }
    }
    LinkProcessor.prototype = {

        /**
         * mark supplied link as already processed
         * @param {Link} link the link to mark
         */
        markProcessed : function (link) {
            this.progress[link.uri] = link;
        },

        /**
         * checks whether processing has completed (i.e. all requests have returned)
         * @return {Boolean} true if processing is complete, false otherwise
         */
        isComplete : function() {
            return this.itemsProcessed === this.toProcess.length;
        },

        /**
         * checks whether the supplied link has already been processed
         * @param {Link} link the link to check
         * @return {Boolean} truth-y value if already processed, false-y otherwise
         */
        alreadyProcessed : function (link) {
            return this.progress[link.uri];
        },

        /**
         * Helper method for handling a link having been checked
         * @param {Link} link the link which has been checked
         */
        handleLinkChecked : function(link) {
            this.itemsProcessed++;
            this.fire(events.checked, link);

            if(this.isComplete()) {
                this.fire(events.completed);
            }
        },

        /**
         * Kicks off processing of links
         */
        go : function () {
            var link, i,
                self = this;

            self.fire(events.started, this.toProcess.length);

            for(i = 0 ; i < self.toProcess.length; i++) {

                link = self.toProcess[i];

                if(this.alreadyProcessed(link)) {
                    self.handleLinkChecked(link);
                }
                else {
                    self.markProcessed(link);
                    link.check(function() {
                        self.handleLinkChecked(this);
                    });
                }
            }
        }
    };

    return {
        LinkProcessor : LinkProcessor,
        Link : Link,
        events : events
    };

}(window));
</script>';
		return $output;
	}


	/*protected function checkDeadLink ($url)
	{
		$a = @get_headers($url);
		if ($a)
		{
			//*** On a retour : on test le header HTTP
			if (strstr($a[0],'404'))
			{
				return FALSE; // Erreur 404	
			}
			else
			{
				return TRUE; // OK
			}
		}
		else
		{
			return FALSE; // Erreur accès au site
		}
	}*/
	
}
