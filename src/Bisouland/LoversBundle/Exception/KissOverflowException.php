<?php

namespace Bisouland\LoversBundle\Exception;

class KissOverflowException extends \OverflowException
{
    public $message = 'Number of kiss exceeds the quota';
}
