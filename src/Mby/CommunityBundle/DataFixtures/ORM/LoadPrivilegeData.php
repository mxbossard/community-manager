<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Privilege;

class LoadPrivilegeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $owner = new Privilege();
        $owner->setCode('owner');
        $owner->setRank(1);
        $manager->persist($owner);

        $admin = new Privilege();
        $admin->setCode('admin');
        $admin->setRank(2);
        $manager->persist($admin);

        $moderator = new Privilege();
        $moderator->setCode('moderator');
        $moderator->setRank(3);
        $manager->persist($moderator);

        $manager->flush();

        $this->addReference('priv-owner', $owner);
        $this->addReference('priv-admin', $admin);
        $this->addReference('priv-moderator', $moderator);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}