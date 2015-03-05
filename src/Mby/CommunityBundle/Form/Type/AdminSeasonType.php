<?php

namespace Mby\CommunityBundle\Form\Type


;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminSeasonType extends AbstractType
{

    const CREATE = 'create';
    const EDIT = 'edit';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('name', 'text')
        ;

        if (isset($options['intention']) && $options['intention'] == AdminSeasonType::EDIT) {
            $builder
                ->add('fromDate', 'date', array(
                    'disabled' => true


                ))
                ->add('toDate', 'date', array(
                    //'years' => array(),
                    //'months' => array(),
                    //'days' => array(),
                ))
            ;
        }

        $builder->add('note', 'textarea', array(
                'required' => false
            ))
        ;

        $builder
            ->add('save', 'submit', array(
                'attr' => array('class' => 'btn-success'),
            ))
            ->add('reset', 'reset', array(
                'attr' => array('class' => 'btn-danger'),
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Entity\Season'
        ));
    }

    public function getName()
    {
        return 'admin_season';
    }
}
