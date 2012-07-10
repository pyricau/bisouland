<?php

namespace Bisouland\BeingsBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

use Bisouland\BeingsBundle\Controller\SelectionController;

class Attribution
{
    public static $flashKey = 'hasAttributedBeing';

    private $doctrine;
    private $session;

    public function __construct(ManagerRegistry $doctrine, Session $session)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }

    public function make()
    {
        $hasAttributedBeing = false;
        if (true === $this->hasToAttributeBeing()) {
            $this->setBeingNameInSessionForNewVisitor();
            $hasAttributedBeing = true;
        }
        
        $this->session->setFlash(self::$flashKey, $hasAttributedBeing);
    }

    private function hasToAttributeBeing()
    {
        $hasToAttributeBeing = true;
        if (true === $this->session->has(SelectionController::$sessionKey)) {
            $selectedBeing = $this->doctrine
                    ->getRepository('BisoulandBeingsBundle:Being')
                    ->findOneByName($this->session->get(SelectionController::$sessionKey));
            
            $hasToAttributeBeing = (null === $selectedBeing);
        }
        
        return $hasToAttributeBeing;
    }

    private function setBeingNameInSessionForNewVisitor()
    {
        $being = $this->doctrine
            ->getRepository('BisoulandBeingsBundle:Being')
            ->findLastOne();

        $this->session->set(SelectionController::$sessionKey, $being->getName());
    }
}
