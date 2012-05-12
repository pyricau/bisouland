<?php

namespace Bisouland\BeingsBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use PronounceableWord_Generator;

use Bisouland\BeingsBundle\Entity\Factory\BeingFactory;

class BeingUpdaterSubscriber implements EventSubscriberInterface
{
    static public $maximumNumberOfBirthInOneDay = 42;
    
    private $doctrine;
    private $pronounceable_word_generator;
    
    public function __construct(ManagerRegistry $doctrine, PronounceableWord_Generator $pronounceable_word_generator)
    {
        $this->doctrine = $doctrine;
        $this->pronounceable_word_generator = $pronounceable_word_generator;
    }

    static public function getSubscribedEvents()
    {
        $priorities = array(
            'firstEvent' => 0,
        );

        return array(
            'kernel.request' => array(
                array('birth', $priorities['firstEvent']),
            ),
        );
    }
    
    public function birth()
    {
        $numberOfBirthsToday = $this->doctrine
                ->getRepository('BisoulandBeingsBundle:Being')
                ->countBirthsToday();
        
        if (self::$maximumNumberOfBirthInOneDay > $numberOfBirthsToday) {
            $beingFactory = new BeingFactory($this->pronounceable_word_generator);

            $em = $this->doctrine->getEntityManager();
            $em->persist($beingFactory->make());
            $em->flush();
        }
    }
}
