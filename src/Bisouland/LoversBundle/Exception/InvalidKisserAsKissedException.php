<?php

namespace Bisouland\LoversBundle\Exception;

class InvalidKisserAsKissedException extends \InvalidArgumentException
{
    public $message = 'Kisser and Kissed cannot be the same';
}
