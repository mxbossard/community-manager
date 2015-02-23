<?php

namespace Mby\CommunityBundle\Service\Facade;

use Doctrine\ORM\EntityManager;

use Mby\CommunityBundle\Entity\CommunityPrivilege;
use Mby\CommunityBundle\Entity\CommunityPrivilegeRepository;
use Mby\CommunityBundle\Entity\Privilege;
use Mby\CommunityBundle\Entity\PrivilegeRepository;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Model\PrivilegedUser;
use Mby\CommunityBundle\Service\CommunityManager;
use Mby\CommunityBundle\Service\PrivilegeManager;
use Mby\CommunityBundle\Service\SeasonManager;
use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SeasonFacade
{

    const SERVICE_NAME = 'season_facade';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SeasonManager
     */
    protected $seasonManager;

    /**
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    public function __construct(EntityManager $entityManager, SeasonManager $seasonManager,
                                PrivilegeManager $privilegeManager)
    {
        $this->em = $entityManager;
        $this->seasonManager = $seasonManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * Create a new season.
     *
     * @param User $user
     * @param Community $community
     * @param Season $season
     */
    public function createNewSeason(User $user, Community $community, Season $season)
    {
        if (! $this->privilegeManager->isAdministrator($user, $community)) {
            throw new AccessDeniedException();
        }

        // Check if previous season is closed

        // create new season
        $this->seasonManager->create($user, $community, $season);

        $this->em->flush();
    }

    /**
     * Update a season.
     *
     * @param User $user
     * @param Season $season
     */
    public function updateSeason(User $user, Season $season)
    {
        if (! $this->privilegeManager->isAdministrator($user, $season->getCommunity())) {
            throw new AccessDeniedException();
        }

        $this->em->flush();
    }

    /**
     * Close a season.
     *
     * @param User $user
     * @param Season $season
     */
    public function closeSeason(User $user, Season $season)
    {
        if (! $this->privilegeManager->isAdministrator($user, $season->getCommunity())) {
            throw new AccessDeniedException();
        }

        $this->em->flush();
    }

}
