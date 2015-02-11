<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\CommunityPrivilege;

class LoadCommunityPrivilegeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $priv1_1 = new CommunityPrivilege();
        $priv1_1->setCommunity($this->getReference('community-com1'));
        $priv1_1->setUser($this->getReference('user-userA'));
        $priv1_1->setPrivilege($this->getReference('priv-owner'));
        $manager->persist($priv1_1);

        $priv1_2 = new CommunityPrivilege();
        $priv1_2->setCommunity($this->getReference('community-com1'));
        $priv1_2->setUser($this->getReference('user-userA'));
        $priv1_2->setPrivilege($this->getReference('priv-admin'));
        $manager->persist($priv1_2);

        $priv2_1 = new CommunityPrivilege();
        $priv2_1->setCommunity($this->getReference('community-com2'));
        $priv2_1->setUser($this->getReference('user-userB'));
        $priv2_1->setPrivilege($this->getReference('priv-owner'));
        $manager->persist($priv2_1);

        $priv2_2 = new CommunityPrivilege();
        $priv2_2->setCommunity($this->getReference('community-com2'));
        $priv2_2->setUser($this->getReference('user-userB'));
        $priv2_2->setPrivilege($this->getReference('priv-moderator'));
        $manager->persist($priv2_2);

        $priv3_1 = new CommunityPrivilege();
        $priv3_1->setCommunity($this->getReference('community-com3'));
        $priv3_1->setUser($this->getReference('user-userC'));
        $priv3_1->setPrivilege($this->getReference('priv-owner'));
        $manager->persist($priv3_1);

        $manager->flush();

        $this->addReference('comm_priv-1_1', $priv1_1);
        $this->addReference('comm_priv-1_2', $priv1_2);
        $this->addReference('comm_priv-2_1', $priv2_1);
        $this->addReference('comm_priv-2_2', $priv2_2);
        $this->addReference('comm_priv-3_1', $priv3_1);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}