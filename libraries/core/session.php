<?php
defined('BW') or die("Acesso negado!");
@session_start();

class bwSession
{	
	function createToken( $length = 16 )
	{
		static $chars	=	'0123456789abcdef';
		$max			=	strlen( $chars ) - 1;
		$token			=	'';
		$name 			=  session_name();
		for( $i = 0; $i < $length; ++$i ) {
			$token .=	$chars[ (rand( 0, $max )) ];
		}

		$token = md5($token.$name);
		
		$_SESSION['bw.token'] = $token;
		
		return $token;
	}
	
	function getToken()
	{
		if(isset($_SESSION['bw.token']))
			return $_SESSION['bw.token'];
		else
			return bwSession::createToken();
	}
	
	function get($id, $default = false, $session)
	{
		$s = 'bw.'.$session;
	
		if(isset($_SESSION[$s][$id]))
			return $_SESSION[$s][$id];
			
		return $default;
	}

	function set($id, $value, $session)
	{
		$s = 'bw.'.$session;
		$_SESSION[$s][$id] = $value;
		
		return;
	}
		
	function del($id, $session)
	{
		$s = 'bw.'.$session;		
		if(isset($_SESSION[$s][$id]))
			unset($_SESSION[$s][$id]);
	}	
	
}

?>
