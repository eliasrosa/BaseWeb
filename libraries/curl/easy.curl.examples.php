<?php

include('easy.curl.class.php');

// Constructor parameter optional
$curl = new cURL('http://example.com');

/* SIMPLE METHODS - These methods lets you create 1 line cURL requests */

// Simple call to remote URL
echo $curl->get('http://example.com');

// Simple post request to a domain without http:// specified
$curl->post('example.com', array('foo'=>'bar'));

// Set advanced options in simple calls
// Can use any of these flags http://uk3.php.net/manual/en/function.curl-setopt.php

$curl->get('http://example.com', array(CURLOPT_PORT => 8080));
$curl->post('http://example.com', array('foo'=>'bar'), array(CURLOPT_BUFFERSIZE => 10));


/* ADVANCE METHODS - These methods allow you to build a more complex request */

// Start session (also wipes existing/previous sessions)
$curl->create('http://example/com');

// Option & Options
$curl->option(CURLOPT_BUFFERSIZE, 10);
$curl->options(array(CURLOPT_BUFFERSIZE => 10));

// Login to HTTP user authentication
$curl->http_login('username', 'password');

// Post - If you do not use post, it will just run a GET request
$post = array('foo'=>'bar');
$curl->post($post);

// Cookies - If you do not use post, it will just run a GET request
$vars = array('foo'=>'bar');
$curl->set_cookies($vars);

// Proxy - Request the page through a proxy server
// Port is optional, defaults to 80
$curl->proxy('http://example.com', 1080);
$curl->proxy('http://example.com');

// Proxy login
$curl->proxy_login('username', 'password');

// Execute - returns responce
echo $curl->execute();


// Debug data ------------------------------------------------

// Errors
$curl->error_code; // int
$curl->error_string;

// Information
$curl->info; // array

?>