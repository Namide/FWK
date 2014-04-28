<?php

	include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';

	$ACTUAL_PAGE_URL = 'admin.php?p=page-list';
	$PAGE_EDIT_URL = 'admin.php?p=page-edit';

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
			<td>
				<input list="directories" type=text name="pageId" required />
				<datalist id="directories">
					<?php
						$listDir = getListDir( _CONTENT_DIRECTORY );
						foreach ($listDir as $value)
						{
							echo '<option value="'.substr( $value, strlen( _CONTENT_DIRECTORY.'/' ) ).'">';
						}
					?>
				</datalist>
			</td>
			<td>basic/homepage</td>
		</tr>
				
		<tr>
			<td><strong>URL</strong></td>
			<td><input type=text name="url" required /></td>
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
		
		<!--
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
		-->
		
		<tr>
			<td></td>
			<td><input type="hidden" name="type" value="add" /></td>
			<td><input type="submit" value="Add" /></td>
		</tr>
		
	</table>
</form>





