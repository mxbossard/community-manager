<?php

namespace Mby\CommunityBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mby\CommunityBundle\Form\CommunityPrivilegeToCodeTransformer;
use Mby\CommunityBundle\Form\CommunityToIdTransformer;
use Mby\CommunityBundle\Form\UserToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrivilegedUserType extends AbstractType
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

        $builder->add('userId', 'hidden');
        $builder->add('communityId', 'hidden');
        $builder->add('label', 'hidden');

        $builder->add('owner', 'checkbox', array(
            'label' => ' ',
            'required' => false,
        ));
        $builder->add('admin', 'checkbox', array(
            'label' => ' ',
            'required' => false,
        ));
        $builder->add('moderator', 'checkbox', array(
            'label' => ' ',
            'required' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mby\CommunityBundle\Model\PrivilegedUser',
        ));
    }

    public function getName()
    {
        return 'privilegedUser';
    }

}