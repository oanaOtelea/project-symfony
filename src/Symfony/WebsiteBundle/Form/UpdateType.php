<?php

namespace Symfony\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('required' => true))
            ->add('lastname', 'text', array('required' => false))
            ->add('firstname', 'text', array('required' => false))
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'required' => false,
                    'options' => array('attr' => array('class' => 'password-field')),
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                  ))
            ->add('email', 'email', array('required' => true))
            ->add('birthday', 'date', array('required' => false))
            ->add('gender', 'choice', array(
                    'choices'   => array('male' => 'Male', 'female' => 'Female'),
                    'required'  => false,
                    'empty_data'  => null
                 ))
            ->add('save', 'submit', array('label' => 'Save'))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symfony\WebsiteBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'symfony_websitebundle_update';
    }
}
