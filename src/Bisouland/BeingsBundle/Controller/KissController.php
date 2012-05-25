<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bisouland\BeingsBundle\Entity\Factory\KissFactory;
use Bisouland\BeingsBundle\Controller\SelectionController;

use Bisouland\BeingsBundle\Exception\InvalidKisserException;
use Bisouland\BeingsBundle\Exception\InvalidKissedException;
use Bisouland\BeingsBundle\Exception\InvalidKisserAsKissedException;

class KissController extends Controller
{
    public static $flashKeyReport = 'kissReport';
    public static $flashKeyKisserName = 'kisserName';
    public static $flashKeyKissedName = 'kissedName';
    public static $flashKeyIsCritical = 'isCritical';
    public static $flashKeyHasKissed = 'hasKissed';
    public static $flashKeyKisserEarning = 'kisserEarning';
    public static $flashKeyKissedLoss = 'kissedLoss';
    
    public static $flashKeyHasError = 'hasKissError';
    public static $flashKeyMessageError = 'kissMessageError';

    /**
     * @Route("/embrasser/{kissedName}", name="kiss")
     */
    public function indexAction($kissedName)
    {
        $kissFactory = new KissFactory($this->getDoctrine());

        try {
            $this->setReportFlash($kissFactory->make(
                    $this->getRequest()->getSession()->get(SelectionController::$sessionKey),
                    $kissedName
            ));
        } catch (\InvalidArgumentException $e) {
            $this->setErrorFlash($e);
        }
        
        return $this->redirect($this->generateUrl('beings'));
    }
    
    private function setReportFlash($kiss)
    {
        $session = $this->getRequest()->getSession();
        $session->setFlash(self::$flashKeyReport, true);

        $session->setFlash(self::$flashKeyKisserName, $kiss->getKisser()->getName());
        $session->setFlash(self::$flashKeyKissedName, $kiss->getKissed()->getName());
        $session->setFlash(self::$flashKeyIsCritical, $kiss->getIsCritical());
        $session->setFlash(self::$flashKeyHasKissed, $kiss->getHasKissed());
        $session->setFlash(self::$flashKeyKisserEarning, $kiss->getKisserEarning());
        $session->setFlash(self::$flashKeyKissedLoss, $kiss->getKissedLoss());
    }
    
    private function setErrorFlash(\InvalidArgumentException $e)
    {
        $message = '';
        if ($e instanceof InvalidKisserException) {
            $message = 'Embrasseur non valide';
        }
        if ($e instanceof InvalidKissedException) {
            $message = 'Embrass&eacute; non valide';
        }
        if ($e instanceof InvalidKisserAsKissedException) {
            $message = 'Vous ne pouvez vous embrasser vous m&ecirc;me';
        }

        $session = $this->getRequest()->getSession();

        $session->setFlash(self::$flashKeyHasError, true);
        $session->setFlash(self::$flashKeyMessageError, $message);
    }
      
}
