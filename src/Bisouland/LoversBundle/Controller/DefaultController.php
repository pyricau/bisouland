<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="lovers")
     * @Template()
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getEntityManager();

        $lovers = $entityManager
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findAll();
        $numberOfLovers = count($lovers);

        foreach ($lovers as $lover) {
            $lover->setLifePoints($lover->getLifePoints());
            $entityManager->persist($lover);
            $entityManager->flush();
        }

        $loversQuery = $entityManager
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findAllAsQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $loversQuery,
                $this->get('request')->query->get('page', 1),
                $numberOfLovers
        );
        $pagination->setTemplate('BisoulandLoversBundle:Pagination:pagination.html.twig');
        $pagination->setSortableTemplate('BisoulandLoversBundle:Pagination:sortable.html.twig');

        return compact('pagination');
    }
    
    /**
     * @Route("/{name}", name="lovers_view")
     * @Template()
     */
    public function viewAction($name)
    {
        $lover = $this->getDoctrine()
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findOneByName($name);

        return array('lover' => $lover);
    }
}
