<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bisouland\BeingsBundle\Entity\Factory\KissFactory;
use Bisouland\BeingsBundle\Controller\SelectionController;

class KissController extends Controller
{
    public static $flashKeyReport = 'kissReport';
    public static $flashKeyKisserName = 'kisserName';
    public static $flashKeyKissedName = 'kissedName';
    public static $flashKeyIsCritical = 'isCritical';
    public static $flashKeyHasKissed = 'hasKissed';
    public static $flashKeyKisserEarning = 'kisserEarning';
    public static $flashKeyKissedLoss = 'kissedLoss';

    /**
     * @Route("/embrasser/{kissedName}", name="kiss")
     */
    public function indexAction($kissedName)
    {
        $kissFactory = new KissFactory($this->getDoctrine());
        $session = $this->getRequest()->getSession();
        $kiss = $kissFactory->make(
                $session->get(SelectionController::$sessionKey),
                $kissedName
        );

        $session->setFlash(self::$flashKeyReport, true);
        $session->setFlash(self::$flashKeyKisserName, $kiss->getKisser()->getName());
        $session->setFlash(self::$flashKeyKissedName, $kiss->getKissed()->getName());
        $session->setFlash(self::$flashKeyIsCritical, $kiss->getIsCritical());
        $session->setFlash(self::$flashKeyHasKissed, $kiss->getHasKissed());
        $session->setFlash(self::$flashKeyKisserEarning, $kiss->getKisserEarning());
        $session->setFlash(self::$flashKeyKissedLoss, $kiss->getKissedLoss());
        
        return $this->redirect($this->generateUrl('beings'));
    }    
}
