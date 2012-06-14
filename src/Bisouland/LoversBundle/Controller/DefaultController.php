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

        $today = new \DateTime();
        $lovers = $entityManager
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findAll();
        foreach ($lovers as $lover) {
            $lover->setLifePoints($lover->getLifePoints());
            $entityManager->persist($lover);
            $entityManager->flush();
        }

        $displayInOnePage = $entityManager
                ->getRepository('BisoulandLoversBundle:Lover')
                ->count();

        $loversQuery = $entityManager
                ->getRepository('BisoulandLoversBundle:Lover')
                ->findAllAsQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $loversQuery,
                $this->get('request')->query->get('page', 1),
                $displayInOnePage
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
