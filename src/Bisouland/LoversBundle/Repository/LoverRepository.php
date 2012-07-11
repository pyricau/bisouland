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
                ->from('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function countLoversGeneratedToday()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_lover.id)')
                ->from('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->where('bisouland_lover.created >= CURRENT_DATE()')
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function getBonusStatistics()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('AVG(bisouland_lover.seduction_bonus) AS average_seduction'
                        .', AVG(bisouland_lover.heart_bonus) AS average_heart'
                        .', AVG(bisouland_lover.tongue_bonus) AS average_tongue'
                        .', AVG(bisouland_lover.slap_bonus) AS average_slap'
                        .', MIN(bisouland_lover.seduction_bonus) AS minimum_seduction'
                        .', MIN(bisouland_lover.heart_bonus) AS minimum_heart'
                        .', MIN(bisouland_lover.tongue_bonus) AS minimum_tongue'
                        .', MIN(bisouland_lover.slap_bonus) AS minimum_slap'
                        .', MAX(bisouland_lover.seduction_bonus) AS maximum_seduction'
                        .', MAX(bisouland_lover.heart_bonus) AS maximum_heart'
                        .', MAX(bisouland_lover.tongue_bonus) AS maximum_tongue'
                        .', MAX(bisouland_lover.slap_bonus) AS maximum_slap')
                ->from('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->getQuery();

        return $query->getArrayResult($query);
    }

    public function removeLoversWithNoMoreLovePoints()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->delete('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->where('bisouland_lover.love_points <= :now - UNIX_TIMESTAMP(bisouland_lover.updated)')
                ->setParameter(':now', time())
                ->getQuery();

        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }

    public function findLastOne()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_lover')
                ->from('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->orderBy('bisouland_lover.id', 'desc')
                ->setMaxResults(1)
                ->getQuery();

        return $query->getSingleResult();
    }

    public function findAllAsQuery()
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('bisouland_lover')
                ->from('BisoulandGameSystemBundle:Lover', 'bisouland_lover')
                ->getQuery();

        return $query;
    }
}
