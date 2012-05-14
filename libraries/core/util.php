<?php

defined('BW') or die("Acesso negado!");

class bwUtil
{
    /**
     * Cria um valor visivel, mas seguro
     * ********************************************* */
    public function createSafeValue($value, $customKey = '', $randKey = NULL)
    {    
        //
        if(is_null($randKey))
            $randKey = strtoupper(substr(sha1(rand()), 0, 10));
        
        //
        $customKey = strtoupper(sha1($customKey));

        //
        $sha1 = strtoupper(substr(sha1("SAFE::VALUE::$randKey::$value::$customKey"), 0, 10));
        
        //
        $safeValue = sprintf('%s|%s|%s', $randKey, $sha1, $value);
        
        return $safeValue;
    }
    
    
    /**
     * Pega o valor, se a chave for verdadeira
     * retorna NULL caso a chave for false
     * ********************************************* */
    public function getSafeValue($safeValue, $customKey = NULL)
    {
        $a = explode('|', $safeValue);
        $safeValue2 = bwUtil::createSafeValue($a[2], $customKey, $a[0]);
      
        //echo "$safeValue<br>$safeValue2";
        //var_dump($safeValue == $safeValue2); // ? $a[2] : NULL;
        
        //
        return ($safeValue == $safeValue2) ? $a[2] : NULL;
    }
    
    

    /**
     * Convert números BR para decimail para o MySql
     * Ex: 1.253,07 => 1253.07, 14.352 => 14352.00
     * ********************************************* */
    public function stringToDecimal($str, $casas_decimais = 2)
    {
        $v = (float) str_replace(',', '.', str_replace('.', '', $str));
        $v = number_format($v, $casas_decimais, '.', '');
        
        return $v;
    }


    /**
     * Convert números float para formato R$ 0,00
     * Ex: 1253.07 => 1.253,07, 14352.00 => 14.352,00
     * ********************************************* */
    public function floatToString($float, $casas_decimais = 2)
    {
        $v = number_format($float, $casas_decimais, ',', '.');
        return $v;
    }


    /**
     * Converte acentos de uma string para html
     * ********************************************* */
    public function acentos2Html($str)
    {
        $ascii = array
            (
            "´", "~", "`", "ç", "^", "'", "á", "é", "í", "ó", "ú", "Á",
            "É", "Í", "Ó", "Ú", "ã", "õ", "Ã", "Õ", "à", "è", "ì", "ò", "ù",
            "À", "È", "Ì", "Ò", "Ù", "â", "ê", "î", "ô", "û", "Â", "Ê", "Î", "Ô", "Û"
        );
        $html = array
            (
            "&cute;", "&tilde;", "&grave;", "&ccedil;", "&circ;", "&acute;",
            "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&Aacute;",
            "&Eacute", "&Iacute;", "&Oacute;", "&Uacute;", "&atilde;", "&otilde;",
            "&Atilde;", "&Otilde;", "&agrave;", "&egrave;", "&igrave;", "&ograve;",
            "&ugrave;", "&Agrave;", "&Egrave;", "&Igrave;", "&Ograve;", "&Ugrave;",
            "&acirc;", "&ecirc;", "&icirc;", "&ocirc;", "&ucirc;", "&Acirc;", "&Ecirc;", "&Icirc;", "&Ocirc;", "&Ucirc;"
        );
        return str_replace($ascii, $html, $str);
    }

    function stringURLSafe($str)
    {
        //remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $str);

        // remove os acentos
        $str = bwUtil::removeAcentos($str);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));
        return $str;
    }

    function alias($str1, $str2 = '')
    {
        $str = bwUtil::stringURLSafe($str1);

        if ($str != '')
            return $str;
        else
            return bwUtil::stringURLSafe($str2);
    }

    function resizeImage($tag)
    {
        bwLoader::import('plugins.resizeimage.helper');

        return bwPluginResizeImageHelper::_($tag);
    }

    function removeAcentos($var)
    {
        $acentos = array('Á', 'À', 'Â', 'Ã', 'á', 'à', 'â', 'ã', 'É', 'È', 'Ê', 'Ẽ', 'é', 'è', 'ê', 'ẽ',
            'Ó', 'Ò', 'Ô', 'Õ', 'ó', 'ò', 'ô', 'õ', 'º', 'Ú', 'Ù', 'Û', 'Ũ', 'ú', 'ù', 'û', 'ũ', 'Ç', 'ç');

        $letras = array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'E', 'E', 'E', 'E', 'e', 'e', 'e', 'e',
            'O', 'O', 'O', 'O', 'o', 'o', 'o', 'o', 'o', 'U', 'U', 'U', 'U', 'u', 'u', 'u', 'u', 'C', 'c');

        return str_replace($acentos, $letras, $var);
    }


    function ampReplace($text)
    {
        $text = str_replace('&&', '*--*', $text);
        $text = str_replace('&#', '*-*', $text);
        $text = preg_replace('|&(?![\w]+;)|', '&amp;', $text);
        $text = str_replace('&amp;', '&', $text);
        $text = str_replace('*-*', '&#', $text);
        $text = str_replace('*--*', '&&', $text);

        return $text;
    }

    /**
     * Cleans text of all formating and scripting code
     */
    function cleanText(&$text)
    {
        $text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
        $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        $text = preg_replace('/<!--.+?-->/', '', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = preg_replace('/&nbsp;/', ' ', $text);
        $text = preg_replace('/&amp;/', ' ', $text);
        $text = preg_replace('/&quot;/', ' ', $text);
        $text = strip_tags($text);
        $text = htmlspecialchars($text);
        return $text;
    }

    /**
     * Retorna valor seguro para o banco de dados
     * ********************************************* */
    public function quote($value, $trim = true)
    {
        if ($trim)
            $value = trim($value);

        $value = addslashes($value);
        return $value;
    }

    /**
     * Retorna valor inteiro
     * ********************************************* */
    public function int($int, $default = 0)
    {
        if (preg_match("#^\d*$#", $int) && !empty($int))
            return $int;
        else
            return $default;
    }

    /**
     * Converte data mysql p/ normal e vice-versa
     * ********************************************* */
    function converteData($data, $h = true)
    {
        @list($data, $hora) = explode(' ', $data);

        if (strpos($data, "/") === false)
            $data = join("/", array_reverse(explode("-", $data)));
        else
            $data = join("-", array_reverse(explode("/", $data)));

        if ($hora && $h)
            return "{$data} {$hora}";
        else
            return $data;
    }

    /**
     * Alias da função converteData
     * ********************************************* */
    function data($data, $h = true)
    {
        return bwUtil::converteData($data, $h);
    }

    /**
     * Converte a data para o formato do mysql
     * retorna false em caso de erro
     * ********************************************* */
    function dataToMysql($data)
    {    
      if (preg_match('#^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$#', $data))
        return bwUtil::data($data, true);

      elseif(preg_match('#^\d{2}\/\d{2}\/\d{4}$#', $data))
        return bwUtil::data($data, false);

      elseif(preg_match('#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#', $data))
        return $data;

      elseif(preg_match('#^\d{4}-\d{2}-\d{2}$#', $data))
        return $data;
        
      else
        return false;
    }

    /**
     * Remove códigos Html da string
     * ********************************************* */
    public function removeHtml($s)
    {
        return htmlspecialchars(bwUtil::quote($s));
    }

    /**
     * Redireciona a página
     * ********************************************* */
    public function redirect($url, $router = true)
    {
        if ($router)
            $url = bwRouter::_($url);
    
        header('Location: ' . $url);
        exit();
    }

    /**
     * Converte float => R$ 999.999,99
     * ********************************************* */
    public function float2rs($float, $decimals=2, $prefix='R$ ')
    {
        return $prefix . number_format($float, $decimals, ',', '.');
    }

    /**
     * Converte o cep para "00000000" => 000-00000"
     * ******************************************************** */
    public function cep($t)
    {
        $str = "$t[0]$t[1]$t[2]-$t[3]$t[4]$t[5]$t[6]$t[7]";

        return $str;
    }

    /**
     * Converte telefone "00 00000000" => "(00) 0000-0000"
     * ******************************************************** */
    public function telefone($telefone)
    {
        $t = str_ireplace(' ', '', $telefone);
        $telefone = "($t[0]$t[1]) $t[2]$t[3]$t[4]$t[5]-$t[6]$t[7]$t[8]$t[9]";

        return $telefone;
    }

    /**
     * Quebra string por palavras
     * ********************************************* */
    public function truncate($string, $length = 160, $break_words = false)
    {
        if ($length == 0)
            return '';

        if (strlen($string) > $length)
        {
            if (!$break_words)
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));

            return substr($string, 0, $length) . '...';
        } else
            return $string;
    }

    /**
     * Verifica se $str está com encoding latin1
     * ********************************************* */
    public function isLatin1($str)
    {
        return (preg_match("/^[\\x00-\\xFF]*$/u", $str) === 1);
    }

    /**
     * Converte para utf-8 se $str está com encoding latin1
     * ********************************************* */
    public function convertIsLatin1($str)
    {
        $encoding = mb_detect_encoding($str, "UTF-8, ISO-8859-1, Latin1");

        if (!preg_match("/UTF-8|utf-8/", $encoding))
            return iconv($encoding, "UTF-8", $str);
        else
            return $str;
    }

    /**
     * Data atual formato
     * ********************************************* */
    public function dataNow($mysql = true, $hora = true)
    {
        if($hora)
            $data = date("Y-m-d H:i:s");
        else
            $data = date("Y-m-d");
        
        if($mysql)
            return $data;
        else
            return bwUtil::data($data);
    }
    
    /**
     * Executa um arquivo php e retorna o conteúdo
     * ********************************************* */
    public function execPHP($file, $vars = array())
    {
        if (bwFile::exists($file))
        {
            ob_start();

            extract($vars);

            require($file);
            
            return ob_get_clean();
        }
        else
            bwError::show("Arquivo '{$file}' não foi encontrado!");
    }
    

    /**
     * Retorna o o IP real do usuário
     * ********************************************* */    
    public function getIpReal()
    {
        //check ip from share internet
        //if (!empty($_SERVER['HTTP_CLIENT_IP']))   
        //  $ip=$_SERVER['HTTP_CLIENT_IP'];
        
        //to check ip is pass from proxy  
        //elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
        //  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
          
        //else
          $ip=$_SERVER['REMOTE_ADDR'];
          
        return $ip;
    }   
    
    /**
     * Convert array para query_string
     * ********************************************* */    
    public function array2query($array)
    {
        return http_build_query($array, '', '&');
    }       

    /**
     * Convert query_string para array
     * ********************************************* */    
    public function query2string($query)
    {
        parse_str($query, $array);
        return $array;
    }
    
    public function array2csv($data)
    {
        $outstream = fopen("php://temp", 'r+');
        fputcsv($outstream, $data, ',', '"');
        rewind($outstream);
        $csv = fgets($outstream);
        fclose($outstream);
        return $csv;
    }

    public function csv2array($data)
    {
        $csv = array();
        foreach(explode("\n", $data) as $data)
        {
            if(trim($data) != '')
            {
                $instream = fopen("php://temp", 'r+');
                fwrite($instream, $data);
                rewind($instream);
                $csv[] = fgetcsv($instream, 0, ',', '"');
                fclose($instream);
            }
        }
        
        return($csv);
    }


    
    /**
     * Verifica se a string é um e-mail válido
     * ********************************************* */        
    public function isEmail($string)
    {
        return preg_match('#^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@(([0-9a-zA-Z])+([-\w]*[0-9a-zA-Z])*\.)+[a-zA-Z]{2,9})$#', $string);
        //return filter_var($string, FILTER_VALIDATE_EMAIL);
    }    
    

    /**
     * Convert Array para Object
     * ********************************************* */  
    function array2object($array) {
        if(!is_array($array)) {
            return $array;
        }
        
        $object = new stdClass();
        if (is_array($array) && count($array) > 0) {
          foreach ($array as $name=>$value) {
             $name = trim($name);
             if (!empty($name)) {
                $object->$name = bwUtil::array2object($value);
             }
          }
          return $object;
        }
        else {
          return FALSE;
        }
    }    
    
}
?>
