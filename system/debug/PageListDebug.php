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
		
		$_CACHE = FALSE;
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
			$results .= '<strong style="color:red;">Cache unactive</strong>';
		}
		if ( file_exists ( $_CACHE_DIRECTORY ) )
		{
			$color = ( ( $_MAX_PAGE_CACHE - count(glob($_CACHE_DIRECTORY.'*.html')) ) < 1 ) ? 'style="color:red;"' : 'style="color:green;"';
			$results .= '<tr><th>Pages cached</th><td><span '.$color.'>'.count(glob($_CACHE_DIRECTORY.'*.html')).'/'.$_MAX_PAGE_CACHE.'&nbsp;&nbsp;&nbsp;<button onclick="location.reload();">refresh</button></span></td></tr>';
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
		$results .= '<script type="text/javascript" src="'.PageUtils::getRootAbsoluteUrl('system/debug/js/LinkChecker.js').'"></script>';
		
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
