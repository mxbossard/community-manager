<?php

namespace Mby\CommunityBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mby\CommunityBundle\Form\CommunityPrivilegeToCodeTransformer;
use Mby\CommunityBundle\Form\CommunityToIdTransformer;
use Mby\CommunityBundle\Form\UserToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommunityPrivilegeType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userTransformer = new UserToIdTransformer($this->em);
        $communityTransformer = new CommunityToIdTransformer($this->em);
        $communityPrivilegeTransformer = new CommunityPrivilegeToCodeTransformer($this->em);

        //$builder->add('username', null, array('widget' => 'single_text'));

        $builder->add('privilege', 'entity', array(
            'class' => 'MbyCommunityBundle:Privilege',
        ));

        $builder->add(
            $builder->create('user', 'hidden')
                ->addModelTransformer($userTransformer)
            );
        $builder->add(
            $builder->create('community', 'hidden')
                ->addModelTransformer($communityTransformer)
        );
        /*
        $builder->add(
            $builder->create('privilege', 'hidden')
                ->addModelTransformer($communityPrivilegeTransformer)
        );*/
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Entity\CommunityPrivilege',
        ));
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Entity\CommunityPrivilege'
        ));
    }

    public function getName()
    {
        return 'privilege';
    }

}