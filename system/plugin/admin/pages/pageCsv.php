<?php

	//include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';

	
	$ACTUAL_PAGE_URL = 'admin.php?p=page-csv';
	
	if ( isset($_POST['csv']) )
	{
		if ( $_POST['csv'] === 'export' )
		{
			/*include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/csvGenerator.php';
			generateHtml( $pagesDebugPage );*/
			echo '<script>window.location.href = "admin.php?p=csv-export";</script>';
		}
		elseif ( $_POST['csv'] === 'generate' && isset($_FILES['file']) && $_FILES['file']['error'] == 0 )
		{
			$rootDir = 'temp-init/';
			$uploadfile = $rootDir.basename($_FILES['file']['name']);
			if(!is_dir($rootDir)) mkdir($rootDir);

			if ( !move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile) )
			{
				print_r($_FILES);
			}

			createInit( $rootDir, $uploadfile );

		}
		elseif ( $_POST['csv'] === 'update' )
		{
			
		}
	}
	
			
?>

<h1>CSV page</h1>

<table>
	<caption><h2>Parameters</h2></caption>
	<tr>
		<th>Export CSV of the pages</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" style="display:inline;">
				<input type="hidden" name="csv" value="export" />
				<input type="submit" value="Export" /> 
			</form>
		</td>
	</tr>
	<tr>
		<th>Generate files by CSV</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" enctype="multipart/form-data" style="display:inline;">
				<input type="hidden" name="csv" value="generate" />
				<input type="file" name="file" />
				<input type="submit" value="Import" /> 
			</form>
		</td>
	</tr>
	<tr>
		<th>Update pages by CSV</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" enctype="multipart/form-data" style="display:inline;">
				<input type="hidden" name="csv" value="update" />
				<input type="file" name="file" />
				<input type="submit" value="Update" style="color:red;" /> 
			</form>			
		</td>
	</tr>
</table>

<?php 

function createInit( $rootDir, $file )
{
	include_once _SYSTEM_DIRECTORY.'init/Cache.php';
	
	
	$cache = new Cache();
	
	
	$pagesPHP = '<?php'."\n\n";
	$pagesPHP .= '$pageList = PageList::getInstance();'."\n\n";
	
	
	$listPageId = array();
	
	$row = 1;
	if (($handle = fopen($file, "r")) !== FALSE)
	{
		ini_set("auto_detect_line_endings", true);
		while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE)
		{
			
			if ( !isset($listPageId[$data[1]]) )
			{
				if ( hackCsvAccent($data[0]) == 'static' )
				{
					$pagesPHP .= '$pageList->addPage( \''.hackCsvAccent($data[1]).'\' );'."\n";
				}
				elseif ( hackCsvAccent($data[0]) == 'default' )
				{
					$pagesPHP .= '$pageList->addDefaultPage( \''.hackCsvAccent($data[1]).'\' );'."\n";
				}
				elseif ( hackCsvAccent($data[0]) == 'error404' )
				{
					$pagesPHP .= '$pageList->addError404Page( \''.hackCsvAccent($data[1]).'\' );'."\n";
				}
				$listPageId[$data[1]] = true;
			}
			elseif ( hackCsvAccent($data[0]) == 'dynamic' )
			{
				$pagesPHP .= '$pageList->addDynamicPage( \''.hackCsvAccent($data[1]).'\', ';
				$pagesPHP .= '\''.hackCsvAccent($data[3]).'\', ';
				$pagesPHP .= '\''.hackCsvAccent($data[2]).'\', ';
				$pagesPHP .= '\''.hackCsvAccent($data[14]).'\' ';
				$pagesPHP .= ');'."\n";
			}
			
			if (	hackCsvAccent($data[0]) == 'static' ||
					hackCsvAccent($data[0]) == 'default' ||
					hackCsvAccent($data[0]) == 'error404' )
			{
				// 0'type',1'!id',2'!lang',3'url',4'title'
				// 5'description',6'template',7'header',8'visible'
				// 9'cachable',10'tags',11'phpHeader',12'contents',13'requests'
				// 14'!vo'
				
				$pageInitPHP = '<?php'."\n\n";
				$pageInitPHP .= '$url = \''.hackCsvAccent($data[3]).'\';'."\n";
				$pageInitPHP .= '$title = \''.hackCsvAccent($data[4]).'\';'."\n";
				$pageInitPHP .= '$description = \''.hackCsvAccent($data[5]).'\';'."\n";
				$pageInitPHP .= '$template = \''.hackCsvAccent($data[6]).'\';'."\n";
				$pageInitPHP .= '$header = \''.hackCsvAccent($data[7]).'\';'."\n";
				$pageInitPHP .= '$visible = '.hackCsvAccent("$data[8]").';'."\n";
				$pageInitPHP .= '$cachable = '.hackCsvAccent("$data[9]").';'."\n\n";
				
				$pageInitPHP .= '$tags = \''.hackCsvAccent($data[10]).'\';'."\n";
				$pageInitPHP .= '$phpHeader = \''.hackCsvAccent($data[11]).'\';'."\n";
				$pageInitPHP .= '$contents = \''.hackCsvAccent($data[12]).'\';'."\n";
				$pageInitPHP .= '$requests = \''.hackCsvAccent($data[13]).'\';'."\n";
				
				$id = hackCsvAccent($data[1]);
				$lang = hackCsvAccent($data[2]);
				
				$cache->writesCacheFile( $pagesPHP, $rootDir.$id.'/'.$lang.'-init.php' );
			}
			
			$row++;
			$num = count($data);
			for ($c=0; $c < $num; $c++)
			{
				//echo hackCsvAccent($data[$c]) . "<br />\n";
			}
		}
		fclose($handle);
	}
	
	$cache->writesCacheFile( $pagesPHP, $rootDir.'pages.php' );
}

function hackCsvAccent($text)
{
	//echo mb_detect_encoding($text);
	//$text = iconv('ASCII', 'UTF-8//TRANSLIT', $text);
	//$text = iconv( mb_detect_encoding($text), 'UTF-8//TRANSLIT', $text );
	$text = iconv('UTF-16LE', 'UTF-8//TRANSLIT', $text);
	//$text = iconv('Windows-1252', 'UTF-8//TRANSLIT', $text);
	//$text = mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8');;
	return $text;//mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8' );
}

function createPage(  )
{
	
}