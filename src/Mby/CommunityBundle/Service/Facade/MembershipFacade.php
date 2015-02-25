<?php

namespace Mby\CommunityBundle\Service\Facade;

use Doctrine\ORM\EntityManager;

use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Service\CommunityManager;
use Mby\CommunityBundle\Service\MembershipManager;
use Mby\CommunityBundle\Service\PrivilegeManager;
use Mby\CommunityBundle\Service\ResponsibilityManager;
use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;

class MembershipFacade
{

    const SERVICE_NAME = 'membership_facade';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var MembershipManager
     */
    protected $membershipManager;

    /**
     * @var ResponsibilityManager
     */
    protected $responsibilityManager;

    public function __construct(EntityManager $entityManager, MembershipManager $membershipManager,
                                ResponsibilityManager $responsibilityManager)
    {
        $this->em = $entityManager;
        $this->membershipManager = $membershipManager;
        $this->responsibilityManager = $responsibilityManager;
    }

    /**
     * Register the application of a user.
     *
     * @param User $user
     * @param Season $season
     */
    public function applyToSeason(User $user, Season $season)
    {


        $this->em->flush();
    }

    /**
     * Accept an application.
     *
     * @param Membership $membership
     */
    public function acceptApplication(Membership $membership)
    {


        $this->em->flush();
    }

    /**
     * Reject an application.
     *
     * @param Membership $membership
     */
    public function rejectApplication(Membership $membership)
    {


        $this->em->flush();
    }

    /**
     * Cancel an application.
     *
     * @param Membership $membership
     */
    public function cancelApplication(Membership $membership)
    {


        $this->em->flush();
    }


}
