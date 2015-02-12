<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Entity\Responsibility;
use Mby\CommunityBundle\Entity\ResponsibilityRepository;

class MembershipManager
{

    const SERVICE_NAME = 'membership_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    /**
     * @var ResponsibilityManager
     */
    protected $responsibilityManager;

    public function __construct(EntityManager $entityManager, PrivilegeManager $privilegeManager, ResponsibilityManager $responsibilityManager)
    {
        $this->em = $entityManager;
        $this->privilegeManager = $privilegeManager;
        $this->responsibilityManager = $responsibilityManager;
    }

    public function apply(User $user, Season $season, \DateTime $fromDate = null, \DateTime $toDate = null)
    {
        if (! $season->getCommunity()->getJoinable()) {
            throw new \Exception("selected community is not joinable");
        }

        $this->checkSeasonNotExpired($season);

        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respApplicant = $respRepo->findByName(ResponsibilityRepository::APPLICANT_NAME);

        if ($fromDate == null) {
            $fromDate = new \DateTime();
        }

        $this->createMembership($user, $season, $respApplicant, $fromDate, $toDate);
    }

    public function validApplication(User $user, Membership $membership)
    {
        $season = $membership->getSeason();
        $community = $season->getCommunity();

        $this->checkSeasonNotExpired($season);

        if (! $this->privilegeManager->isModerator($user, $community)) {
            throw new \Exception("user must be moderator to validate an application");
        }

        if (! $this->responsibilityManager->isApplicant($membership)) {
            throw new \Exception("selected membership is not an application");
        }

        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respApplicant = $respRepo->findByName(ResponsibilityRepository::APPLICANT_NAME);
        $respMember = $respRepo->findByName(ResponsibilityRepository::MEMBER_NAME);

        $membership->removeResponsibility($respApplicant);
        $membership->addResponsibility($respMember);

        $this->updateMembership($membership);
    }

    public function cancelApplication(User $user, Membership $membership)
    {
        $season = $membership->getSeason();
        $community = $season->getCommunity();

        $this->checkSeasonNotExpired($season);

        if (! $this->privilegeManager->isModerator($user, $community)
            && $membership->getUser()->getId() !== $user->getId()) {
            throw new \Exception("user must be moderator or the applicant to cancel the application");
        }

        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respApplicant = $respRepo->findByName(ResponsibilityRepository::APPLICANT_NAME);

        $membership->removeResponsibility($respApplicant);

        $this->updateMembership($membership);
    }

	/**
	 * Register a user's membership to a season with responsibilities.
	 *
     */
    protected function createMembership(User $user, Season $season, Responsibility $responsibility, \DateTime $fromDate = null, \DateTime $toDate = null)
    {
        $ms = new Membership();
        $ms->setUser($user);
        $ms->setSeason($season);
        $ms->addResponsibility($responsibility);

        $ms->setFromDate($fromDate);
        $ms->setToDate($toDate);

        $this->em->persist($ms);
        $this->em->flush();
    }

    /**
     * Register a user's membership to a season with responsibilities.
     *
     */
    protected function updateMembership(Membership $membership)
    {
        if ($membership->getResponsibilities()->count() === 0) {
            $this->em->remove($membership);
        }

        $this->em->flush();
    }

    /**
     * @param Season $season
     * @throws \Exception
     */
    public function checkSeasonNotExpired(Season $season)
    {
// Check for season expiration date. If toDate is null => season not expired.
        $seasonToDate = $season->getToDate();
        if ($seasonToDate == null) {
            $seasonToDate = new \DateTime();
        } else {
            $seasonToDate = clone $seasonToDate;
        }
        $seasonToDate->modify("+1 day");

        if ($seasonToDate->getTimestamp() < (new \DateTime())->getTimestamp()) {
            throw new \Exception(sprintf("selected season expired since %s", $seasonToDate->format("Y-m-d H:i:s")));
        }
    }

}
