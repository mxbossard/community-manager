<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\Privilege;
use Mby\CommunityBundle\Entity\PrivilegeRepository;

class PrivilegeManager
{

    const SERVICE_NAME = 'privilege_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var PrivilegeRepository
     */
    protected $privilegeRepo;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->privilegeRepo = $this->em->getRepository('MbyCommunityBundle:Privilege');
    }

    public function isOwner(User $user, Community $community)
    {
        $privilegeOwner = $this->privilegeRepo->find(PrivilegeRepository::OWNER_CODE);

        return $this->findPrivilege($user, $community, $privilegeOwner);
    }

    public function isAdministrator(User $user, Community $community)
    {
        $privilegeAdmin = $this->privilegeRepo->find(PrivilegeRepository::ADMIN_CODE);

        return $this->findPrivilege($user, $community, $privilegeAdmin);
    }

    public function isModerator(User $user, Community $community)
    {
        $privilegeModerator = $this->privilegeRepo->find(PrivilegeRepository::MODERATOR_CODE);

        return $this->findPrivilege($user, $community, $privilegeModerator);
    }

    public function grantOwnerPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to grant owner privilege");
        }

        $privilege= $this->privilegeRepo->find(PrivilegeRepository::OWNER_CODE);
        $target->addPrivilege($privilege);
    }

    public function grantAdministratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community) && ! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner or administrator to grant administrator privilege");
        }

        $privilege= $this->privilegeRepo->find(PrivilegeRepository::ADMIN_CODE);
        $target->addPrivilege($privilege);
    }

    public function grantModeratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community)) {
            throw new Exception("user must be administrator to grant moderator privilege");
        }

        $privilege = $this->privilegeRepo->find(PrivilegeRepository::MODERATOR_CODE);
        $target->addPrivilege($privilege);
    }

    public function revokeOwnerPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to revoke owner privilege");
        }

        $privilege= $this->privilegeRepo->find(PrivilegeRepository::OWNER_CODE);
        $target->removePrivilege($privilege);
    }

    public function revokeAdministratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to revoke administrator privilege");
        }

        $privilege= $this->privilegeRepo->find(PrivilegeRepository::ADMIN_CODE);
        $target->removePrivilege($privilege);
    }

    public function revokeModeratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community)) {
            throw new Exception("user must be administrator to revoke moderator privilege");
        }

        $privilege = $this->privilegeRepo->find(PrivilegeRepository::MODERATOR_CODE);
        $target->removePrivilege($privilege);
    }

    /**
     * @param User $user
     * @param Community $community
     * @param $privilegeAdmin
     * @return bool
     */
    protected function findPrivilege(User $user, Community $community, $privilegeAdmin)
    {
        foreach ($user->getPrivileges() as $privilege) {
            if ($privilege->getCommunity()->getId() === $community->getId()
                && $privilege->getId() === $privilegeAdmin->getId()
            ) {
                return true;
            }
        }

        return false;
    }

}
