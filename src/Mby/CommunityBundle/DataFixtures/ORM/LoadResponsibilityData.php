<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Responsibility;

class LoadResponsibilityData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $applicant = new Responsibility();
        $applicant->setName('applicant');
        $manager->persist($applicant);

        $admin = new Responsibility();
        $admin->setName('admin');
        $manager->persist($admin);

        $moderator = new Responsibility();
        $moderator->setName('moderator');
        $manager->persist($moderator);

        $president = new Responsibility();
        $president->setName('president');
        $manager->persist($president);

        $secretary = new Responsibility();
        $secretary->setName('secretary');
        $manager->persist($secretary);

        $manager->flush();

        $this->addReference('resp-applicant', $applicant);
        $this->addReference('resp-admin', $admin);
        $this->addReference('resp-moderator', $moderator);
        $this->addReference('resp-president', $president);
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