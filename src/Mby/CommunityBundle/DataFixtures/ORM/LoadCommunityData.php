<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Community;

class LoadCommunityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $com1 = new Community();
        $com1->setName('Le coffre à jeux');
        $manager->persist($com1);

        $com2 = new Community();
        $com2->setName('Ufolep centre');
        $manager->persist($com2);

        $com3 = new Community();
        $com3->setName('Info Labo');
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
        return 1; // the order in which fixtures will be loaded
    }
}