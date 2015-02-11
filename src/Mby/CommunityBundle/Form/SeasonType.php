<?php

namespace Mby\CommunityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromDate')
            ->add('toDate')
            ->add('name')
            ->add('note')
            ->add('community')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Entity\Season'
        ));
    }

    public function getName()
    {
        return 'mby_communitybundle_season';
    }
}
