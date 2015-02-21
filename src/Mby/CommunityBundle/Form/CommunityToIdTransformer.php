<?php

namespace Mby\CommunityBundle\Form;

use Doctrine\ORM\EntityManager;
use Mby\CommunityBundle\Entity\Community;
use MxBossard\CommunityMgrBundle\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class CommunityToIdTransformer implements DataTransformerInterface
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
    * Transforms an object (Community) to an integer (id).
    *
    * @param  Community|null $community
    * @return integer
    */
    public function transform($community)
    {
        if (null === $community) {
            return -1;
        }

        return $community->getId();
    }

    /**
    * Transforms an integer (id) to an object (Community).
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

        $community = $this->em->getReference("MbyCommunityBundle:Community", $id);

        if (null === $community) {
            throw new TransformationFailedException(sprintf(
                "Community #%s does not exist !",
                $id
            ));
        }

        return $community;
    }
}