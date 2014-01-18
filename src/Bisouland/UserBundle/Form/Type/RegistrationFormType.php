<?php

namespace Bisouland\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Overriding FOSUserBundle's FormType to remove password confirmation.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class RegistrationFormType extends BaseType
{
    private $class;

    /**
     * @{inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('plainPassword')
            ->add('plainPassword', 'password', array(
                'label' => 'form.password',
            ))
        ;
    }

    /**
     * @{inheritdoc}
     */
    public function getName()
    {
        return 'bisouland_user_registration';
    }
}
