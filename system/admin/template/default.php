<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin</title>

<style type="text/css">
	
	body
	{
		font-family: Arial, sans-serif;
		font-size: 12px;
		color: #444;
	}
	body>header
	{
		position:absolute;
		width:224px;
		padding:16px;
	}
	body>section
	{
		margin-left:256px;
		padding:16px;
	}
	
	p { text-align: left; }
	
	h1 { font-size: 30px; color:#000; margin: 0; }
	h2 { font-size: 20px; color:#000; margin: 30px 0 0 0; }
	h3 { font-size: 18px; color:#000; margin: 18px 0 0 0; }
	h4 { font-size: 16px; color:#000; margin: 15px 0 0 0; }
	h5 { font-size: 14px; color:#000; margin: 12px 0 0 0; }
	h6 { font-size: 12px; color:#000; margin: 10px 0 0 0; }
	
	table tr:nth-child(odd) { background-color:#EEE; 	}
	table td { padding:8px; }
	table th { padding:16px 8px; }
	table { border-spacing: 0; float:left; margin: 0 32px 32px 0; }
	
</style>

</head>

<body>

	<header>
		<h1>Admin</h1>
		<nav>
			<ul>
				<li><a href="admin.php?p=debug">Debug</a></li>
				<li><a href="admin.php?p=page-list">Pages list</a></li>
				<li><?php echo Login::getLogoutForm(); ?></li>
			</ul>
		</nav>
	</header>
	
	<section>
		<?php
		
			global $_SYSTEM_DIRECTORY;
			
			if( !empty($_GET["p"]) )
			{
				switch ( $_GET["p"] )
				{
					case 'debug':

						include $_SYSTEM_DIRECTORY.'admin/pages/debug.php';
						break;

					case 'page-list':

						include $_SYSTEM_DIRECTORY.'admin/pages/pageList.php';
						break;

					case 'page-edit':

						include $_SYSTEM_DIRECTORY.'admin/pages/pageEdit.php';
						break;

					default:
						break;
				}
			}
			
		?>
	</section>

</body>
</html>
