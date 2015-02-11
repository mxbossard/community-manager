<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Season;

class LoadSeasonData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $seas1 = new Season();
        $seas1->setName('2013-2014');
        $seas1->setFromDate(new \DateTime('2013-09-01'));
        $seas1->setToDate(new \DateTime('2014-08-31'));
        $seas1->setCommunity($this->getReference('community-com1'));
        $manager->persist($seas1);

        $seas2 = new Season();
        $seas2->setName('2014-2015');
        $seas2->setFromDate(new \DateTime('2014-09-01'));
        $seas2->setCommunity($this->getReference('community-com1'));
        $manager->persist($seas2);

        $seas3 = new Season();
        $seas3->setName('2013-2014');
        $seas3->setFromDate(new \DateTime('2013-09-01'));
        $seas3->setToDate(new \DateTime('2014-08-31'));
        $seas3->setCommunity($this->getReference('community-com2'));
        $manager->persist($seas3);

        $seas4 = new Season();
        $seas4->setName('2014-2015');
        $seas4->setFromDate(new \DateTime('2014-09-01'));
        $seas4->setCommunity($this->getReference('community-com2'));
        $manager->persist($seas4);

        $seas5 = new Season();
        $seas5->setName('2014-2015');
        $seas5->setFromDate(new \DateTime('2014-09-01'));
        $seas5->setCommunity($this->getReference('community-com3'));
        $manager->persist($seas5);

        $manager->flush();

        $this->addReference('season-com1-1', $seas1);
        $this->addReference('season-com1-2', $seas2);
        $this->addReference('season-com2-1', $seas3);
        $this->addReference('season-com2-2', $seas4);
        $this->addReference('season-com3-1', $seas5);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}