<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Responsibility;

class LoadResponsibilityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $president = new Responsibility();
        $president->setName('president');
        $manager->persist($president);

        $vice_president = new Responsibility();
        $vice_president->setName('vice_president');
        $manager->persist($vice_president);

        $treasurer = new Responsibility();
        $treasurer->setName('treasurer');
        $manager->persist($treasurer);

        $secretary = new Responsibility();
        $secretary->setName('secretary');
        $manager->persist($secretary);

        $member = new Responsibility();
        $member->setName('member');
        $manager->persist($member);
        
        $applicant = new Responsibility();
        $applicant->setName('applicant');
        $manager->persist($applicant);

        $follower = new Responsibility();
        $follower->setName('follower');
        $manager->persist($follower);

        $manager->flush();

        $this->addReference('resp-follower', $follower);
        $this->addReference('resp-applicant', $applicant);
        $this->addReference('resp-member', $member);
        $this->addReference('resp-president', $president);
        $this->addReference('resp-vice_president', $vice_president);
        $this->addReference('resp-treasurer', $treasurer);
        $this->addReference('resp-secretary', $secretary);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}