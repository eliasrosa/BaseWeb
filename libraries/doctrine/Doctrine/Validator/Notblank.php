<?php
class Doctrine_Validator_Notblank extends Doctrine_Validator_Driver
{
    public function validate($value)
    {
        if ((!is_float($value)) && empty($value) || is_null($value)) {
            return false;
        }

        return true;
    }
}