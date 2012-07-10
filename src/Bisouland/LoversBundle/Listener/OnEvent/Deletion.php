<?php

namespace Bisouland\LoversBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

class Deletion
{
    public static $flashKey = 'numberOfRemovedLovers';

    private $doctrine;
    private $session;

    public function __construct(ManagerRegistry $doctrine, Session $session)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }

    public function make()
    {
        $numberOfRemovedLovers = $this->deleteLovers();

        $this->session->setFlash(self::$flashKey, $numberOfRemovedLovers);
    }

    private function deleteLovers()
    {
        return $this->doctrine
                ->getRepository('BisoulandLoversBundle:Lover')
                ->removeLoversWithNoMoreLovePoints();
    }
}
