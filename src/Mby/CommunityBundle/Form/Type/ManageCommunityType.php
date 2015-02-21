<?php

namespace Mby\CommunityBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mby\CommunityBundle\Form\CommunityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ManageCommunityType extends AbstractType
{

    /**
     * @var EntityManager
     */
    protected $em;

    /** @var CommunityType */
    var $communityType;

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->communityType = new CommunityType();
    }

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('community', new CommunityType($this->em));

        $builder->add('privilegedUsers', 'collection', array(
            'type' => new PrivilegedUserType($this->em),
            'allow_add' => true,
            'allow_delete' => false,
            'prototype' => true,
            ));

        $builder->add('save', 'submit', array(
            'attr' => array('class' => 'btn-success'),
        ));

        $builder->add('reset', 'reset', array(
            'attr' => array('class' => 'btn-danger'),
        ));

    }

    /** {@inheritdoc} */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'data_class' => 'Mby\CommunityBundle\Entity\Community',
            //'compound' => true,
        ));
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'manage_community';
    }
}