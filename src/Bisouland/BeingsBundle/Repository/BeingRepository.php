<?php

namespace Bisouland\BeingsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BeingRepository extends EntityRepository
{
    public function countAlivePopulation()
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
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_being.id)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->where('bisouland_being.created >= CURRENT_DATE()')
                ->getQuery()
                ->getSingleScalarResult();
    }
    
    public function countTotalBirths()
    {
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_being.id')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->orderBy('bisouland_being.id', 'desc')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
    }
    
    public function removeLosers()
    {
        $now = time();
        return $this->getEntityManager()
                ->createQueryBuilder()
                ->delete('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->where('bisouland_being.love_points <= :now - UNIX_TIMESTAMP(bisouland_being.updated)')
                ->setParameter(':now', $now)
                ->getQuery()
                ->getScalarResult();
    }
}
