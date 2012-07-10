<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Bisouland\LoversBundle\Form\LevelUpForm;

class LevelUpController extends Controller
{
    /**
     * @Route("/ameliorer/{name}/{bonus}", name="level_up")
     */
    public function indexAction($name, $bonus)
    {
        $bonuses = array(
            'seduction' => 'SeductionBonus',
            'langue' => 'TongueBonus',
            'esquive' => 'DodgueBonus',
            'baffe' => 'SlapBonus',
        );

        if (true === array_key_exists($bonus, $bonuses)) {
            $setMethod = 'set'.$bonuses[$bonus];
            $getMethod = 'get'.$bonuses[$bonus];

            $lover = $this->getDoctrine()
                    ->getRepository('BisoulandGameSystemBundle:Lover')
                    ->findOneByName($name);

            if (null !== $lover) {
                $lover->setLevel($lover->getLevel() + 1);
                $lover->setLovePoints($lover->getLovePoints() - $lover->getNextLevelCost());
                $lover->{$setMethod}($lover->{$getMethod}() + 1);

                $em = $this->getDoctrine()->getManager();
                $em->persist($lover);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('lovers_view', array('name' => $name)));
    }
}
