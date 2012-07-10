<?php

namespace Bisouland\BeingsBundle\Repository;

use Bisouland\BeingsBundle\Repository\EntityRepositoryWithExceptionManagement;

class KissRepository extends EntityRepositoryWithExceptionManagement
{
    public function countForLastGivenSeconds($kisserId, $kissedId, $numberOfSeconds)
    {
        $query = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('COUNT(bisouland_kiss.id)')
                ->from('BisoulandBeingsBundle:Kiss', 'bisouland_kiss')
                ->where('bisouland_kiss.kisser = :kisser_id')
                ->andWhere('bisouland_kiss.kissed = :kissed_id')
                ->andWhere('UNIX_TIMESTAMP(bisouland_kiss.created) > :last24Hours')
                ->setParameters(array(
                    'kisser_id' => $kisserId,
                    'kissed_id' => $kissedId,
                    'last24Hours' => time() - $numberOfSeconds,
                ))
                ->getQuery();
        
        return $this->tryToGetSingleScalarResultWithDefaultOnFailure($query);
    }
}
