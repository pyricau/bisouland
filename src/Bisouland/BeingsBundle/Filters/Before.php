<?php

namespace Bisouland\BeingsBundle\Filters;

/**
 * @Annotation
 */
class Before 
{
    private $methods;

    public function __construct(array $methods)
    {   
        $this->methods = $methods;
    }   

    public function getMethods()
    {   
        return $this->methods;
    }   
}
