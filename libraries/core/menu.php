<?php
defined('BW') or die("Acesso negado!");

class bwMenu extends bwObject
{
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
 
 	var $cache = array();
	
	function getAll()
	{
		$dql = Doctrine_Query::Create()
			->from("MenuItem m")
			->where('m.status = 1')
			->orderBy('m.ordem')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		foreach($dql as $i)
			$this->cache[$i['id']] = $i;
	}

	function getId($itemid)
	{
		if(isset($this->cache[$itemid]))		
			return $this->cache[$itemid];
		else
			return false;
	}
	
	function getParams($itemid)
	{
		if(BW_ADM)
			return array();
		
		//if(!bwRequest::getVar('itemid', false))
		//	return array();

		$menu = $this->getId($itemid);
		if($menu['params'])
			parse_str($menu['params'], $params);
		else
			$params = array();
		
		return $params;
	}
}

?>
