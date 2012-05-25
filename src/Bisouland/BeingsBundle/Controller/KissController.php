<?php

namespace Bisouland\BeingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bisouland\BeingsBundle\Entity\Factory\KissFactory;
use Bisouland\BeingsBundle\Controller\SelectionController;

use Bisouland\BeingsBundle\Exception\InvalidKisserException;
use Bisouland\BeingsBundle\Exception\InvalidKissedException;
use Bisouland\BeingsBundle\Exception\InvalidKisserAsKissedException;
use Bisouland\BeingsBundle\Exception\OverflowKissException;

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
        } catch (\Exception $e) {
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
    
    private function setErrorFlash(\Exception $e)
    {
        $message = '';
        if ($e instanceof InvalidKisserException) {
            $message = 'Vous ne pouvez embrasser avec un amoureux qui n\'existe pas';
        }
        if ($e instanceof InvalidKissedException) {
            $message = 'Vous ne pouvez embrasser un amoureux qui n\'existe pas';
        }
        if ($e instanceof InvalidKisserAsKissedException) {
            $message = 'Vous ne pouvez vous embrasser vous m&ecirc;me';
        }
        if ($e instanceof OverflowKissException) {
            $message = sprintf(
                    'Vous ne pouvez pas embrasser plus de %s fois le m&ecirc;me amoureux en moins de %s heures',
                    KissFactory::$quotaOfKiss,
                    KissFactory::$quotaOfSeconds / 60 /60
            );
        }

        $session = $this->getRequest()->getSession();

        $session->setFlash(self::$flashKeyHasError, true);
        $session->setFlash(self::$flashKeyMessageError, $message);
    }
      
}
