<?php

namespace Mby\CommunityBundle\Controller;

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
        $privilegeAdmin = $this->privilegeRepo->find(PrivilegeRepository::OWNER_CODE);

        return $this->findPrivilege($user, $community, $privilegeAdmin);
    }

    public function isAdministrator(User $user, Community $community)
    {
        $privilegeAdmin = $this->privilegeRepo->find(PrivilegeRepository::ADMIN_CODE);

        return $this->findPrivilege($user, $community, $privilegeAdmin);
    }

    public function isModerator(User $user, Community $community)
    {
        $privilegeAdmin = $this->privilegeRepo->find(PrivilegeRepository::MODERATOR_CODE);

        return $this->findPrivilege($user, $community, $privilegeAdmin);
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
            if ($privilege->getCommunity()->getId() === $community . getId()
                && $privilege->getId() === $privilegeAdmin->getId()
            ) {
                return true;
            }
        }

        return false;
    }

}
