<?php

namespace Bisouland\BeingsBundle\Exception;

class OverflowKissException extends \OverflowException
{
    public $message = 'Number of kiss exceeds the quota';
}
