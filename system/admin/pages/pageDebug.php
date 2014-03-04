<?php

	include_once $_SYSTEM_DIRECTORY.'admin/pages/includes/helpers.php';

	$ACTUAL_PAGE_URL = 'admin.php?p=page-debug';

	$pageList = PageList::getInstance();
	$pagesDebugPage = $pageList->getPagesByUrl();
	
	if( !empty($_POST['standaloneHtml']) && $_POST['standaloneHtml'] === 'ALL' )
	{
		include_once $_SYSTEM_DIRECTORY.'admin/pages/includes/htmlGenerator.php';
		generateHtml( $pagesDebugPage );
		echo '<script>window.location.href = "admin.php?p=page-html-save";</script>';
	}
	
	if( !empty($_POST['clear']) && $_POST['clear'] === 'ALL' )
	{
		if ( is_dir($_CACHE_DIRECTORY) ) { delTree( $_CACHE_DIRECTORY ); }
		echo '<script>window.location.href = "'.$ACTUAL_PAGE_URL.'";</script>';
	}

			
?>

<h1>Debug page</h1>

<table>
	<caption><h2>Parameters</h2></caption>
	<tr>
		<th>URL rewriting</th>
		<td><?php echo (($_URL_REWRITING)?'<span style="color:green;">activated</span>':'<strong style="color:red;">disabled</strong>') ?></td>
	</tr>
	<tr>
		<th>Cache</th>
		<td>
			<?php echo (($_CACHE)?'<span style="color:green;">activated</span>':'<strong style="color:red;">disabled</strong>') ?>
		</td>
	</tr>
	<tr>
		<th>Debug mode</th>
		<td><?php echo (($_DEBUG)?'<strong style="color:red;">activated</strong>':'<span style="color:green;">disabled</span>') ?></td>
	</tr>
</table>



<table>
	
	<caption>
		<h2>Cache</h2>
		<p>The debug mode takes all pages in cache if the cache is activated.
		To see the real number of the pages in cache you must to reload this page.
		Pages cached number represent all pages without the uncachables pages.</p>
	</caption>
	
	<tr>
		<th>Total weight cache</th>
		<td>
			<?php
				if ( file_exists ( $_CACHE_DIRECTORY ) )
				{
					echo getFormatedSize( $_CACHE_DIRECTORY, FALSE );
				}
				else
				{
					echo '<strong style="color:red;">Cache empty</strong>&nbsp;&nbsp;&nbsp;';
					echo '<button onclick="location.reload();">refresh</button>';
				}
			?>
			&nbsp;
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" style="display:inline;">
				<input type="hidden" name="clear" value="ALL" />
				<input type="submit" value="Clear all" style="color:red;" /> 
			</form>
		</td>
	</tr>
	
	<?php		
		if ( file_exists ( $_CACHE_DIRECTORY ) )
		{
			include_once $_SYSTEM_DIRECTORY.'init/Cache.php';

			$color = ( ( $_MAX_PAGE_CACHE - Cache::getNumPages($_CACHE_DIRECTORY) ) < 1 ) ? 'style="color:red;"' : 'style="color:green;"';
			echo '<tr><th>Pages cached</th><td><span '.$color.'>'.Cache::getNumPages($_CACHE_DIRECTORY).'/'.$_MAX_PAGE_CACHE.'&nbsp;&nbsp;&nbsp;<button onclick="location.reload();">refresh</button></span></td></tr>';
		}
	?>
	
	<tr>
		<th style="color:red;">Standalone HTML version<br/>
			!Don't work with GET and dynamics pages</th>
		<td><form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" style="display:inline;">
				<input type="hidden" name="standaloneHtml" value="ALL" />
				<input type="submit" value="Generate" /> 
			</form></td>
	</tr>
	
</table>



<table>
	
	<caption><h2>Pages - global</h2></caption>
	
	<tr>
		<th>Visible pages number</th>
		<td><?php echo count( $pageList->getAllPages( 'all' ) ); ?> / <?php echo count($pagesDebugPage); ?></td>
	</tr>
	<tr>
		<th>Internal links</th>
		<td><div id="link-checker"><button onclick="processor.go();">check</button></div></td>
	</tr>
	<tr>
		<th>Total weight of content</th>
		<td><?php echo getFormatedSize( 'content', FALSE ); ?></span></td>
	</tr>
	<tr>
		<th>SEO</th>
		<td><div id="seo-test"><button onclick="seoTest.start();">SEO test</button></div></td>
	</tr>
	
</table>



<table>
	<caption><h2>Pages - details</h2></caption>
	
	<tr>
		<th>num</th>
		<th>urls</th>
		<th>load</th>
		<th>weight</th>
		<th>links</th>
		<th>SEO</th>
	</tr>

<?php

	$i = 0;
	$seoList = '[';
	foreach( $pagesDebugPage as $page )
	{
		if( $i > 0 ) { $seoList .= ', '; }
?>
	<tr>

		<!-- num -->
		<td>
			<?php echo ++$i; ?>
		</td>


		<!-- url -->
		<td>
			<a href="<?php echo PageUtils::urlPageToAbsoluteUrl( $page->getUrl()); ?>" class="checkURL"><?php echo $page->getUrl(); ?></a><br />
			<?php echo 'content'.'/'.$page->getID().'/'; ?>
		</td>

		<!-- body -->
		<td>
			<?php $pageList->updatePage( $page ) or die("Unable to load page $page->getUrl()"); ?>
			<?php $body = $page->getBody(); ?>OK
		</td>

		<!-- weight -->
		<td>
			<?php echo getFormatedSize( 'content/'.$page->getId() ); ?>
		</td>

		<!-- links -->
		<?php
			$regex = "\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))"; // Anchor		
			preg_match_all("%$regex%s",$body, $links);
			$links = $links[0];
		?>

		<td>
			<strong><?php count($links); ?> links</strong><br />
			<?php 
				foreach ($links as $link)
				{
					echo '<a href="' , $link, '" class="checkURL">', $link, '</a>';
					echo '<br />';
				}
			?>
		</td>

		<td id="seo<?php echo $i; ?>">
			<?php 
				$seoList .= '{ url:"'.PageUtils::urlPageToAbsoluteUrl( $page->getUrl()).'", id:"seo'.$i.'" }';
			?>
		</td>
		
	</tr>
		
<?php

	}
	$seoList .= ']';
?>

</table>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
<script type="text/javascript"><?php include $_SYSTEM_DIRECTORY.'admin/pages/includes/linkChecker.js'; ?></script>


<script type="text/javascript" >
	
	function SeoTest( pList )
	{
		this.id = 0;
		this.list = pList;
		this.xmlhttp;
	}
	SeoTest.prototype =
	{
		start:function()
		{
			document.getElementById("seo-test").innerHTML = 'started';
			seoTest.testPage();
		},
		
		testPage:function() 
		{
			document.getElementById( seoTest.list[seoTest.id]["id"] ).innerHTML = "pending";//xmlhttp.responseText;
				
			seoTest.xmlhttp = new XMLHttpRequest();
			seoTest.xmlhttp.open( "GET", seoTest.list[seoTest.id]["url"], true );
			seoTest.xmlhttp.onreadystatechange = function()
			{
				//alert( seoTest.id + " " + (seoTest.id < seoList.length) );
				if ( seoTest.xmlhttp.readyState==4 && seoTest.xmlhttp.status==200 )
				{
					var pageContent = seoTest.xmlhttp.responseText;
					if ( pageContent == undefined ) pageContent = string(seoTest.xmlhttp.responseXML);
					document.getElementById( seoTest.list[seoTest.id]["id"] ).innerHTML = seoTest.analysePage( pageContent );
					seoTest.id++;
					if ( seoTest.id < seoTest.list.length )
					{
						seoTest.testPage();
					}
				}
			}
			seoTest.xmlhttp.send();
		},
		
		analysePage:function( page )
		{
			var headTitle = seoTest.getTagContent( page, "<title>", "</title>" );
			var contentBrut = seoTest.getBrutContent( page );
			var metaDescription = seoTest.getMetaDescription( page );
			
			var valid = (headTitle.length>0 && headTitle.length<65);
			var resume = seoTest.getSpanText( "title: " + headTitle.length + " char", valid, " ( < 65 )" );
			
			valid = (contentBrut.length>300 && contentBrut.length<500);	
			resume += seoTest.getSpanText( "content: " + contentBrut.length + " char", valid, " ( 300 < char < 500 )" );
			
			valid = (metaDescription.length>0 && metaDescription.length<150);	
			resume += seoTest.getSpanText( "meta-description: " + metaDescription.length + " char", valid, " ( char < 150 )" );
			
			return resume;
			//return page.getElementsByTagName("title")[0].childNodes[0].nodeValue.length;
			//return page.length();//"a";//page.querySelector('head title').toString();
		},
		
		getMetaDescription:function( text )
		{
			var a = text.split("<meta");
			for ( var i = 0; i < a.length; i++ )
			{
				var b = a[i].split("name=\"description")
				if ( b.length > 1 )
				{
					var c = a[i].split("content=\"");
					if( c.length > 1 )
					{
						return c[1].split("\"")[0];
					}
				}
			}
			return "";
		},
		
		getSpanText:function( text, valid, error )
		{
			return '<span style="color:'+((valid)?"green":"red")+'">' + text + ((valid)?"":error) + '<br />';
		},
				
		/*getElement:function( text, tag, attributeName = "", attributeValue = "" )
		{
			var parser = new DOMParser();
			var xml = parser.parseFromString( text, 'text/xml' );
			var racine = xml.documentElement;
			var tagnom = racine.getElementsByTagName( tag )
			for(var i = 0; i< tagnom.length; i++){
				var element = racine.childNodes[i];
				
			}
			alert(tagnom.length);
			return "";
		},*/
				
		getTagContent:function( text, tag1, tag2 )
		{
			var a = text.split(tag1);
			if( a.length > 1 )
			{
				a = a[1].split(tag2);
				if( a.length > 1 )
				{
					return a[0];
				}
			}
			return "";
		},
		
		getBrutContent:function( text )
		{
			var t = text;
			var a = text.split("<body>");
			if ( a.length > 1 )
			{
				a.shift();
				t = a[0];
			}
			
			a = t.split("<");
			for ( var i = 0; i< a.length; i++ )
			{
				var b = a[i].split(">");
				if( b.length == 2 && b[1] != "" ) a[i] = b[1];
				else a[i] = "";
			}
			t = a.join("");
			
			return String(t);
		}
			
	};
	var seoTest = new SeoTest( <?php echo $seoList; ?> );
	
	/*function runSeo()
	{
		document.getElementById("seo-test").innerHTML = 'started';
		
		var seoList = <?php echo $seoList; ?>;
		//if ( 0 < seoList.length ) testSeo( 0, seoList );
		for ( var i=0; i<seoList.length; i++ )
		{
			//setTimeout( function() { testSeo( i, seoList ); }, 1000 );
			setTimeout( function() { alert( i ); }, 1000 );
			
		}
	}
	
	function testSeo( i, seoList )
	{
		document.getElementById( seoList[i]["id"] ).innerHTML = "pending";//xmlhttp.responseText;
				
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET",seoList[i]["url"], true);
		xmlhttp.onreadystatechange = function()
		{
			if ( xmlhttp.readyState==4 && xmlhttp.status==200 )
			{
				document.getElementById( seoList[i]["id"] ).innerHTML = "OK";
				if ( ++i < seoList.length )
				{
					//setTimeout( function() { testSeo( i ); }, 500 );//testSeo( i );
					alert( i );
					testSeo( i );
				}
				
				
			}
		}
		xmlhttp.send();
		
		
	}*/
	
	
</script>

<script>

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

</script>
