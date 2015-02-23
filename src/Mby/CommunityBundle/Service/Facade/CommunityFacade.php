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

        $communityPrivileges = $community->getPrivileges();
        $comparison = $this->comparePrivilegedUsersAndCommunityPrivilegesBis($communityPrivileges, $privilegedUsers);

        /** @var CommunityPrivilege $toAdd */
        foreach($comparison['add'] as $toAdd) {
            $toAdd->setCommunity($community);
            $this->em->persist($toAdd);
        }

        /** @var CommunityPrivilege $toRemove */
        foreach($comparison['remove'] as $toRemove) {
            $toRemove->setCommunity($community);
            $this->em->remove($toRemove);
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

        $privilegedUsers = $this->wrapCommunityPrivilegesToPrivilegedUsers($community, $communityPrivileges);

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
     * @deprecated
     */
    public function comparePrivilegedUsersAndCommunityPrivileges(Community $community, $privilegedUsers)
    {
        $communityPrivileges = $community->getPrivileges();

        // array of users involved in privilege modifications
        $usersInvolved = array();

        $indexedCommunityPrivileges = array();
        /** @var CommunityPrivilege $cp */
        foreach ($communityPrivileges as $cp) {
            $key = $cp->getUser()->getId() . $cp->getPrivilege()->getCode();
            $indexedCommunityPrivileges[$key] = $cp;

            $usersInvolved[$cp->getUser()->getId()] = null;
        }

        /** @var PrivilegedUser $pu */
        foreach($privilegedUsers as $pu) {
            $usersInvolved[$pu->getUserId()] = $pu;
        }

        $communityPrivilegesToKeep = array();
        $communityPrivilegesToAdd = array();
        $communityPrivilegesToRemove = array();

        /** @var PrivilegedUser $pu */
        foreach ($usersInvolved as $userId => $pu) {

            $userPrivilegesMap = array(
                CommunityPrivilegeRepository::OWNER_CODE => $pu !== null && $pu->isOwner(),
                CommunityPrivilegeRepository::ADMIN_CODE => $pu !== null && $pu->isAdmin(),
                CommunityPrivilegeRepository::MODERATOR_CODE => $pu !== null && $pu->isModerator(),
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

    /**
     * Compare passed array of @link PrivilegedUser with passed array of @link CommunityPrivilege.
     * Return the comparison result:
     * array(
     *      'keep' => $communityPrivilegesToKeep,
     *      'add' => $communityPrivilegesToAdd,
     *      'remove' => $communityPrivilegesToRemove,
     * )
     *
     * @param array $communityPrivileges
     * @param array $privilegedUsers
     * @return array
     */
    public function comparePrivilegedUsersAndCommunityPrivilegesBis($communityPrivileges, $privilegedUsers) {

        // index CommunityPrivilege array
        $indexedCp = array();
        /** @var CommunityPrivilege $cp */
        foreach($communityPrivileges as $cp) {
            $indexedCp[serialize($cp)] = $cp;
        }

        $indexedPu = array();
        /** @var PrivilegedUser $pu */
        foreach($privilegedUsers as $pu) {
            $puWrappedToCps = $this->wrapPrivilegedUserToCommunityPrivileges($pu);

            $indexedPu = array_merge($indexedPu, $puWrappedToCps);
        }

        $cpToKepp = array_intersect_key($indexedCp, $indexedPu);
        $cpToAdd = array_diff_key($indexedPu, $indexedCp);
        $cpToRemove = array_diff_key($indexedCp, $indexedPu);

        return array(
            'keep' => $cpToKepp,
            'add' => $cpToAdd,
            'remove' => $cpToRemove,
        );
    }

    /**
     * Wrap a @link PrivilegedUser to an array of @link CommunityPrivilege
     * @param PrivilegedUser $privilegedUser
     * @return  array
     */
    public function wrapPrivilegedUserToCommunityPrivileges(PrivilegedUser $privilegedUser) {
        $communityPrivileges = array();

        /** @var User $user */
        $user = $this->em->getReference('MbyUserBundle:User', $privilegedUser->getUserId());
        /** @var Community $community */
        $community = $this->em->getReference('MbyCommunityBundle:Community', $privilegedUser->getCommunityId());

        if ($privilegedUser->isOwner()) {
            /** @var Privilege $ownerPrivilege */
            $ownerPrivilege = $this->em->getReference('MbyCommunityBundle:Privilege', PrivilegeRepository::OWNER_CODE);
            $ownerCp = CommunityPrivilege::build($user, $community, $ownerPrivilege);
            $communityPrivileges[serialize($ownerCp)] = $ownerCp;
        }

        if ($privilegedUser->isAdmin()) {
            /** @var Privilege $adminPrivilege */
            $adminPrivilege = $this->em->getReference('MbyCommunityBundle:Privilege', PrivilegeRepository::ADMIN_CODE);
            $adminCp = CommunityPrivilege::build($user, $community, $adminPrivilege);
            $communityPrivileges[serialize($adminCp)] = $adminCp;
        }

        if ($privilegedUser->isModerator()) {
            /** @var Privilege $moderatorPrivilege */
            $moderatorPrivilege = $this->em->getReference('MbyCommunityBundle:Privilege', PrivilegeRepository::MODERATOR_CODE);
            $moderatorCp = CommunityPrivilege::build($user, $community, $moderatorPrivilege);
            $communityPrivileges[serialize($moderatorCp)] = $moderatorCp;
        }

        return $communityPrivileges;
    }

    /**
     * @param Community $community
     * @param $communityPrivileges
     * @return array
     */
    public function wrapCommunityPrivilegesToPrivilegedUsers(Community $community, $communityPrivileges) {
        $privilegedUsers = array();

        /** @var CommunityPrivilege $communityPrivilege */
        foreach ($communityPrivileges as $communityPrivilege) {
            $user = $communityPrivilege->getUser();
            $userId = $user->getId();
            $privilege = $communityPrivilege->getPrivilege();

            if (! isset($privilegedUsers[$userId])) {
                $pu = new PrivilegedUser();
                $pu->setCommunity($community);
                $pu->setCommunityId($community->getId());
                $pu->setUser($user);
                $pu->setUserId($user->getId());
                $pu->setLabel($user->getUsername());
            } else {
                $pu = $privilegedUsers[$userId];
            }

            if (PrivilegeRepository::OWNER_CODE === $privilege->getCode()) {
                $pu->setOwner(true);
            } else if (PrivilegeRepository::ADMIN_CODE === $privilege->getCode()) {
                $pu->setAdmin(true);
            } else if (PrivilegeRepository::MODERATOR_CODE === $privilege->getCode()) {
                $pu->setModerator(true);
            }

            $privilegedUsers[$user->getId()] = $pu;
        }
        return $privilegedUsers;
    }
}
