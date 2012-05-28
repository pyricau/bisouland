<?php

namespace Bisouland\LoversBundle\Exception;

class InvalidKissedException extends \InvalidArgumentException
{
    public $message = 'Kissed Being cannot be found in database';
}
