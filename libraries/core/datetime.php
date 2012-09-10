<?php

defined('BW') or die('Acesso negado!');

class bwDateTime extends DateTime
{

    public function __construct($date = 'now', $object = NULL)
    {
        // converte o formato do Brasil para mysql
        if (preg_match('#^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$#', $date)) {
            list($d, $h) = explode(' ', $date);
            $date = join("-", array_reverse(explode("/", $d))) . ' ' . $h;
        } elseif (preg_match('#^\d{2}\/\d{2}\/\d{4}$#', $date)) {
            $date = join("-", array_reverse(explode("/", $date)));
        }

        return parent::__construct($date, $object);
    }

    public function check($params = array())
    {
        list($year, $month, $day) = explode('-', $this->format('Y-m-d'));

        $params = array_merge(array(
            'year_min' => 1850,
            'block_current_day' => false,
            'block_current_month' => false,
            'block_current_year' => false,
            ), $params);

        extract($params);

        if (!checkdate($month, $day, $year)) {
            return false;
        }

        if ($year < $year_min) {
            return false;
        }

        if ($block_current_year) {
            if ($year > date('Y')) {
                return false;
            }
        }

        if ($block_current_month) {
            if ($month > date('m') && $year == date('Y')) {
                return false;
            }
        }

        if ($block_current_day) {
            if ($day > date('d') && $month == date('m') && $year == date('Y')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retorna data no formato desejado (traduzido para pt_BR)
     * 
     * Exemplo:
     * <pre>
     *  - F Junho, Agosto, Dezembro, ...
     *  - M Jun, Ago, Dez, ...
     *  - l Domingo, Sábado, Sexta-feira
     *  - D Dom, Sab, Sex
     * </pre>
     * 
     * @link http://php.net/manual/en/class.datetime.php
     * @link http://br.php.net/manual/pt_BR/function.date.php
     * 
     * @param string $format
     * @return string 
     */
    public function format($format)
    {
        $date = parent::format($format);

        $days_pt = array('Domingo', 'Segunda-feira', 'Terça-feira',
            'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');

        $days_en = array('Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday');

        $months_pt = array('Janeiro', 'Fevereiro', 'Março', 'Abril',
            'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro',
            'Novembro', 'Dezembro');

        $months_en = array('January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August', 'September', 'October',
            'November', 'December');

        $days_short_pt = array('Dom', 'Seg', 'Ter',
            'Qua', 'Qui', 'Sex', 'Sab');

        $days_short_en = array('Sun', 'Mon', 'Tue',
            'Wed', 'Thu', 'Fri', 'Sat');

        $months_short_pt = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');

        $months_short_en = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        $pt = array_merge($months_pt, $days_pt, $months_short_pt, $days_short_pt);
        $en = array_merge($months_en, $days_en, $months_short_en, $days_short_en);

        foreach ($pt as $k => $v) {
            $date = str_replace($en[$k], $pt[$k], $date);
        }

        return $date;
    }

    /**
     * Retorna string no formato MySql
     *
     * @param boolean $time
     * @return string 
     */
    public function toMysql($time = true)
    {
        $time = (bool) $time ? ' H:i:s' : '';
        $format = 'Y-m-d' . $time;

        return $this->format($format);
    }

}

?>
