<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Responsibility;
use Mby\CommunityBundle\Entity\ResponsibilityRepository;

class LoadResponsibilityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $president = new Responsibility();
        $president->setCode('president');
        $president->setName('president');
        $president->setRank(0);
        $manager->persist($president);

        $vice_president = new Responsibility();
        $vice_president->setCode('vice_president');
        $vice_president->setName('vice_president');
        $vice_president->setRank(1);
        $manager->persist($vice_president);

        $treasurer = new Responsibility();
        $treasurer->setCode('treasurer');
        $treasurer->setName('treasurer');
        $treasurer->setRank(2);
        $manager->persist($treasurer);

        $secretary = new Responsibility();
        $secretary->setCode('secretary');
        $secretary->setName('secretary');
        $secretary->setRank(3);
        $manager->persist($secretary);

        $member = new Responsibility();
        $member->setCode(ResponsibilityRepository::MEMBER_CODE);
        $member->setName('member');
        $member->setRank(4);
        $manager->persist($member);
        
        $applicant = new Responsibility();
        $applicant->setCode(ResponsibilityRepository::APPLICANT_CODE);
        $applicant->setName('applicant');
        $applicant->setRank(5);
        $manager->persist($applicant);

        $manager->flush();

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