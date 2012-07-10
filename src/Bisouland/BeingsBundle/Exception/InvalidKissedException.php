<?php

namespace Bisouland\BeingsBundle\Exception;

class InvalidKissedException extends \InvalidArgumentException
{
    public $message = 'Kissed Being cannot be found in database';
}
