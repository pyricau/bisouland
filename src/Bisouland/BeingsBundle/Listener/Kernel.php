<?php

namespace Bisouland\BeingsBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Bisouland\BeingsBundle\Listener\OnEvent\Deletion;
use Bisouland\BeingsBundle\Listener\OnEvent\Generation;
use Bisouland\BeingsBundle\Listener\OnEvent\Attribution;
use Symfony\Component\HttpKernel\HttpKernel;

class Kernel
{
    private $beingDeletion;
    private $beingGeneration;
    private $beingAttribution;
    private $session;

    public function __construct(Deletion $beingDeletion, Generation $beingGeneration, Attribution $beingAttribution)
    {
        $this->beingDeletion = $beingDeletion;
        $this->beingGeneration = $beingGeneration;
        $this->beingAttribution = $beingAttribution;
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
        $this->beingDeletion->make();
        $this->beingGeneration->make();
        $this->beingAttribution->make();
    }
}
