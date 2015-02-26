<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mby\CommunityBundle\Entity\Membership;

class LoadMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $today = new \DateTime('now');

        $ms_A_1_1 = new Membership();
        $ms_A_1_1->setUser($this->getReference('user-userA'));
        $ms_A_1_1->setSeason($this->getReference('season-com1-1'));
        $ms_A_1_1->setApplicationDate($today);
        $ms_A_1_1->addResponsibility($this->getReference('resp-member'));
        $ms_A_1_1->addResponsibility($this->getReference('resp-president'));
        $manager->persist($ms_A_1_1);

        $ms_A_1_2 = new Membership();
        $ms_A_1_2->setUser($this->getReference('user-userA'));
        $ms_A_1_2->setSeason($this->getReference('season-com1-2'));
        $ms_A_1_2->setApplicationDate($today);
        $ms_A_1_2->addResponsibility($this->getReference('resp-member'));
        $ms_A_1_2->addResponsibility($this->getReference('resp-president'));
        $manager->persist($ms_A_1_2);

        $ms_B_1_1 = new Membership();
        $ms_B_1_1->setUser($this->getReference('user-userB'));
        $ms_B_1_1->setSeason($this->getReference('season-com1-1'));
        $ms_B_1_1->setApplicationDate($today);
        $ms_B_1_1->addResponsibility($this->getReference('resp-member'));
        $manager->persist($ms_B_1_1);

        $ms_B_1_2 = new Membership();
        $ms_B_1_2->setUser($this->getReference('user-userB'));
        $ms_B_1_2->setSeason($this->getReference('season-com1-2'));
        $ms_B_1_2->setApplicationDate($today);
        $ms_B_1_2->addResponsibility($this->getReference('resp-applicant'));
        $manager->persist($ms_B_1_2);

        $ms_C_1_2 = new Membership();
        $ms_C_1_2->setUser($this->getReference('user-userC'));
        $ms_C_1_2->setSeason($this->getReference('season-com1-2'));
        $ms_C_1_2->setApplicationDate($today);
        $ms_C_1_2->addResponsibility($this->getReference('resp-applicant'));
        $manager->persist($ms_C_1_2);

        $manager->flush();

        $this->addReference('membership_A_1_1', $ms_A_1_1);
        $this->addReference('membership_A_1_2', $ms_A_1_2);
        $this->addReference('membership_B_1_1', $ms_B_1_1);
        $this->addReference('membership_B_1_2', $ms_B_1_2);
        $this->addReference('membership_C_1_2', $ms_C_1_2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }

}