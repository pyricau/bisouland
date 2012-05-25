<?php

namespace Bisouland\BeingsBundle\Exception;

class InvalidKisserException extends \InvalidArgumentException
{
    public $message = 'Kisser Being cannot be found in database';
}
