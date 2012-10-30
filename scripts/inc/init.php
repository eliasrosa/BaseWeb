<?

define('BW_NOT_INIT', true);
require "../index.php";

function console_log($text, $new_line = true)
{
    echo $text;

    if ($new_line) {
        echo "\n";
    }
}