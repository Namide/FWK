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
	table tr:nth-child(odd)
	{
		background-color:#EEE;
	}
	table td { padding:8px; }
	table th { padding:16px 8px; }
	table { border-spacing: 0; }
</style>

</head>

<body>

	<header>
		<h1>Admin</h1>
		<nav>
			<ul>
				<li></li>
				<li><?php echo Login::getLogoutForm(); ?></li>
			</ul>
		</nav>
	</header>
	
	<section>
		<?php 
			global $_SYSTEM_DIRECTORY;
			
			include_once( $_SYSTEM_DIRECTORY.'admin/PageListDebug.php' );
			echo PageListDebug::getInstance()->getAnalyse();
		?>
	</section>

</body>
</html>
