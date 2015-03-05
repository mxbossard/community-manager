<?php

namespace Mby\CommunityBundle\Service\Facade;

use Doctrine\ORM\EntityManager;

use Mby\CommunityBundle\Entity\CommunityPrivilege;
use Mby\CommunityBundle\Entity\CommunityPrivilegeRepository;
use Mby\CommunityBundle\Entity\Privilege;
use Mby\CommunityBundle\Entity\PrivilegeRepository;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\SeasonRepository;
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
                                PrivilegeManager $privilegeManager) {
        $this->em = $entityManager;
        $this->seasonManager = $seasonManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * Build a new season with default params.
     *
     * @param Community $community
     * @return Season
     */
    public function buildNewSeason(Community $community) {
        $season = new Season();
        $season->setActive(false)
            ->setFromDate(new \DateTime('today'))
            ->setToDate(null)
            ->setCommunity($community);
        ;

        return $season;
    }

    /**
     * Create a new season.
     *
     * @param User $user
     * @param Community $community
     * @param Season $season
     */
    public function createNewSeason(User $user, Community $community, Season $season) {
        if (! $this->privilegeManager->isAdministrator($user, $community)) {
            throw new AccessDeniedException();
        }

        // create new season
        $season->setActive(false);
        // from date: today
        $season->setFromDate(new \DateTime('today'));
        // season not closed on creation
        $season->setToDate(null);

        $this->seasonManager->create($community, $season);

        $this->em->flush();
    }

    /**
     * Update a season.
     *
     * @param User $user
     * @param Season $season
     */
    public function updateSeason(User $user, Season $season) {
        /** @var SeasonRepository $seasonRepo */
        $seasonRepo = $this->em->getRepository('MbyCommunityBundle:Season');
        /** @var Season $managedSeason */
        $managedSeason = $seasonRepo->find($season->getId());

        if (! $this->privilegeManager->isAdministrator($user, $managedSeason->getCommunity())) {
            throw new AccessDeniedException();
        }

        // TODO Check if season is updatable

        // Update editable fields
        $managedSeason->setToDate($season->getToDate())
            ->setName($season->getName())
            ->setNote($season->getNote())
        ;

        $this->seasonManager->update($managedSeason);

        $this->em->flush();
    }

    /**
     * Close a season.
     *
     * @param User $user
     * @param Season $season
     * @param \DateTime $endDate
     * @throws \Mby\CommunityBundle\Exception\OrmException
     */
    public function closeSeason(User $user, Season $season, \DateTime $endDate) {
        if (! $this->privilegeManager->isAdministrator($user, $season->getCommunity())) {
            throw new AccessDeniedException();
        }

        $this->seasonManager->close($season, $endDate);
        $this->em->flush();
    }

    /**
     * Lock a season.
     *
     * @param User $user
     * @param Season $season
     */
    public function lockSeason(User $user, Season $season) {
        if (! $this->privilegeManager->isAdministrator($user, $season->getCommunity())) {
            throw new AccessDeniedException();
        }

        $this->seasonManager->lockSeason($season);
        $this->em->flush();
    }

    /**
     * Unlock a season.
     *
     * @param User $user
     * @param Season $season
     */
    public function unlockSeason(User $user, Season $season) {
        if (! $this->privilegeManager->isAdministrator($user, $season->getCommunity())) {
            throw new AccessDeniedException();
        }

        $this->seasonManager->unlockSeason($season);
        $this->em->flush();
    }
}
