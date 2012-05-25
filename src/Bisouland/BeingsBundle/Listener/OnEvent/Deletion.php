<?php

namespace Bisouland\BeingsBundle\Listener\OnEvent;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

class Deletion
{
    public static $flashKey = 'numberOfRemovedBeings';

    private $doctrine;
    private $session;

    public function __construct(ManagerRegistry $doctrine, Session $session)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }

    public function make()
    {
        $numberOfRemovedBeings = $this->deleteBeings();

        $this->session->setFlash(self::$flashKey, $numberOfRemovedBeings);
    }

    private function deleteBeings()
    {
        return $this->doctrine
                ->getRepository('BisoulandBeingsBundle:Being')
                ->removeBeingsWithNoMoreLovePoints();
    }
}
