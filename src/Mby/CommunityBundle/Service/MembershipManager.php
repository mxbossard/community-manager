<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;

use Faker\Provider\cs_CZ\DateTime;
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

    public function __construct(EntityManager $entityManager, PrivilegeManager $privilegeManager, ResponsibilityManager $responsibilityManager) {
        $this->em = $entityManager;
        $this->privilegeManager = $privilegeManager;
        $this->responsibilityManager = $responsibilityManager;
    }

    public function apply(User $user, Season $season, \DateTime $fromDate = null, \DateTime $toDate = null) {
        if (! $season->getCommunity()->getJoinable()) {
            throw new \Exception("selected community is not joinable");
        }

        $this->checkSeasonNotExpired($season);

        $refApplicant = $this->em->getReference('MbyCommunityBundle:Responsibility', ResponsibilityRepository::APPLICANT_CODE);

        $today = new \DateTime('today');

        $ms = $this->createMembership($user, $season, $refApplicant, $today);

        $this->em->persist($ms);
    }

    public function acceptApplication(Membership $membership, $comment = null, \DateTime $fromDate = null, \DateTime $toDate = null) {
        $season = $membership->getSeason();

        $this->checkSeasonNotExpired($season);

        if (! $this->responsibilityManager->isApplication($membership)) {
            throw new \Exception("selected membership is not an application");
        }

        $this->removeResponsibility($membership, ResponsibilityRepository::APPLICANT_CODE);

        /** @var Responsibility $refMember */
        $refMember = $this->em->getReference('MbyCommunityBundle:Responsibility', ResponsibilityRepository::MEMBER_CODE);
        $membership->addResponsibility($refMember);

        $membership->setFromDate($fromDate);
        $membership->setToDate($toDate);
        $membership->setComment($comment);

        $this->updateMembership($membership);
    }

    public function rejectApplication(Membership $membership, $comment = null) {
        $season = $membership->getSeason();

        $this->checkSeasonNotExpired($season);

        if (! $this->responsibilityManager->isApplication($membership)) {
            throw new \Exception("selected membership is not an application");
        }

        $this->removeResponsibility($membership, ResponsibilityRepository::APPLICANT_CODE);

        $today = new \DateTime('today');
        $membership->setRejected(true);
        $membership->setFromDate($today);
        $membership->setToDate($today->modify('-1 day'));
        $membership->setComment($comment);

        $this->updateMembership($membership);
    }

    public function cancelApplication(Membership $membership, $comment = null) {
        $season = $membership->getSeason();

        $this->checkSeasonNotExpired($season);

        if (! $this->responsibilityManager->isApplication($membership)) {
            throw new \Exception("selected membership is not an application");
        }

        $this->removeResponsibility($membership, ResponsibilityRepository::APPLICANT_CODE);

        $today = new \DateTime('today');
        $membership->setCanceled(true);
        $membership->setFromDate($today);
        $membership->setToDate($today->modify('-1 day'));
        $membership->setComment($comment);

        $this->updateMembership($membership);
    }

    /**
     * Register a user's membership to a season with responsibilities.
     *
     * @param User $user
     * @param Season $season
     * @param Responsibility $responsibility
     * @param \DateTime $applicationDate
     * @return Membership
     */
    protected function createMembership(User $user, Season $season, Responsibility $responsibility, \DateTime $applicationDate) {
        $ms = new Membership();
        $ms->setUser($user);
        $ms->setSeason($season);
        $ms->addResponsibility($responsibility);

        $ms->setApplicationDate($applicationDate);

        return $ms;
    }

    /**
     * Register a user's membership to a season with responsibilities.
     *
     * @param Membership $membership
     */
    protected function updateMembership(Membership $membership) {
        /*
        if ($membership->getResponsibilities()->count() === 0) {
            $this->em->remove($membership);
        } else {
            $this->em->persist($membership);
        }
        */

        $this->em->persist($membership);
    }

    /**
     * @param Season $season
     * @throws \Exception
     */
    public function checkSeasonNotExpired(Season $season) {
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

    /**
     * @param Membership $membership
     */
    public function removeResponsibility(Membership &$membership, $responsibilityCodeToRemove)
    {
        /** @var Responsibility $resp */
        foreach ($membership->getResponsibilities() as $resp) {
            if ($resp->getCode() === $responsibilityCodeToRemove) {
                $membership->removeResponsibility($resp);
                break;
            }
        }
    }

}
