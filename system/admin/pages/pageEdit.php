<?php

	$ACTUAL_PAGE_URL = 'admin.php?p=page-edit';

	$pageList = PageList::getInstance();
	$pages = $pageList->getPagesByUrl();

	if ( !empty($_POST['type']) )
	{
		if ( $_POST['type'] === 'add' )
		{
			addPage();
		}
	}
	
	if ( !empty( $_POST['update'] ) && !empty( $_POST['file'] ) )
	{
		$fileName = $_CONTENT_DIRECTORY.$_POST['file'];

		try
		{
			unlink($fileName);
			
			$file = fopen( $fileName , "w" );
			$fileContent = $_POST['update'];
			fwrite($file, $fileContent);
			fclose($file);
		}
		catch (Exception $ex)
		{
			echoError( $ex->getMessage() );
		}
		
	}
			
?>











<h2>File list</h2>

<?php

///////////////////////////////////
//
//		FILE LIST !
//
///////////////////////////////////
	

function formDir( $parent = '', $directory = '/' )
{
	
	
	//$parent .= '/';
	global $_CONTENT_DIRECTORY;
	global $ACTUAL_PAGE_URL;
	
    $rep = opendir($_CONTENT_DIRECTORY.$parent) or die('the directory '.$_CONTENT_DIRECTORY.$parent.' don\'t exist');
	
	
	echo '<strong style="margin:12px 0 4px -2px; display:inline-block;">'.$directory.'</strong>';
    echo '<ul style="border-left:1px solid #CCC;">';
	
	
	
	while($file = @readdir($rep))
    {

        if ( $file === "." || $file === ".." || substr( $file, 0, 1 ) === '.' ) continue;


        if( !is_dir($_CONTENT_DIRECTORY.$parent.$file) )
        {
			
			$file_extension = strtolower(substr(strrchr( str_replace ( '.cache', '', $file ) ,"."),1));
			if( $file_extension == 'php' )
			{
				echo '<li>',$file;
				echo '<form action="',$ACTUAL_PAGE_URL,'#edit-page" method="POST" style="display:inline;">
				<input type="hidden" name="type" value="edit" />
				<input type="hidden" name="file" value="',$parent.$file,'" />
				<input type="submit" value="Edit" style="color:red;" /> 
			</form>';
				echo '</li>';
			}
        }



    }
	closedir($rep);
	
	$rep = opendir($_CONTENT_DIRECTORY.$parent) or die('rectory '.$_CONTENT_DIRECTORY.$parent.' don\'t exist');
	while($file = @readdir($rep))
    {

        if ( $file === "." || $file === ".." || substr( $file, 0, 1 ) === '.' ) continue;


        if( is_dir($_CONTENT_DIRECTORY.$parent.$file) )
        {
			formDir( $parent.$file.'/' , $file );
        }
        

    }
	closedir($rep);
	
	
	
	
	
	echo '</ul>';

    
}

echo '<ul>';
formDir();
echo '</ul>';

?>








<?php

	
	///////////////////////////////////
	//
	//		CONTENT EDITOR
	//
	///////////////////////////////////
	
	

	if ( !empty($_POST['type']) && $_POST['type'] === 'edit' )
	{ 
		
		if ( empty( $_POST['file'] ) )
		{
			echoError( 'no variable file' );
		}
		if ( !file_exists($_CONTENT_DIRECTORY.$_POST['file']) )
		{
			echoError( 'the file '.$_POST['file'].'don\'t exist' );
		}
		
		
	?>

<h2>File: <code><?php echo $_POST['file']; ?></code></h2>
		<form  action="<?php echo $ACTUAL_PAGE_URL; ?>#edit-page" method="POST" id="edit-page">
			
			<div style="padding: 16px; border: 1px solid #CCC;">
				<textarea name="update" rows="50" cols="100" style="width:100%; border:none; font-family: 'Courier New', Courier, monospace; font-size: 13px;"><?php readfile( $_CONTENT_DIRECTORY.$_POST['file'] ); ?></textarea>
			</div>
			
			<input type="hidden" name="file" value="<?php echo $_POST['file']; ?>" />
			<input type="hidden" name="type" value="edit" />
			<input type="submit" value="Update" style="color:red; margin:8px 0; font-size: 16px;" /> 
		</form>

<?php 
	}
?>













<?php

	///////////////////////////////////
	//
	//		ADD PAGE
	//
	///////////////////////////////////
	

	function addPage()
	{
		//$_POST['pageId']
		
		if ( empty( $_POST['url'] ) )		{ echoError( 'no variable url' ); }
		if ( empty( $_POST['language'] ) )	{ echoError( 'no variable language' ); }
		if ( !isset( $_POST['template'] ) )	{ echoError( 'no variable template' ); }
		if ( empty( $_POST['visible'] ) )	{ echoError( 'no variable visible' ); }
		if ( empty( $_POST['cachable'] ) )	{ echoError( 'no variable cachable' ); }
		if ( !isset( $_POST['title'] ) )		{ echoError( 'no variable title' ); }

		if ( PageList::getInstance()->hasPage( $_POST['pageId'], $_POST['language'] ) )	{ echoError( 'this directory already exist' ); }
		if ( PageList::getInstance()->hasUrl( $_POST['url'] ) )	{ echoError( 'this URL already exist' ); }
		
		global $_CONTENT_DIRECTORY;
		$directory = $_CONTENT_DIRECTORY.$_POST['pageId'];
		
        if( !file_exists($directory) )
		{
			try
			{
				mkdir( $directory, 0777, TRUE );
			}
			catch (Exception $e)
			{
				echo echoError( $e->getMessage() );
			}
		}
		
		// ADD INIT FILE
		try
		{
			$fileInitName = $directory.'/'.$_POST['language'].'-init.php';
			if( !file_exists($fileInitName) )
			{
				$fileInit = fopen( $fileInitName , "w" );
				$fileInitContent = '<?php

$url = \''.$_POST['url'].'\';      
$title = \''.$_POST['title'].'\';
$template = \''.$_POST['template'].'\';
$header = \' <title>'.$_POST['title'].'</title>
	<meta name="robots" content="all" />\';

$visible = '. ( ( (bool) $_POST['visible'] ) ? 'TRUE' : 'FALSE' ) .';
$cachable = '. ( ( (bool) $_POST['cachable'] ) ? 'TRUE' : 'FALSE' ) .';

//$categories = [];
//$phpHeader;
//$preface;';
				fwrite($fileInit, $fileInitContent);
				fclose($fileInit);
			
			}
			else
			{
				echoError( $fileInitName.' exist!' ); 
			}
		}
		catch (Exception $e)
		{
			echo echoError( $e->getMessage() );
		}
			
		// ADD BUILD FILE
		try
		{
			$fileBuildName = $directory.'/'.$_POST['language'].'-build.php';
			if( !file_exists($fileBuildName) )
			{
				$fileBuild = fopen( $fileBuildName , "w" );
				$fileBuildContent = '<?php

$body = <<<EOF
<p>Empty page</p>
EOF;
';
				fwrite($fileBuild, $fileBuildContent);
				fclose($fileBuild); 
			}
			else
			{
				echoError( $fileBuildName.' exist!' ); 
			}
		}
		catch (Exception $e)
		{
			echo echoError( $e->getMessage() );
		}
		
	}
	
	function echoError( $text = '' )
	{
		echo 'Admin error : "', $text, '"';
		exit;
	}

?>
