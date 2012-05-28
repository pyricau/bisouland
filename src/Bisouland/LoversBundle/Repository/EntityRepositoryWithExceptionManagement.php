<?php

namespace Bisouland\LoversBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class EntityRepositoryWithExceptionManagement extends EntityRepository
{
    public function tryToGetSingleScalarResultWithDefaultOnFailure($query, $default = 0)
    {
        try {
            $result = $query->getSingleScalarResult();
        }
        catch (NoResultException $e) {
            $result = $default;
        }

        return $result;
    }
}
