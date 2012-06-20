<?php

namespace Bisouland\LoversBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bisouland\LoversBundle\Entity\Factory\KissFactory;
use Bisouland\LoversBundle\Controller\SelectionController;
use Bisouland\RolePlayingGameSystemBundle\Entity\Factory\AttackFactory;

use Bisouland\LoversBundle\Exception\InvalidKisserException;
use Bisouland\LoversBundle\Exception\InvalidKissedException;
use Bisouland\LoversBundle\Exception\InvalidKisserAsKissedException;
use Bisouland\LoversBundle\Exception\KissOverflowException;

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
        $kissFactory = new KissFactory($this->getDoctrine(), $this->get('bisouland_game_system.kiss_factory'));

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
        if ($e instanceof InvalidKisserException) {
            $message = 'Vous ne pouvez embrasser avec un amoureux qui n\'existe pas';
        }
        if ($e instanceof InvalidKissedException) {
            $message = 'Vous ne pouvez embrasser un amoureux qui n\'existe pas';
        }
        if ($e instanceof InvalidKisserAsKissedException) {
            $message = 'Vous ne pouvez vous embrasser vous m&ecirc;me';
        }
        if ($e instanceof KissOverflowException) {
            $message = sprintf(
                    'Vous ne pouvez pas embrasser plus de %s fois le m&ecirc;me amoureux en moins de %s heures',
                    KissFactory::$quotaOfKiss,
                    KissFactory::$quotaIsTwelveHoursInSeconds / 60 /60
            );
        }

        $session = $this->getRequest()->getSession();

        $session->setFlash(self::$flashKeyHasError, true);
        $session->setFlash(self::$flashKeyMessageError, $message);
    }
      
}
