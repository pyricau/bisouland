<?php

namespace Bisouland\BeingsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BeingRepository extends EntityRepository
{
    public function count()
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_being.id)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->getQuery()
                ->getSingleScalarResult();
    }
    
    public function countBirthsToday()
    {
        $today = date('Y/m/d 00:00:00');

        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_being.id)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->where('bisouland_being.created >= :today')
                ->setParameter('today', $today)
                ->getQuery()
                ->getSingleScalarResult();
    }
}
