<?php

namespace Bisouland\LoversBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

use Bisouland\LoversBundle\Controller\SelectionController;

class Attribution
{
    public static $flashKey = 'hasAttributedLover';

    private $doctrine;
    private $session;

    public function __construct(ManagerRegistry $doctrine, Session $session)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }

    public function make()
    {
        $hasAttributedLover = false;
        if (true === $this->hasToAttributeLover()) {
            $this->setLoverNameInSessionForNewVisitor();
            $hasAttributedLover = true;
        }

        $this->session->setFlash(self::$flashKey, $hasAttributedLover);
    }

    private function hasToAttributeLover()
    {
        $hasToAttributeLover = true;
        if (true === $this->session->has(SelectionController::$sessionKey)) {
            $selectedLover = $this->doctrine
                    ->getRepository('BisoulandLoversBundle:Lover')
                    ->findOneByName($this->session->get(SelectionController::$sessionKey));
            
            $hasToAttributeLover = (null === $selectedLover);
        }

        return $hasToAttributeLover;
    }

    private function setLoverNameInSessionForNewVisitor()
    {
        $lover = $this->doctrine
            ->getRepository('BisoulandLoversBundle:Lover')
            ->findLastOne();

        $this->session->set(SelectionController::$sessionKey, $lover->getName());
    }
}
