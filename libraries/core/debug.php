<?php

class bwDebug extends bwObject
{
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
 
    function show()
    {
        if (bwCore::getConfig()->getValue('debug.status'))
        {
            $contants = get_defined_constants(true);

            echo "<h2>:: Debug :: Constants</h2>";
            echo "<pre>" . htmlspecialchars(print_r($contants['user'], 1)) . "</pre>";

            bwBuffer::getInstance();

            echo "<h2>:: Debug :: Request</h2>";
            echo "<pre>" . htmlspecialchars(print_r(array(
                'bwRequests' => bwRequest::getAll(),
                'GET' => $_GET,
                'POST' => $_POST,
                'FILES' => $_FILES,
                'COOKIE' => $_COOKIE
                //'INSTANCES' => bwObject::$_instances
            ), 1)) . "</pre>";

            echo "<h2>:: Debug :: Sessions</h2>";
            echo "<pre>" . htmlspecialchars(print_r($_SESSION, 1)) . "</pre>";
        }
    }
}
?>
