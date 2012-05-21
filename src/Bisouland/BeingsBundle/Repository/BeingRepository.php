<?php

namespace Bisouland\BeingsBundle\Repository;

use Bisouland\BeingsBundle\Repository\EntityRepositoryWithExceptionManagement;

class BeingRepository extends EntityRepositoryWithExceptionManagement
{
    public function countAlivePopulation()
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
    
    public function countTotalBirths()
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
    
    public function updateLovePoints()
    {
        $this->getEntityManager()
                ->createQueryBuilder()
                ->update('BisoulandBeingsBundle:Being', 'bisouland_being')
                ->set('bisouland_being.love_points', '(bisouland_being.love_points - :now + UNIX_TIMESTAMP(bisouland_being.updated))')
                ->setParameter(':now', time())
                ->getQuery()
                ->execute();
    }
    
    public function removeLosers()
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
