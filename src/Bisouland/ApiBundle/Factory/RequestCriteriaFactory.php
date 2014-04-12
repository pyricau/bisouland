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

        $createUserCriteria->username = $request->request->get('username');
        $createUserCriteria->plainPassword = $request->request->get('plain_password');

        return $createUserCriteria;
    }
}
