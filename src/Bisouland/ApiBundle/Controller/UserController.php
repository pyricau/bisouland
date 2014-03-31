<?php

namespace Bisouland\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Method({"POST"})
     * @Route("/user", name="bisouland_api_create_user")
     */
    public function createUserAction(Request $request)
    {
        $requestCriteriaFactory = $this->get('bisouland_api.request_criteria_factory');
        $userFactory = $this->get('bisouland_api.user_factory');
        $userDataMapper = $this->get('bisouland_api.user_data_mapper');

        $createUserCriteria = $requestCriteriaFactory->makeCreateUser($request);

        $user = $userFactory->make($createUserCriteria);
        $userDataMapper->insert($user);

        return $this->renderCreated(array(
            'username' => $user->getUsername(),
        ));
    }

    /**
     * @param array $data
     *
     * @return JsonResponse
     */
    private function renderCreated(array $data)
    {
        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }
}
