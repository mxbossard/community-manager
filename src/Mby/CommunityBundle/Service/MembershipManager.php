<?php

namespace Mby\CommunityBundle\Controller;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Entity\Responsibility;

class MembershipManager
{

    public const NAME = 'membserhip_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

	/**
	 * Register a user's membership to a season with responsibilities.
	 *
     */
    public function create(User $user, Season $season, Responsibility $responsibility, \Date $fromDate = null, \Date $toDate = null)
    {
        $ms = new Membership();
        $ms->setUser($user);
        $ms->setSeason($season);
        $ms->addResponsibility($responsibility)

        // if ($fromDate == null) {
        //     $fromDate = $season->getFromDate();
        // }
        
        $ms->setFromDate($fromDate);
        $ms->setToDate($toDate);

        $msRepo = $em->getRepository('MbyCommunityBundle:Membership');
        
        $em->persist($ms);
        $em->flush();
    }


    /**
     * Register a user's membership to a community with responsibilities.
     *
     */
    public function update(Membership $membership)
    {
        $em->update($membership);
        $em->flush();
    }


}
