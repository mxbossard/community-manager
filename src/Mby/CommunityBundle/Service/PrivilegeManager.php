<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Mby\CommunityBundle\Entity\CommunityPrivilege;
use Mby\CommunityBundle\Entity\CommunityPrivilegeRepository;
use Mby\CommunityBundle\Model\PrivilegedUser;
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

    /**
     * @var CommunityPrivilegeRepository
     */
    protected $comPrivRepo;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->privilegeRepo = $this->em->getRepository('MbyCommunityBundle:Privilege');
        $this->comPrivRepo = $this->em->getRepository('MbyCommunityBundle:CommunityPrivilege');
    }

    public function isOwner(User $user, Community $community)
    {
        //$privilege = $this->loadPrivilege(PrivilegeRepository::OWNER_CODE);

        return $this->isPrivileged($user, $community, PrivilegeRepository::OWNER_CODE);
    }

    public function isAdministrator(User $user, Community $community)
    {
        //$privilege = $this->loadPrivilege(PrivilegeRepository::ADMIN_CODE);

        return $this->isPrivileged($user, $community, PrivilegeRepository::ADMIN_CODE);
    }

    public function isModerator(User $user, Community $community)
    {
        //$privilege = $this->loadPrivilege(PrivilegeRepository::MODERATOR_CODE);

        return $this->isPrivileged($user, $community, PrivilegeRepository::MODERATOR_CODE);
    }

    public function grantOwnerPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to grant owner privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::OWNER_CODE);
        $target->addPrivilege($privilege);
    }

    public function grantAdministratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community) && ! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner or administrator to grant administrator privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::ADMIN_CODE);
        $target->addPrivilege($privilege);
    }

    public function grantModeratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community)) {
            throw new Exception("user must be administrator to grant moderator privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::MODERATOR_CODE);
        $target->addPrivilege($privilege);
    }

    public function revokeOwnerPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to revoke owner privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::OWNER_CODE);
        $target->removePrivilege($privilege);
    }

    public function revokeAdministratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isOwner($user, $community)) {
            throw new Exception("user must be owner to revoke administrator privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::ADMIN_CODE);
        $target->removePrivilege($privilege);
    }

    public function revokeModeratorPrivilege(User $user, User $target, Community $community)
    {
        if (! $this->isAdministrator($user, $community)) {
            throw new Exception("user must be administrator to revoke moderator privilege");
        }

        $privilege = $this->loadPrivilege(PrivilegeRepository::MODERATOR_CODE);
        $target->removePrivilege($privilege);
    }

    /**
     * @param User $user
     * @param Community $community
     * @param $privilegeAdmin
     * @return bool
     */
    protected function isPrivileged(User $user, Community $community, $privilegeCode)
    {
        $privileges = $user->getPrivileges();
        /** @var CommunityPrivilege $p */
        foreach ($privileges as $p) {
            if ($p->getCommunity()->getId() === $community->getId()
                    && $p->getPrivilege()->getCode() === $privilegeCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Privilege
     */
    protected function loadPrivilege($code)
    {
        $privilege = $this->em->getReference("MbyCommunityBundle:Privilege", $code);
        return $privilege;
    }

}
