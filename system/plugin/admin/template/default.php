<?php

if( !empty($_GET["p"]) )
{
	switch ( $_GET["p"] )
	{
		case 'page-save':

			include _SYSTEM_DIRECTORY.'plugin/admin/pages/zipAndDownloadContent.php';
			break;

		case 'csv-export':

			include _SYSTEM_DIRECTORY.'plugin/admin/pages/downloadCsv.php';
			break;

		case 'page-html-save':

			include _SYSTEM_DIRECTORY.'plugin/admin/pages/zipAndDownloadHtml.php';
			break;

		default:
			break;
	}
}

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - <?php echo _ROOT_URL; ?></title>

<style type="text/css"><?php include _SYSTEM_DIRECTORY.'plugin/admin/template/includes/admin.css'; ?></style>

</head>

<body>

	<header>
		<h1>Admin</h1><p><?php echo _ROOT_URL; ?></p>
		<nav>
			<ul>
				<li><a href="admin.php?p=page-debug">Debug</a></li>
				<li><a href="admin.php?p=page-list">Pages list</a></li>
				<li><a href="admin.php?p=page-edit">Edition</a></li>
				<li><a href="admin.php?p=page-csv">CSV</a></li>
				<li>
					<form action="admin.php?p=page-save" method="get">
						<input type="hidden" name="p" value="page-save">
						<input type="submit" value="Export content">
					</form>
				</li>
				<!--<li><a href="admin.php?p=page-save">Export content</a></li>-->
				<li><?php echo Login::getLogoutForm(); ?></li>
			</ul>
		</nav>
	</header>
	
	<section>
		<?php
		
			if( !empty($_GET["p"]) )
			{
				switch ( $_GET["p"] )
				{
					case 'page-debug':

						include _SYSTEM_DIRECTORY.'plugin/admin/pages/pageDebug.php';
						break;

					case 'page-list':

						include _SYSTEM_DIRECTORY.'plugin/admin/pages/pageList.php';
						break;

					case 'page-edit':

						include _SYSTEM_DIRECTORY.'plugin/admin/pages/pageEdit.php';
						break;

					case 'page-csv':

						include _SYSTEM_DIRECTORY.'plugin/admin/pages/pageCsv.php';
						break;

					default:
						break;
				}
			}
			
		?>
	</section>

</body>
</html>
