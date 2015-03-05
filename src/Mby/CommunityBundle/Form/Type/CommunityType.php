<?php

namespace Mby\CommunityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommunityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('name')
            ->add('email')
            ->add('description')
            ->add('note')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Entity\Community'
        ));
    }

    public function getName()
    {
        return 'mby_communitybundle_community';
    }
}
