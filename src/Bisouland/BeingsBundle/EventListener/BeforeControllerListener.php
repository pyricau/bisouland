<?php

namespace Bisouland\BeingsBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BeforeControllerListener
{
    private $annotation_reader;

    public function __construct($annotation_reader)
    {   
        $this->annotation_reader = $annotation_reader;
    }   

    public function onKernelController(FilterControllerEvent $event)
    {   
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }   

        $controllerObject = $controller[0];
        $class = new \ReflectionClass(get_class($controllerObject));

        $before = $this->annotation_reader->getClassAnnotation($class, 'Bisouland\BeingsBundle\Filters\Before');
        if ($before) {
            foreach ($before->getMethods() as $method) {
                $class->getMethod($method)->invoke($controllerObject);
            }   
        }   
    }   
}
