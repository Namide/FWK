<?php

// delete directories empty
function cleanDirRecurs( $dir )
{
	
	$numChilds = 0;
	
	if ( !file_exists($dir) ) { return 0; }
	if ( is_file($dir) ) { return 1; }
	
	$files = array_diff( scandir($dir), array( '.', '..', '.DS_Store', 'Thumbs.db' ) );
	foreach ($files as $file)
	{
		if (is_dir("$dir/$file"))
		{
			$numChilds += cleanDirRecurs("$dir/$file");
		}
		else
		{
			//unlink("$dir/$file");
			$numChilds++;
		}
	}
	
	
	if ( $numChilds < 1 )
	{
		rmdir($dir);
	}
	
	return $numChilds;
}

function getListDir( $dir )
{
	$array = array();
	
	if ( !file_exists($dir) ) { return $array; }
	if ( is_file($dir) ) { return $array; }
	
	array_push($array, $dir );
	
	$files = array_diff( scandir( $dir ), array( '.', '..', '.DS_Store', 'Thumbs.db' ) );
	foreach ( $files as $file )
	{
		if ( is_dir("$dir/$file") )
		{
			$array = array_merge( $array, getListDir( "$dir/$file" ) );
		}
	}
	
	return $array;
}

function delTree( $dir )
{
	if ( !file_exists($dir) )
	{
		echo 'error: No such file or directory "'.$dir.'"';
	}
	
	$files = array_diff( scandir($dir), array('.','..') );
	foreach ($files as $file)
	{
		if (is_dir("$dir/$file")) { delTree("$dir/$file"); }
		else { unlink("$dir/$file"); }
	}
	return rmdir($dir);
}


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

function copyDir( $dir2copy, $dir_paste )
{
	// On vérifie si $dir2copy est un dossier
	if (is_dir($dir2copy))
	{

		// Si oui, on l'ouvre
		if ($dh = opendir($dir2copy))
		{     
			// On liste les dossiers et fichiers de $dir2copy
			while (($file = readdir($dh)) !== false)
			{
				// Si le dossier dans lequel on veut coller n'existe pas, on le créé
				if (!is_dir($dir_paste)) mkdir ($dir_paste, 0777);

				// S'il s'agit d'un dossier, on relance la fonction rÃ©cursive
				if(is_dir($dir2copy.$file) && $file != '..'  && $file != '.') copyDir ( $dir2copy.$file.'/' , $dir_paste.$file.'/' );     
				// S'il sagit d'un fichier, on le copue simplement
				elseif( $file != '..' &&
						$file != '.' &&
						substr($file, -4, 4) != '.php' &&
						substr($file, -5, 4) != '.php' ) copy ( $dir2copy.$file , $dir_paste.$file );                                       
			}

			// On ferme $dir2copy
			closedir($dh);

		}

	}
}

/*function getJsLinkChecker()
{
	$output = '';
	return $output;
}*/

