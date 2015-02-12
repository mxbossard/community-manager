<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\PrivilegeRepository;

class CommunityManager
{

    const SERVICE_NAME = 'community_manager';

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
	 * Create a new community.
	 *
     */
    protected function create(User $user, Community $community)
    {
        $privilegeRepo = $this->em->getRepository('MbyCommunityBundle:Privilege');
        $ownerPrivilege = $privilegeRepo->find(PrivilegeRepository::OWNER_CODE);
        $adminPrivilege = $privilegeRepo->find(PrivilegeRepository::ADMIN_CODE);

        $ownerRel = new CommunityPrivilege();
        $ownerRel->setCommunity($community);
        $ownerRel->setUser($user);
        $ownerRel->setPrivilege($ownerPrivilege);

        $adminRel = new CommunityPrivilege();
        $adminRel->setCommunity($community);
        $adminRel->setUser($user);
        $adminRel->setPrivilege($adminPrivilege);

        $this->em->persist($community);
        $this->em->persist($ownerRel);
        $this->em->persist($adminRel);

        $this->em->flush();
    }

}
