<?php

namespace MxBossard\CommunityMgrBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => 'form.user.username'));
        $builder->add('email', 'email', array('label' => 'form.user.email'));
        $builder->add('password', 'repeated', array(
           'first_name'  => 'password',
           'first_options' => array('label' => 'form.user.password'),
           'second_name' => 'confirm',
           'second_options' => array('label' => 'form.user.password_confirm'),
           'type'        => 'password'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MxBossard\CommunityMgrBundle\Entity\User',
            'label' => 'form.user'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}