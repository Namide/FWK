<?php

class Login
{
	
	function __construct()
	{		
		session_start();
		
		if ( isset( $_POST['disconnect'] ) )
		{
			$this->disconnect();
		}
		
		if ( !$this->isLogged() )
		{
			if( !$this->tryConnect() )
			{
				$this->disconnect( FALSE );
				echo self::getLoginForm();
				exit();
			}
		}
    }
	
	private function isLogged()
	{
		if( !isset( $_SESSION['userId'] ) ||
			!isset( $_SESSION['userName'] ) )
		{
			return FALSE;
		}
		
		$userId = (int) $_SESSION['userId'];
		$userName = htmlentities( $_SESSION['userName'] );
		
		global $_ADMIN_USERS;
		if ( $_ADMIN_USERS[$userId][0] != $userName )
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function tryConnect()
	{
		global $_ADMIN_USERS;
		global $_ADMIN_IP;
		
		if( isset( $_POST ) &&
			!empty( $_POST['userName'] ) &&
			!empty( $_POST['userPass'] ) )
		{
			
			$inputUserName = htmlentities( $_POST['userName'] );
			$inputUserPass = sha1( htmlentities( $_POST['userPass'] ) );
			$inputIp = htmlentities( $_SERVER["REMOTE_ADDR"] );
			
			$inputId = array_search( array($inputUserName, $inputUserPass), $_ADMIN_USERS );
			if ( !($inputId === FALSE) )
			{
				if (	$inputUserName === $_ADMIN_USERS[$inputId][0] &&
						$inputUserPass === $_ADMIN_USERS[$inputId][1] &&
						in_array( $inputIp, $_ADMIN_IP, TRUE ) )
				{
					$this->connect( $inputId, $inputUserName );
					return TRUE;
				}
			}
			
			if ( !in_array( $inputIp, $_ADMIN_IP, TRUE ) )
			{
				echo '<span style="color:red;">IP unrecognized, the administrator must to add your IP: '.$_SERVER['REMOTE_ADDR'].'</span><br>';
			}
			else
			{
				echo '<span style="color:red;">Login/password unrecognized.</span><br>';
			}
		}
		
		
		
		return FALSE;
	}
	
	private function connect( $id, $name )
	{
		$_SESSION['userId'] = $id;
		$_SESSION['userName'] = $name;
	}

	public static function getLogoutForm()
	{
		$output = '<form action="admin.php" method="POST" >
			<input type="hidden" name="disconnect" value="TRUE">
            <input type="submit" value="Logout" /> 
        </form>';
		return $output;
	}
	
	public function disconnect( $destroy = TRUE )
	{
		if( isset( $_SESSION['userId'] ) &&
			isset( $_SESSION['userName'] ) )
		{
			$id = $_SESSION['userId'];
			$name = $_SESSION['userName'];
			
			unset($_SESSION['userId']);
			unset($_SESSION['userName']);
		}
		
		if ( $destroy ) session_destroy();
	}
	
	private static function getLoginForm()
	{
		$output = '<form action="admin.php" method="POST" >
            Username: <input type="text" name="userName" /><br>
            Password: <input type="password" name="userPass" /><br>
            <input type="submit" value="Login" /> 
        </form>';
		return $output;
	}
	
}