<?php

namespace Mby\CommunityBundle\Form;

use Doctrine\ORM\EntityManager;
use Mby\CommunityBundle\Entity\Privilege;
use MxBossard\CommunityMgrBundle\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class CommunityPrivilegeToCodeTransformer implements DataTransformerInterface
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
    * Transforms an object (Privilege) to a string (code).
    *
    * @param  Privilege|null $user
    * @return integer
    */
    public function transform($privilege)
    {
        if (null === $privilege) {
            return "";
        }

        return $privilege->getCode();
    }

    /**
    * Transforms a string (code) to an object (Privilege).
    *
    * @param  string $code
    *
    * @return Privilege|null
    *
    * @throws TransformationFailedException if object (issue) is not found.
    */
    public function reverseTransform($code)
    {
        if (!$code) {
            return null;
        }

        $user = $this->em->getReference("MbyCommunityBundle:Privilege", $code);

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                "Privilege #%s does not exist !",
                $code
            ));
        }

        return $user;
    }
}