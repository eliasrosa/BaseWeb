<?php

class Doctrine_Validator_Alias extends Doctrine_Validator_Driver
{
    public function validate($value)
    {
        if (is_null($value)) {
            return false;
        }

        if (empty($value)) {
            return false;
        }

        return true;
    }
    

}