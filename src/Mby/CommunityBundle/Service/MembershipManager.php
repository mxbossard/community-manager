<?php

namespace Mby\CommunityBundle\Controller;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Entity\Responsibility;
use Mby\CommunityBundle\Entity\ResponsibilityRepository;

class MembershipManager
{

    const SERVICE_NAME = 'membserhip_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function apply(User $user, Season $season, \Date $fromDate = null, \Date $toDate = null)
    {
        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respApplicant = $respRepo->findByCode(ResponsibilityRepository::APPLICANT_CODE);

        $this->create($user, $season, $respApplicant, $fromDate, $toDate);
    }

    public function validApplication(Membership $membership)
    {
        $respRepo = $this->em->getRepository('MbyCommunityBundle:Responsibility');
        $respApplicant = $respRepo->findByCode(ResponsibilityRepository::APPLICANT_CODE);
        $respMember = $respRepo->findByCode(ResponsibilityRepository::MEMBER_CODE);

        $membership->removeResponsibility($respApplicant);
        $membership->addResponsibility($respMember);

        $this->update($membership);
    }

	/**
	 * Register a user's membership to a season with responsibilities.
	 *
     */
    protected function create(User $user, Season $season, Responsibility $responsibility, \Date $fromDate = null, \Date $toDate = null)
    {
        $ms = new Membership();
        $ms->setUser($user);
        $ms->setSeason($season);
        $ms->addResponsibility($responsibility);

        // if ($fromDate == null) {
        //     $fromDate = $season->getFromDate();
        // }

        $ms->setFromDate($fromDate);
        $ms->setToDate($toDate);

        $msRepo = $this->em->getRepository('MbyCommunityBundle:Membership');
        
        $this->em->persist($ms);
        $this->em->flush();
    }

    /**
     * Register a user's membership to a community with responsibilities.
     *
     */
    protected function update(Membership $membership)
    {
        $this->em->update($membership);
        $this->em->flush();
    }


}
