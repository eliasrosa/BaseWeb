<?php

class Doctrine_Validator_Datahora extends Doctrine_Validator_Driver
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

		return preg_match('#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#', $value);
    }
}