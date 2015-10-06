<?php

namespace Symfony\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('label_attr' => array('class' => 'username')))
            ->add('lastname', 'text', array('label_attr' => array('class' => 'lastname')))
            ->add('firstname', 'text', array('label_attr' => array('class' => 'firstname')))
            ->add('password', 'password',
                    array('label' => 'Password', 'label_attr' => array('class' => 'password'),
                  ))
            ->add('repeatedPassword','password',
                    array('label' => 'Repeat Password', 'label_attr' => array('class' => 'repeatPassword'),
                  ))
            ->add('email', 'email', array('label_attr' => array('class' => 'email')))
            ->add('birthday', 'date', array('label_attr' => array('class' => 'birthday')))
            ->add('gender', 'choice', array(
                    'choices'   => array('male' => 'Male', 'female' => 'Female'),
                    'label_attr' => array('class' => 'gender'),
                 ))
            ->add('agree', 'checkbox', array('label' => 'Agree with terms and conditions', 'value' => true, 'label_attr' => array('class' => 'agree')))
            ->add('Register', 'submit')
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symfony\WebsiteBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'symfony_websitebundle_register';
    }
}
