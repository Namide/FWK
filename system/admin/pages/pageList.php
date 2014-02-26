<?php

	$ACTUAL_PAGE_URL = 'admin.php?p=page-list';
	$PAGE_EDIT_URL = 'admin.php?p=page-edit';

	$pageList = PageList::getInstance();
	$pages = $pageList->getPagesByUrl();

	
	/*		
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
	*/			
?>

<h1>Pages list</h1>



<table>
	<caption><h2>Page list</h2></caption> 
	<tr>
		
		<th>num</th>
		<th>directory</th>
		<th>url</th>
		<!-- <th>edition</th> -->
		
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

		<!-- directory -->
		<td>
			<?php echo 'content'.'/'.$page->getID().'/'; ?>
		</td>

		<!-- url -->
		<td>
			<a href="<?php echo PageUtils::urlPageToAbsoluteUrl( $page->getUrl()) ?>" class="checkURL"><?php echo $page->getUrl() ?></a><br />
		</td>

		<!-- url -->
		<!-- <td>
			<form action="<?php echo $PAGE_EDIT_URL; ?>" method="POST" style="display:inline;">
				<input type="hidden" name="pageId" value="<?php echo $page->getID(); ?>" />
				<input type="submit" value="Edit" /> 
			</form>
		</td> -->

	</tr>
		
<?php } ?>

</table>


<form action="<?php echo $PAGE_EDIT_URL; ?>" method="POST" style="display:inline;">
	
	<table>
		
		<caption><h2>Add page</h2></caption> 
	
		<tr>
			<th>Label</th>
			<th>Value</th>
			<th>Example</th>
		</tr>
		
		<tr>
			<td><strong>Directory</strong></td>
			<td><input type=text name="pageId" /></td>
			<td>basic/homepage</td>
		</tr>
		
		<tr>
			<td><strong>URL</strong></td>
			<td><input type=text name="url" /></td>
			<td>en/homepage</td>
		</tr>
		
		<tr>
			<td><strong>Language</strong></td>
			<td>
				<select name="language">
					<?php

						foreach ( LanguageList::getInstance()->getList() as $lang)
						{
							echo '<option>',$lang,'</option>';
						}

					?>
				</select>
			</td>
			<td>en</td>
		</tr>
		
		<tr>
			<td><strong>Template</strong></td>
			<td><input type=text name="template" value="default" /></td>
			<td>default</td>
		</tr>
		
		<tr>
			<td><strong>Visible</strong></td>
			<td><input type="checkbox" name="visible" value="true" checked></td>
			<td>x</td>
		</tr>
		
		<tr>
			<td><strong>Cachable</strong></td>
			<td><input type="checkbox" name="cachable" value="true" checked></td>
			<td>x</td>
		</tr>
		
		<tr>
			<td><strong>Title</strong></td>
			<td><input type=text name="title" /></td>
			<td>Homepage of FWK</td>
		</tr>

		<tr>
			<td></td>
			<td><input type="hidden" name="type" value="add" /></td>
			<td><input type="submit" value="Add" /></td>
		</tr>
		
	</table>
</form>









<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
<!-- <script type="text/javascript" src="'.PageUtils::getRootAbsoluteUrl('---/js/LinkChecker.js').'"></script> -->
<?php include $_SYSTEM_DIRECTORY.'admin/pages/includes/linkChecker.php'; ?>

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

	$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;

	$sizeChar = round($size,$round).' '.$sizes[$i];

	if ( !$color ) return $sizeChar;

	if ( $i < 2 && $size < 150 ) 		return '<span style="color:green">'.$sizeChar.'</span>';
	else if ( $i > 1 || ($i == 1 && $size > 700) ) return '<strong style="color:red">'.$sizeChar.'</strong>';
	return $sizeChar;
}
