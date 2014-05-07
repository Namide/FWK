<?php

	//include_once _SYSTEM_DIRECTORY.'plugin/admin/pages/includes/helpers.php';

	
	$ACTUAL_PAGE_URL = 'admin.php?p=page-csv';
	
	if ( isset($_POST['csv']) )
	{
		if ( $_POST['csv'] === 'export' )
		{
			echo '<script>window.location.href = "admin.php?p=csv-export";</script>';
		}
		elseif ( $_POST['csv'] === 'generate' && isset($_FILES['file']) && $_FILES['file']['error'] == 0 )
		{
			$rootDir = _TEMP_DIRECTORY;
			$uploadfile = $rootDir.basename($_FILES['file']['name']);
			if(!file_exists($rootDir)) mkdir($rootDir);

			if ( !move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile) )
			{
				print_r($_FILES);
			}

			createInit( $rootDir, $uploadfile );
			
			echo '<script>window.location.href = "admin.php?p=download-init";</script>';
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
		<th>Export CSV of the pages (UTF-8)</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" style="display:inline;">
				<input type="hidden" name="csv" value="export" />
				<input type="submit" value="Export" /> 
			</form>
		</td>
	</tr>
	<tr>
		<th>Generate files by CSV (UTF-8)</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" enctype="multipart/form-data" style="display:inline;">
				<input type="hidden" name="csv" value="generate" />
				<input type="file" name="file" />
				<input type="submit" value="Import" /> 
			</form>
		</td>
	</tr>
	<!-- <tr>
		<th>Update pages by CSV</th>
		<td>
			<form action="<?php echo $ACTUAL_PAGE_URL; ?>" method="POST" enctype="multipart/form-data" style="display:inline;">
				<input type="hidden" name="csv" value="update" />
				<input type="file" name="file" />
				<input type="submit" value="Update" style="color:red;" /> 
			</form>			
		</td>
	</tr> -->
</table>

<?php 

function createInit( $rootDir, $file )
{
	//include_once _SYSTEM_DIRECTORY.'helpers/Cache.php';
	include_once _SYSTEM_DIRECTORY.'helpers/FileUtil.php';
	
	
	//$cache = new Cache();
	
	
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
				
				if( count($data) > 3 )
					$pagesPHP .= '\''.hackCsvAccent($data[3]).'\', ';
				else
					$pagesPHP .= '\'\', ';
				
				if( count($data) > 2 )
					$pagesPHP .= '\''.hackCsvAccent($data[2]).'\', ';
				else
					$pagesPHP .= '\'\', ';
				
				if( count($data) > 14 )
					$pagesPHP .= '\''.hackCsvAccent($data[14]).'\' ';
				else
					$pagesPHP .= '\'\', ';
				
				if( count($data) > 15 )
					$pagesPHP .= '\''.hackCsvAccent($data[15]).'\' ';
				else
					$pagesPHP .= '\'\', ';
				
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
				
				if( count($data) > 3 )
					$pageInitPHP .= '$url = \''.hackCsvAccent($data[3]).'\';'."\n";
				if( count($data) > 4 )
					$pageInitPHP .= '$title = \''.hackCsvAccent($data[4]).'\';'."\n";
				if( count($data) > 5 )
					$pageInitPHP .= '$description = \''.hackCsvAccent($data[5]).'\';'."\n";
				if( count($data) > 6 )
					$pageInitPHP .= '$template = \''.hackCsvAccent($data[6]).'\';'."\n";
				if( count($data) > 7 )
					$pageInitPHP .= '$header = \''.hackCsvAccent($data[7]).'\';'."\n";
				if( count($data) > 8 )
					$pageInitPHP .= '$visible = '.hackCsvAccent("$data[8]").';'."\n";
				if( count($data) > 9 )
					$pageInitPHP .= '$cachable = '.hackCsvAccent("$data[9]").';'."\n\n";
				
				if( count($data) > 10 && strlen($data[10]) > 0 )
					$pageInitPHP .= '$tags = '.hackCsvAccent($data[10]).';'."\n";
				else
					$pageInitPHP .= '//$tags = array();'."\n";
				
				if( count($data) > 11 && strlen($data[11]) > 0 )
					$pageInitPHP .= '$phpHeader = \''.hackCsvAccent($data[11]).'\';'."\n";
				else
					$pageInitPHP .= '//$phpHeader = \'\';'."\n";
				
				if( count($data) > 12 && strlen($data[12]) > 0 )
					$pageInitPHP .= '$contents = '.hackCsvAccent($data[12]).';'."\n";
				else
					$pageInitPHP .= '//$contents = array();'."\n";
				
				if( count($data) > 13 && strlen($data[13]) > 0 )
					$pageInitPHP .= '$requests = '.hackCsvAccent($data[13]).';'."\n";
				else
					$pageInitPHP .= '//$requests = array();'."\n";
				
				$id = hackCsvAccent($data[1]);
				$lang = hackCsvAccent($data[2]);
				
				FileUtil::writeFile( $pageInitPHP, $rootDir.$id.'/'.$lang.'-init.php' );
				//$cache->writesCacheFile( $pageInitPHP, $rootDir.$id.'/'.$lang.'-init.php' );
			}
			
			/*$row++;
			$num = count($data);
			for ($c=0; $c < $num; $c++)
			{
				echo hackCsvAccent($data[$c]) . "<br />\n";
			}*/
		}
		fclose($handle);
	}
	else
	{
		echo 'load error';
	}
	//if ( file_exists($file) ) unlink($file);
	//$cache->writesCacheFile( $pagesPHP, $rootDir.'pages.php' );
	FileUtil::writeFile( $pagesPHP, $rootDir.'pages.php' );
	
}

function hackCsvAccent($text)
{
	$encod = mb_detect_encoding($text);
	$text = iconv( $encod, 'UTF-8//IGNORE', $text);
	
	//echo '->'.$encod;
	//$text = iconv('UTF-16LE', 'UTF-8//IGNORE', $text);// or die( '->'.print_r($text).'<-' );
	//echo mb_detect_encoding($text);
	//$text = iconv('ASCII', 'UTF-8//TRANSLIT', $text);
	//$text = iconv( mb_detect_encoding($text), 'UTF-8//TRANSLIT', $text );
	//$text = iconv('Windows-1252', 'UTF-8//TRANSLIT', $text);
	//$text = mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8');
	$text = str_replace('\'', '\\\'', $text );
	return $text;//mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8' );
}

function createPage(  )
{
	
}