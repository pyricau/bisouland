<?php

namespace Bisouland\LoversBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LevelUpForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('levelUp', 'choice', array(
            'choices' => array(
                'seduction',
                'tongue',
                'dodge',
                'slap',
            ),
            'multiple' => false,
            'expanded' => true,
        ));
    }

    public function getName()
    {
        return 'levelUp';
    }
}
