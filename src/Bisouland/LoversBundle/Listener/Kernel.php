<?php

namespace Bisouland\LoversBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

use Bisouland\LoversBundle\Listener\OnEvent\Deletion;
use Bisouland\LoversBundle\Listener\OnEvent\Generation;
use Bisouland\LoversBundle\Listener\OnEvent\Attribution;

class Kernel
{
    private $loverDeletion;
    private $loverGeneration;
    private $loverAttribution;
    private $session;

    public function __construct(Deletion $loverDeletion, Generation $loverGeneration, Attribution $loverAttribution)
    {
        $this->loverDeletion = $loverDeletion;
        $this->loverGeneration = $loverGeneration;
        $this->loverAttribution = $loverAttribution;
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
        $this->loverDeletion->make();
        $this->loverGeneration->make();
        $this->loverAttribution->make();
    }
}
