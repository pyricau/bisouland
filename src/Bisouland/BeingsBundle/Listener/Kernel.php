<?php

namespace Bisouland\BeingsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class Kernel
{
    private $session;

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
    }
}
