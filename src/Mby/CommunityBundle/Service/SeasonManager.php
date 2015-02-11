<?php

namespace Mby\CommunityBundle\Controller;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\SeasonRepository;

class SeasonManager
{

    const SERVICE_NAME = 'season_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    public function __construct(EntityManager $entityManager, PrivilegeManager $privilegeManager)
    {
        $this->em = $entityManager;
        $this->privilegeManager = $privilegeManager;
    }

	/**
	 * Create a new season.
	 *
     */
    protected function create(Season $season, User $user, Community $community)
    {
        if(! $this->privilegeManager->isAdministrator($user, $community)) {
            throw new Exception("user must be administrator to create a season");
        }

        $seasonRepo = $this->em->getRepository('MbyCommunityBundle:Season');
        $lastSeason = $seasonRepo->findLastSeason($community);

        if (! $season->getFromDate() > $lastSeason) {
            throw new Exception("season start date must take place after the previous season start date");
        }

        $season->setUser($user);
        $season->setCommunity($community);

        $this->em->persist($season);
        $this->em->flush();
    }

}
