<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bisouland\LoversBundle\Entity\Factory\KissFactory as OldKissFactory;
use Bisouland\GameSystemBundle\Entity\Factory\KissFactory;
use Bisouland\LoversBundle\Controller\SelectionController;

use Bisouland\GameSystemBundle\Exception\InvalidLoverNameException;
use Bisouland\GameSystemBundle\Exception\InvalidSelfKissingException;
use Bisouland\GameSystemBundle\Exception\KissOverflowException;

class KissController extends Controller
{
    public static $flashKeyReport = 'kissReport';
    public static $flashKeyKisserName = 'kisserName';
    public static $flashKeyKissedName = 'kissedName';
    public static $flashKeyIsCritical = 'isCritical';
    public static $flashKeyHasKissed = 'hasKissed';
    public static $flashKeyDamages = 'damages';
    
    public static $flashKeyHasError = 'hasKissError';
    public static $flashKeyMessageError = 'kissMessageError';

    /**
     * @Route("/embrasser/{kissedName}", name="kiss")
     */
    public function indexAction($kissedName)
    {
        $kissFactory = new OldKissFactory($this->getDoctrine(), $this->get('bisouland_game_system.kiss_factory'));

        try {
            $this->setReportFlash($kissFactory->make(
                    $this->getRequest()->getSession()->get(SelectionController::$sessionKey),
                    $kissedName
            ));
        } catch (\Exception $e) {
            $this->setErrorFlash($e);
        }
        
        return $this->redirect($this->generateUrl('lovers'));
    }
    
    private function setReportFlash($kiss)
    {
        $session = $this->getRequest()->getSession();
        $session->setFlash(self::$flashKeyReport, true);

        $session->setFlash(self::$flashKeyKisserName, $kiss->getKisser()->getName());
        $session->setFlash(self::$flashKeyKissedName, $kiss->getKissed()->getName());
        $session->setFlash(self::$flashKeyIsCritical, $kiss->getIsCritical());
        $session->setFlash(self::$flashKeyHasKissed, $kiss->getHasSucceeded());
        $session->setFlash(self::$flashKeyDamages, $kiss->getDamages());
    }
    
    private function setErrorFlash(\Exception $e)
    {
        $message = '';
        if ($e instanceof InvalidLoverNameException) {
            $message = 'L\'amoureux '.$e->getMessage().' n\'existe pas';
        }
        if ($e instanceof InvalidSelfKissingException) {
            $message = 'L\'amoureux '.$e->getMessage().' ne peut pas s\'embrasser lui-m&ecirc;me';
        }
        if ($e instanceof KissOverflowException) {
            $kisserAndKissed = $e->getMessage();
            list($kisserName, $kissedName) = explode(',', $kisserAndKissed);
            $message = sprintf(
                    'L\'amoureux %s ne peut embrasser %s plus de %s fois en moins de %s heures',
                    $kisserName,
                    $kissedName,
                    KissFactory::$quotaOfKiss,
                    KissFactory::$timeBetweenQuotaOfKiss / 60 /60
            );
        }

        $session = $this->getRequest()->getSession();

        $session->setFlash(self::$flashKeyHasError, true);
        $session->setFlash(self::$flashKeyMessageError, $message);
    }
      
}
