<?php

namespace Bisouland\BeingsBundle\Exception;

class InvalidKisserAsKissedException extends \InvalidArgumentException
{
    public $message = 'Kisser and Kissed Being cannot be the same';
}
