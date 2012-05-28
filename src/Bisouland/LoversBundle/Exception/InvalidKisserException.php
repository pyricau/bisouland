<?php

namespace Bisouland\LoversBundle\Exception;

class InvalidKisserException extends \InvalidArgumentException
{
    public $message = 'Kisser cannot be found in database';
}
