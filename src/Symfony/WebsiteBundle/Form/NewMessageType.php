<?php
namespace Symfony\WebsiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usernameMessageSend', 'text', array('label' => 'To:', 'attr' => array('placeholder' => 'Username', 'autocomplete' => 'off')))
            ->add('text', 'textarea', array('attr' => array('placeholder' => 'Write a message..')))
            ->add('save', 'submit', array('label' => 'Send message!'))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Symfony\WebsiteBundle\Entity\Message',
        ));
    }

    public function getName()
    {
        return 'new_message';
    }
}