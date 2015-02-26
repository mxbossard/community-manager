<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Mby\CommunityBundle\Entity\Membership;
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

    /**
     * @var MembershipRepository
     */
    protected $membershipRepo;

    /**
     * @var ResponsibilityRepository
     */
    protected $responsibilityRepo;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
        $this->membershipRepo = $this->em->getRepository('MbyCommunityBundle:Membership');
        $this->responsibilityRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
    }

    public function isApplication(Membership $membership) {
        $applicantResp = $this->em->getReference('MbyCommunityBundle:Responsibility', ResponsibilityRepository::APPLICANT_CODE);

        return $this->hasResponsibility($membership->getResponsibilities(), $applicantResp);
    }

    public function isApplicant(User $user, Season $season) {
        $membership = $this->membershipRepo->loadResponsibilities($user->getId(), $season->getId());

        return $this->isApplication($membership);
    }

    public function isMembership(Membership $membership) {
        $memberResp = $this->em->getReference('MbyCommunityBundle:Responsibility', ResponsibilityRepository::MEMBER_CODE);

        return $this->hasResponsibility($membership->getResponsibilities(), $memberResp);
    }

    public function isMember(User $user, Season $season) {
        $membership = $this->membershipRepo->loadResponsibilities($user->getId(), $season->getId());

        return $this->isMembership($membership);
    }

    /**
     * @param $responsibilities
     * @param Responsibility $resp
     * @return bool
     */
    protected function hasResponsibility($responsibilities, Responsibility $resp) {
        /** @var Responsibility $responsibility */
        foreach ($responsibilities as $responsibility) {
            if ($responsibility->getCode() === $resp->getCode()) {
                return true;
            }
        }

        return false;
    }

}
