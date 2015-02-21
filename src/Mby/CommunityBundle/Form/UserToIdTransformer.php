<?php

namespace Mby\CommunityBundle\Form;

use Doctrine\ORM\EntityManager;
use MxBossard\CommunityMgrBundle\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class UserToIdTransformer implements DataTransformerInterface
{

    /**
    * @var EntityManager
    */
    private $em;

    /**
    * @param EntityManager $em
    */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
    * Transforms an object (User) to an integer (id).
    *
    * @param  User|null $user
    * @return integer
    */
    public function transform($user)
    {
        if (null === $user) {
            return -1;
        }

        return $user->getId();
    }

    /**
    * Transforms an integer (id) to an object (User).
    *
    * @param  integer $id
    *
    * @return Issue|null
    *
    * @throws TransformationFailedException if object (issue) is not found.
    */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $user = $this->em->getReference("MbyUserBundle:User", $id);

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                "User #%s does not exist !",
                $id
            ));
        }

        return $user;
    }
}