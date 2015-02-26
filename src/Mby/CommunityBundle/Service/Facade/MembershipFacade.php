<?php

namespace Mby\CommunityBundle\Service\Facade;

use Doctrine\ORM\EntityManager;

use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Entity\Season;
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
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    public function __construct(EntityManager $entityManager, MembershipManager $membershipManager,
                                PrivilegeManager $privilegeManager)
    {
        $this->em = $entityManager;
        $this->membershipManager = $membershipManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * Register the application of a user.
     *
     * @param User $user
     * @param Season $season
     */
    public function applyToSeason(User $user, Season $season) {
        $this->membershipManager->apply($user, $season);

        $this->em->flush();
    }

    /**
     * Accept an application.
     *
     * @param User $user
     * @param Membership $membership
     * @throws \Exception
     */
    public function acceptApplication(User $user, Membership $membership) {
        $season = $membership->getSeason();
        $community = $season->getCommunity();

        if (! $this->privilegeManager->isModerator($user, $community)) {
            throw new \Exception("user must be moderator to accept an application");
        }

        $this->membershipManager->acceptApplication($membership);

        $this->em->flush();
    }

    /**
     * Reject an application.
     *
     * @param User $user
     * @param Membership $membership
     * @throws \Exception
     */
    public function rejectApplication(User $user, Membership $membership) {
        $season = $membership->getSeason();
        $community = $season->getCommunity();

        if (! $this->privilegeManager->isModerator($user, $community)) {
            throw new \Exception("user must be moderator to reject an application");
        }

        $this->membershipManager->rejectApplication($membership);

        $this->em->flush();
    }

    /**
     * Cancel an application.
     *
     * @param User $user
     * @param Membership $membership
     * @throws \Exception
     */
    public function cancelApplication(User $user, Membership $membership) {
        if ($user->getId() !== $membership->getUser()->getId()) {
            throw new \Exception("user must be applicant to cancel an application");
        }

        $this->membershipManager->cancelApplication($membership);

        $this->em->flush();
    }


}
