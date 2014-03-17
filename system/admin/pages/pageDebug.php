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
		<td><div id="seo-test"><button onclick="seoTest.start('seo-test');">SEO test</button></div></td>
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
	<?php include $_SYSTEM_DIRECTORY.'admin/pages/includes/seoTest.js'; ?>
	var seoTest = new SeoTest( <?php echo $seoList; ?> );
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
