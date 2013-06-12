<?php

namespace Bisouland\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Loic Chardonnet <loic.chardonnet@gmail.com>
 */
class RegistrationFormType extends BaseType
{
    private $class;

    /**
     * Overriding FOSUserBundle's FormType to remove password confirmation.
     *
     * @{inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('plainPassword')
            ->add('plainPassword', 'password', array(
                'translation_domain' => 'FOSUserBundle',
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
