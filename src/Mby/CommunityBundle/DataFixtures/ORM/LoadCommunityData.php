<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Community;

class LoadCommunityData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $com1 = new Community();
        $com1->setName('Le coffre à jeux');
        $com1->setOwner($this->getReference('user-userA'));
        $manager->persist($com1);

        $com2 = new Community();
        $com2->setName('Ufolep centre');
        $com2->setOwner($this->getReference('user-userB'));
        $manager->persist($com2);

        $com3 = new Community();
        $com3->setName('Info Labo');
        $com3->setOwner($this->getReference('user-userC'));
        $manager->persist($com3);

        $manager->flush();

        $this->addReference('community-com1', $com1);
        $this->addReference('community-com2', $com2);
        $this->addReference('community-com3', $com3);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}