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
use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommunityFacade
{

    const SERVICE_NAME = 'community_facade';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var CommunityManager
     */
    protected $communityManager;

    /**
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    public function __construct(EntityManager $entityManager, CommunityManager $communityManager,
                                PrivilegeManager $privilegeManager)
    {
        $this->em = $entityManager;
        $this->communityManager = $communityManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * Create a new community.
     * The new community is build it's owner privileges.
     *
     * @param User $user
     * @param Community $community
     */
    public function createNewCommunity(User $user, Community $community)
    {
        $this->communityManager->create($user, $community);

        $this->em->flush();
    }

    /**
     * Save a community.
     *
     * @param User $user
     * @param Community $community
     */
    public function saveCommunity(User $user, Community $community)
    {
        if (! $this->privilegeManager->isOwner($user, $community)) {
            throw new AccessDeniedException();
        }

        $this->em->persist($community);

        $this->em->flush();
    }

    /**
     * Save a community with its privileges.
     *
     * @param User $user
     * @param Community $community
     * @param $privilegedUsers
     */
    public function saveCommunityWithPrivileges(User $user, Community $community, $privilegedUsers)
    {
        if (! $this->privilegeManager->isOwner($user, $community)) {
            throw new AccessDeniedException();
        }

        $this->em->persist($community);

        $this->mapPrivilegedUsersIntoCommunity($community, $privilegedUsers);

        foreach($community->getPrivileges() as $privilege) {
            $privilege->setCommunity($community);
            $this->em->persist($privilege);
        }

        $this->em->flush();
    }

    /**
     * Find all @link PrivilegedUser of a community.
     *
     * @param Community $community
     * @return array
     */
    public function findCommunityPrivilegedUsers(Community $community)
    {
        /** @var CommunityPrivilegeRepository $comPrivRepo */
        $comPrivRepo = $this->em->getRepository('MbyCommunityBundle:CommunityPrivilege');
        $communityPrivileges = $comPrivRepo->findCommunityPrivileges($community);

        $privilegedUsers = $this->loadPrivilegedUsers($community, $communityPrivileges);

        return $privilegedUsers;
    }


    /**
     * Update the moderators privileges of a community.
     *
     * @param User $user
     * @param Community $community
     * @param array $moderators
     */
    public function updateModeratorsOfCommunity(User $user, Community $community, $moderators)
    {
        if (! $this->privilegeManager->isAdministrator($user, $community)) {
            throw new AccessDeniedException();
        }


        $this->em->flush();
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

    /**
     * @param Community $community
     * @param $communityPrivileges
     * @return array
     */
    public function loadPrivilegedUsers(Community $community, $communityPrivileges)
    {
        $privilegedUsers = array();
        /** @var PrivilegedUser $currentPrivilegedUser */
        $currentPrivilegedUser = null;

        // Map all CommunityPrivilege to some PrivilegedUser

        /** @var CommunityPrivilege $communityPrivilege */
        foreach ($communityPrivileges as $communityPrivilege) {
            $user = $communityPrivilege->getUser();
            $privilege = $communityPrivilege->getPrivilege();

            if ($currentPrivilegedUser === null || $currentPrivilegedUser->getUserId() !== $user->getId()) {
                $currentPrivilegedUser = new PrivilegedUser();
                $currentPrivilegedUser->setCommunity($community);
                $currentPrivilegedUser->setCommunityId($community->getId());
                $currentPrivilegedUser->setUser($user);
                $currentPrivilegedUser->setUserId($user->getId());
                $currentPrivilegedUser->setLabel($user->getUsername());
            }

            if (PrivilegeRepository::OWNER_CODE === $privilege->getCode()) {
                $currentPrivilegedUser->setOwner(true);
            } else if (PrivilegeRepository::ADMIN_CODE === $privilege->getCode()) {
                $currentPrivilegedUser->setAdmin(true);
            } else if (PrivilegeRepository::MODERATOR_CODE === $privilege->getCode()) {
                $currentPrivilegedUser->setModerator(true);
            }

            $privilegedUsers[$user->getId()] = $currentPrivilegedUser;
        }
        return $privilegedUsers;
    }

    /**
     * Compare passed array of @link PrivilegedUser and @link CommunityPrivilege contained in @link Community.
     * Return the comparison result:
     * array(
     *      'keep' => $communityPrivilegesToKeep,
     *      'add' => $communityPrivilegesToAdd,
     *      'remove' => $communityPrivilegesToRemove,
     * )
     *
     * @param Community $community
     * @param array $privilegedUsers
     * @return array
     */
    public function comparePrivilegedUsersAndCommunityPrivileges(Community $community, $privilegedUsers)
    {
        $communityPrivileges = $community->getPrivileges();

        // array of users involved in privilege modifications
        $usersInvolved = array();

        $indexedCommunityPrivileges = array();
        /** @var CommunityPrivilege $communityPrivilege */
        foreach ($communityPrivileges as $cp) {
            $key = $cp->getUser()->getId() . $cp->getPrivilege()->getCode();
            $indexedCommunityPrivileges[$key] = $cp;

            $usersInvolved[$cp->getUser()->getId()] = null;
        }

        foreach($privilegedUsers as $pu) {
            $usersInvolved[$pu->getUserId()] = $pu;
        }

        $communityPrivilegesToKeep = array();
        $communityPrivilegesToAdd = array();
        $communityPrivilegesToRemove = array();

        /** @var PrivilegedUser $privilegedUser */
        foreach ($usersInvolved as $userId => $privilegedUser) {

            $userPrivilegesMap = array(
                CommunityPrivilegeRepository::OWNER_CODE => $privilegedUser !== null && $privilegedUser->isOwner(),
                CommunityPrivilegeRepository::ADMIN_CODE => $privilegedUser !== null && $privilegedUser->isAdmin(),
                CommunityPrivilegeRepository::MODERATOR_CODE => $privilegedUser !== null && $privilegedUser->isModerator(),
            );

            // index User's original CommunityPrivilege by privilege code
            $originalCommunityPrivileges = array();

            foreach ($userPrivilegesMap as $code => $granted) {
                $key = $userId . $code;
                if (isset($indexedCommunityPrivileges[$key])) {
                    $originalCommunityPrivileges[$code] = $indexedCommunityPrivileges[$key];
                }
            }

            // Compare original CommunityPrivilege with supplied PrivilegedUsers
            foreach ($userPrivilegesMap as $code => $granted) {
                /** @var CommunityPrivilege $privilegeInDb */

                if ($granted && ! isset($originalCommunityPrivileges[$code])) {
                    // Privilege granted but not before => add it

                    /** @var User $refUser */
                    $refUser = $this->em->getReference('MbyUserBundle:User', $userId);
                    /** @var Privilege $refPrivilege */
                    $refPrivilege = $this->em->getReference('MbyCommunityBundle:Privilege', $code);

                    $toAdd = new CommunityPrivilege();
                    $toAdd->setUser($refUser);
                    $toAdd->setCommunity($community);
                    $toAdd->setPrivilege($refPrivilege);

                    $communityPrivilegesToAdd[] = $toAdd;

                } else if (! $granted && isset($originalCommunityPrivileges[$code])) {
                    // Privilege not granted but granted before => remove it
                    $communityPrivilegesToRemove[] = $originalCommunityPrivileges[$code];
                } else if ($granted && isset($originalCommunityPrivileges[$code])) {
                    // Keep the privilege
                    $communityPrivilegesToKeep[] = $originalCommunityPrivileges[$code];
                }
            }

        }

        return array(
            'keep' => $communityPrivilegesToKeep,
            'add' => $communityPrivilegesToAdd,
            'remove' => $communityPrivilegesToRemove,
        );
    }

}
