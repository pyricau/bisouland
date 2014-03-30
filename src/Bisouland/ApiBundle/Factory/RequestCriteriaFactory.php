<?php

namespace Bisouland\ApiBundle\Factory;

use Bisouland\ApiBundle\Criteria\CreateUserCriteria;
use Symfony\Component\HttpFoundation\Request;

class RequestCriteriaFactory
{
    /**
     * @param Request $request
     *
     * @return CreateUserCriteria
     */
    public function makeCreateUser(Request $request)
    {
        $createUserCriteria = new CreateUserCriteria();
        $formData = json_decode($request->getContent(), true);

        $createUserCriteria->username = $formData['username'];
        $createUserCriteria->plainPassword = $formData['plain_password'];

        return $createUserCriteria;
    }
}
