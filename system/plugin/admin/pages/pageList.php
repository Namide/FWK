<?php

	include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';

	$ACTUAL_PAGE_URL = 'admin.php?p=page-list';

	$pageList = PageList::getInstance();
	$pages = $pageList->getPagesByUrl();	
?>

<h1>Pages list</h1>



<table>
	<caption><h2>Page list</h2></caption> 
	<tr>
		
		<th>num</th>
		<th>directory</th>
		<th>url</th>
		
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
			<a href="<?php echo BuildUtil::getInstance()->urlPageToAbsUrl( $page->getUrl()) ?>" class="checkURL"><?php echo $page->getUrl() ?></a><br />
		</td>

	</tr>
		
<?php } ?>

</table>





