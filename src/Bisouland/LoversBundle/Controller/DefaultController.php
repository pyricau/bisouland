<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\LoversBundle\Form\LevelUpForm;

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
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->findAll();
        $numberOfLovers = count($lovers);

        foreach ($lovers as $lover) {
            $lover->setLovePoints($lover->getLovePoints());
            $entityManager->persist($lover);
        }
        $entityManager->flush();


        $loversQuery = $entityManager
                ->getRepository('BisoulandGameSystemBundle:Lover')
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
                ->getRepository('BisoulandGameSystemBundle:Lover')
                ->findOneByName($name);

        $levelUpform = $this->createForm(new LevelUpForm());

        return array(
            'lover' => $lover,
            'form' => $levelUpform->createView(),
        );
    }

    /**
     * @Route("/{name}/amelioration", name="lover_level_up")
     */
    public function levelUpAction($name)
    {
        $levelUpform = $this->createForm(new LevelUpForm());
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $levelUpform->bindRequest($request);

            if ($levelUpform->isValid()) {
                $lover = $this->getDoctrine()
                        ->getRepository('BisoulandGameSystemBundle:Lover')
                        ->findOneByName($name);

                $lover->setLevel($lover->getLevel() + 1);
                $lover->setLovePoints($lover->getLovePoints() - $lover->getNextLevelCost());
                $bonus = $levelUpform['levelUp']->getData();
                switch ($bonus) {
                    case '0':
                        $lover->setSeductionBonus($lover->getSeductionBonus() + 1);
                        break;
                    case '1':
                        $lover->setTongueBonus($lover->getTongueBonus() + 1);
                        break;
                    case '2':
                        $lover->setDodgeBonus($lover->getDodgeBonus() + 1);
                        break;
                    case '3':
                        $lover->setSlapBonus($lover->getSlapBonus() + 1);
                        break;
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($lover);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('lovers_view', array('name' => $name)));
    }
}
