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
		$fileName = _CONTENT_DIRECTORY.$_POST['file'];

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
	global $ACTUAL_PAGE_URL;
	
	$childNum = 0;
	$rep = opendir(_CONTENT_DIRECTORY.$parent) or die('the directory '._CONTENT_DIRECTORY.$parent.' don\'t exist');
	while($file = @readdir($rep))
    {
        if ( $file === "." || $file === ".." || substr( $file, 0, 1 ) === '.' ) continue;
		
		if( is_dir(_CONTENT_DIRECTORY.$parent.$file) )
		{
			$childNum++;
		}
		else if( !is_dir(_CONTENT_DIRECTORY.$parent.$file) )
		{
			$file_extension = strtolower(substr(strrchr($file, "."), 1));
			if( $file_extension == 'php' )
			{
				$childNum++;
			}
		}
    }
	closedir($rep);
	
	
	$lastName = 'new';
	if ( numPhpFile( _CONTENT_DIRECTORY.$parent ) > 0 )
	{
		echo '<div><strong style="margin:12px 0 4px -2px; display:block;" >'.( ($childNum>0)?'':'').''.$directory.'</strong> ';
	
		$rep = opendir(_CONTENT_DIRECTORY.$parent) or die('the directory '._CONTENT_DIRECTORY.$parent.' don\'t exist');
		while($file = @readdir($rep))
		{

			if ( $file === "." || $file === ".." || substr( $file, 0, 1 ) === '.' ) continue;


			if( !is_dir(_CONTENT_DIRECTORY.$parent.$file) )
			{

				$file_extension = strtolower(substr(strrchr( str_replace ( '.cache', '', $file ) ,"."),1));
				if( $file_extension == 'php' )
				{
					if ( $lastName != 'new' && $lastName != substr( $file, 0, 3 ) )
					{
						echo '<br>';
					}
					$lastName = substr( $file, 0, 3 );
					
					echo '<form action="',$ACTUAL_PAGE_URL,'#edit-page" method="POST" style="display:inline-block;">
						<input type="hidden" name="type" value="edit" />
						<input type="hidden" name="file" value="',$parent.$file,'" />
						<input type="submit" value="'.$file.'" style="color:red;" /> 
					</form>';
					
					
					
				}
			}



		}
		closedir($rep);

		echo '<ul style="border-left:1px solid #CCC; display:'.( ($directory == '/' ) ? 'block' : 'block' ).';" >';
	
		$rep = opendir(_CONTENT_DIRECTORY.$parent) or die('rectory '._CONTENT_DIRECTORY.$parent.' don\'t exist');
		while($file = @readdir($rep))
		{

			if ( $file === "." || $file === ".." || substr( $file, 0, 1 ) === '.' ) continue;

			if( is_dir(_CONTENT_DIRECTORY.$parent.$file) )
			{
				formDir( $parent.$file.'/' , $file );
			}

		}
		closedir($rep);





		echo '</ul></div>';
		
	}
	
	
}


function numPhpFile( $dir )
{
	if ( !file_exists($dir) ) { return 0; }
	if ( is_file($dir) )
	{
		$file_extension = strtolower( substr( strrchr( $dir ,"." ), 1 ) );
		if( in_array( $file_extension, array('php', 'php5') ) )
		{
		   return 1;
		}
		return 0;
		
	}
	
	$num = 0;
	
	
	$files = array_diff( scandir( $dir ), array( '.', '..', '.DS_Store', 'Thumbs.db' ) );
	foreach ( $files as $file )
	{
		$num += numPhpFile( "$dir/$file" );
	}
	
	return $num;
}


?>


<script language="javascript">
	
	/*function seeHide(target)
	{
		var ul = target.parentNode.children[1];
		//alert(target.childNodes[1]);
		
		if( ul.style.display == "none" )
		{
			ul.style.display="block";
		}
		else
		{
			ul.style.display="none";
		}
		
	}*/
	
</script>

<?php

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
		if ( !file_exists(_CONTENT_DIRECTORY.$_POST['file']) )
		{
			echoError( 'the file '.$_POST['file'].'don\'t exist' );
		}
		
		
	?>

<h2>File: <code><?php echo $_POST['file']; ?></code></h2>
		<form  action="<?php echo $ACTUAL_PAGE_URL; ?>#edit-page" method="POST" id="edit-page">
			
			<div style="padding: 16px; border: 1px solid #CCC;">
				<textarea name="update" rows="50" cols="100" style="width:100%; border:none; font-family: 'Courier New', Courier, monospace; font-size: 13px;"><?php readfile( _CONTENT_DIRECTORY.$_POST['file'] ); ?></textarea>
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
		/*if ( !isset( $_POST['template'] ) )	{ echoError( 'no variable template' ); }
		if ( empty( $_POST['visible'] ) )	{ echoError( 'no variable visible' ); }
		if ( empty( $_POST['cachable'] ) )	{ echoError( 'no variable cachable' ); }
		if ( !isset( $_POST['title'] ) )		{ echoError( 'no variable title' ); }*/

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
				/*$fileInitContent = '<?php

$url = \''.$_POST['url'].'\';      
$title = \''.$_POST['title'].'\';
$template = \''.$_POST['template'].'\';
$header = \' <title>'.$_POST['title'].'</title>
	<meta name="robots" content="all" />\';

$visible = '. ( ( (bool) $_POST['visible'] ) ? 'TRUE' : 'FALSE' ) .';
$cachable = '. ( ( (bool) $_POST['cachable'] ) ? 'TRUE' : 'FALSE' ) .';

//$categories = [];
//$phpHeader;
//$preface;';*/
				$fileInitContent = '<?php

// URL like "en/homepage"
$url = \''.$_POST['url'].'\';

// title of the page like "homepage"
$title = \'Title\';

// description of the page like "FWK is a realy fun framework!"
$description = \'Description\';

// Name of the template like "default"
$template = \'default\';

// Additional tags in the head (like CSS, JS, meta...)
$header = \' <meta name="robots" content="all" />\';

// Is the page visible ? (in the sitemap...)
$visible = TRUE;

// Is the page cachable ? (dynamics page are\'nt cachable)
$cachable = TRUE;

// Add tags to the page
//$tags = array( \'home\', \'info\' );

// Arguments to the php function header() of the page (for other type than HTML, like XML)
//$phpHeader = \'Content-Type: application/xml; charset=utf-8\';

// Additionnal contents accessible from other pages
//$contents = array( \'resume\'=>\'The homepage is a [...]\' );';
				fwrite($fileInit, $fileInitContent);
				fclose($fileInit);
			
			}
			else
			{
				echoError( $fileInitName.' exist!' ); 
			}
			$_POST['type'] = 'edit';
			$_POST['file'] = $_POST['pageId'].'/'.$_POST['language'].'-init.php';
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
/*
	Mustaches

	{{urlPageToAbsoluteUrl:en/post/min-max}}
	{{idPageToAbsoluteUrl:basic/homepage}}
	{{pathTemplate:css/alternative-slideshow.css}}
	{{pathCurrentPage:img/test.jpg}}
*/

$body = <<<EOF

<p>Empty page</p>

EOF;
';
				fwrite($fileBuild, $fileBuildContent);
				fclose($fileBuild);
			}
			else
			{
				echoError( $fileBuildName.' already exist!' ); 
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
