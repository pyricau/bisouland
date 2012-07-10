<?php

namespace Bisouland\BeingsBundle\Repository;

use Bisouland\BeingsBundle\Repository\EntityRepositoryWithExceptionManagement;

class BeingRepository extends EntityRepositoryWithExceptionManagement
{
    public function count()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_being.id)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->getQuery();
        
        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }
    
    public function countBeingsGeneratedToday()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_being.id)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->where('bisouland_being.created >= CURRENT_DATE()')
                ->getQuery();
        
        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }
    
    public function findLastId()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_being.id')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->orderBy('bisouland_being.id', 'desc')
                ->setMaxResults(1)
                ->getQuery();
        
        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }
    
    public function getAverageAttributes()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('AVG(bisouland_being.seduction)'
                        .', AVG(bisouland_being.slap)'
                        .',  AVG(bisouland_being.heart)')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->getQuery();
        
        return $query->getArrayResult($query);
    }
    
    public function removeBeingsWithNoMoreLovePoints()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->delete('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->where('bisouland_being.love_points <= :now - UNIX_TIMESTAMP(bisouland_being.updated)')
                ->setParameter(':now', time())
                ->getQuery();
        
        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }
    
    public function findLastOne()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_being')
                ->from('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->orderBy('bisouland_being.id', 'desc')
                ->setMaxResults(1)
                ->getQuery();
        
        return $query->getSingleResult();
    }
}
