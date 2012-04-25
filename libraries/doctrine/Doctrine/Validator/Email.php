<?php

class Doctrine_Validator_Email extends Doctrine_Validator_Driver
{

    public function validate($value)
    {
        if (is_null($value)) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}