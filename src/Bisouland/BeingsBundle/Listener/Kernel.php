<?php

namespace Bisouland\BeingsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Bisouland\BeingsBundle\Listener\OnEvent\Generation;
use Symfony\Component\HttpKernel\HttpKernel;

class Kernel
{
    private $beingGeneration;
    private $session;
    
    public function __construct(Generation $beingGeneration)
    {
        $this->beingGeneration = $beingGeneration;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $this->session = $event->getRequest()->getSession();
        
        $requestType = $event->getRequestType();
        
        $events = array(HttpKernel::MASTER_REQUEST => 'onMasterRequest');
        if (true === array_key_exists($requestType, $events)) {
            $this->{$events[$requestType]}();
        }
    }
    
    private function onMasterRequest()
    {
        $this->beingGeneration->make();
    }
}
