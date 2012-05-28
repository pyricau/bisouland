<?php

namespace Bisouland\LoversBundle\Repository;

use Bisouland\LoversBundle\Repository\EntityRepositoryWithExceptionManagement;

class LoverRepository extends EntityRepositoryWithExceptionManagement
{
    public function count()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_lover.id)')
                ->from('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function countLoversGeneratedToday()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_lover.id)')
                ->from('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->where('bisouland_lover.created >= CURRENT_DATE()')
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function findLastId()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_lover.id')
                ->from('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->orderBy('bisouland_lover.id', 'desc')
                ->setMaxResults(1)
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function getAverageAttributes()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('AVG(bisouland_lover.attack)'
                        .', AVG(bisouland_lover.defense)'
                        .',  AVG(bisouland_lover.constitution)')
                ->from('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->getQuery();

        return $query->getArrayResult($query);
    }

    public function removeLoversWithNoMoreLovePoints()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->delete('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->where('bisouland_lover.life_points <= :now - UNIX_TIMESTAMP(bisouland_lover.updated)')
                ->setParameter(':now', time())
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function findLastOne()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_lover')
                ->from('BisoulandLoversBundle:Lover', 'bisouland_lover')
                ->orderBy('bisouland_lover.id', 'desc')
                ->setMaxResults(1)
                ->getQuery();

        return $query->getSingleResult();
    }
}
