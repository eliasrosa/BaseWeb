<?php

class Doctrine_Validator_Timestamp extends Doctrine_Validator_Driver
{
    /**
     * checks if given value is a valid ISO-8601 timestamp (YYYY-MM-DDTHH:MM:SS+00:00)
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value)
    {
        if (is_null($value)) {
            return true;
        }

        $e = explode('T', trim($value));
        $date = isset($e[0]) ? $e[0]:null;
        $time = isset($e[1]) ? $e[1]:null;

        $dateValidator = Doctrine_Validator::getValidator('date');
        $timeValidator = Doctrine_Validator::getValidator('time');

        if ( ! $dateValidator->validate($date)) {
            return false;
        }

        if ( ! $timeValidator->validate($time)) {
            return false;
        } 

        return true;
    }
}