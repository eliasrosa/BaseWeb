<?php
class Doctrine_Validator_Notbezeroorsmaller extends Doctrine_Validator_Driver
{
    public function validate($value)
    {
      return ($value > 0);
    }
}