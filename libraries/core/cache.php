<?php
defined('BW') or die("Acesso negado!");
@session_start();

class bwCache
{

	function get($id, $session)
	{
		$i = bwCache::createID($id);
		$s = 'bw.cache';
	
		if(isset($_SESSION[$s][$session][$i]))
			return $_SESSION[$s][$session][$i];
			
		return false;
	}

	function set($id, $value, $session)
	{
		$s = 'bw.cache';
		$i = bwCache::createID($id);
	
		$_SESSION[$s][$session][$i] = $value;

		return false;
	}

	function createID($id)
	{
		$x = date("H");
		$i = sha1(md5("$id::$x"));
	
		return $i;
	}

}
?>
