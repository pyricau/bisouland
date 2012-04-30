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
}
