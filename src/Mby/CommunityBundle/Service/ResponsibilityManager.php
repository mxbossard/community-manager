<?php

namespace Mby\CommunityBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Responsibility;
use Mby\CommunityBundle\Entity\ResponsibilityRepository;

class ResponsibilityManager
{

    const SERVICE_NAME = 'responsibility_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function isMember(User $user, Season $season)
    {
        $membershipRepo = $this->em->getRepository('MbyCommunityBundle:Membership');
        $membership = $membershipRepo->loadResponsibilities($user->getId(), $season->getId());

        return $this->hasMemberRole($membership->getResponsibilities());
    }

    protected function hasMemberRole(ArrayCollection $responsibilities)
    {
        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respAdmin = $respRepo->findByName(ResponsibilityRepository::MEMBER_NAME);

        foreach ($responsibilities as $responsibility) {
            if ($responsibility->getId() === $respAdmin->getId()) {
                return true;
            }
        }

        return false;
    }

}
