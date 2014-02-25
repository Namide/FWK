<?php

	$ACTUAL_PAGE_URL = 'admin.php?p=debug';

	$pageList = PageList::getInstance();
	$pages = $pageList->getPagesByUrl();

	
			
	if( !empty($_POST['clear']) && $_POST['clear'] === 'ALL' )
	{
		if ( is_dir($_CACHE_DIRECTORY) ) { delTree( $_CACHE_DIRECTORY ); }
		echo '<script>window.location.href = "'.$ACTUAL_PAGE_URL.'";</script>';
	}

	function delTree( $dir )
	{
		$files = array_diff( scandir($dir), array('.','..') );
		foreach ($files as $file)
		{
			if (is_dir("$dir/$file")) { delTree("$dir/$file"); }
			else { unlink("$dir/$file"); }
		}
		return rmdir($dir);
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
	
</table>



<table>
	
	<caption><h2>Pages - global</h2></caption>
	
	<tr>
		<th>Visible pages number</th>
		<td><?php echo count( $pageList->getAllPages( 'all' ) ); ?> / <?php echo count($pages); ?></td>
	</tr>
	<tr>
		<th>Internal links</th>
		<td><div id="link-checker"><button onclick="processor.go();">check</button></div></td>
	</tr>
	<tr>
		<th>Total weight of content</th>
		<td><?php echo getFormatedSize( 'content', FALSE ); ?></span></td>
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
	</tr>

<?php

	$i = 0;
	foreach( $pages as $page )
	{
?>
	<tr>

		<!-- num -->
		<td>
			<?php echo ++$i; ?>
		</td>


		<!-- url -->
		<td>
			<a href="<?php echo PageUtils::urlPageToAbsoluteUrl( $page->getUrl()) ?>" class="checkURL"><?php echo $page->getUrl() ?></a><br />
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

	</tr>
		
<?php } ?>

</table>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
<!-- <script type="text/javascript" src="'.PageUtils::getRootAbsoluteUrl('---/js/LinkChecker.js').'"></script> -->
<?php include $_SYSTEM_DIRECTORY.'admin/pages/includes/linkChecker.php'; ?>

<script>

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

</script>


<?php

function dirSize($directory)
{
	$size = 0;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
	{
		$size += $file->getSize();
	}
	return $size;
} 

function getFormatedSize( $path , $color = TRUE )
{
	$size = dirSize($path);
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

/*function getJsLinkChecker()
{
	$output = '';
	return $output;
}*/

