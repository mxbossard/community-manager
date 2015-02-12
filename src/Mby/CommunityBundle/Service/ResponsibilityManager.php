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

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->membershipRepo = $this->em->getRepository('MbyCommunityBundle:Membership');
        $this->responsibilityRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
    }

    public function isApplicant(Membership $membership)
    {
        $applicantResp = $this->responsibilityRepo->findByName(ResponsibilityRepository::APPLICANT_NAME);

        return $this->hasMemberRole($membership->getResponsibilities(), $applicantResp);
    }

    public function isApplicant2(User $user, Season $season)
    {
        $membership = $this->membershipRepo->loadResponsibilities($user->getId(), $season->getId());

        return $this->isApplicant($membership);
    }

    public function isMember(Membership $membership)
    {
        $memberResp = $this->responsibilityRepo->findByName(ResponsibilityRepository::MEMBER_NAME);

        return $this->hasMemberRole($membership->getResponsibilities(), $memberResp);
    }

    public function isMember2(User $user, Season $season)
    {
        $membership = $this->membershipRepo->loadResponsibilities($user->getId(), $season->getId());

        return $this->isMember($membership);
    }

    protected function hasResponsibility(ArrayCollection $responsibilities, Responsibility $resp)
    {

        foreach ($responsibilities as $responsibility) {
            if ($responsibility->getId() === $resp->getId()) {
                return true;
            }
        }

        return false;
    }

}
