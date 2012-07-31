<?php

class bwDebug extends bwObject
{

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    function is()
    {
        return bwCore::getConfig()->getValue('debug.status');
    }

    function addHeader($str)
    {
        if(bwDebug::is()){
            header('bwDebug: '. $str);
        }
    }

    function show()
    {
        if (bwDebug::is()) {
            echo '<div id="bw-debug" style="text-align: left;">';
            echo "<h2>:: Debug :: Headers</h2>";
            echo "<pre>" . htmlspecialchars(print_r($_SERVER, 1)) . "</pre>";

            echo "<h2>:: Debug :: Sessions #" . bwSession::getToken() . "</h2>";
            echo "<pre>" . htmlspecialchars(print_r($_SESSION, 1)) . "</pre>";

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

            echo '</div>';
        }
    }

}

?>
