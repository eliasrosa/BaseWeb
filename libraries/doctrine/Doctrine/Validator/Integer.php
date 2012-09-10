<?php

class Doctrine_Validator_Integer extends Doctrine_Validator_Driver
{
    public function validate($value)
    {
        if (is_null($value)) {
            return true;
        }

        return preg_match("/^-?\d+$/", $value);
    }
    

}